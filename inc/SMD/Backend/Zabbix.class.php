<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2012-2016 Rubén Domínguez nuxsmin@cygnux.org
 *
 * This file is part of sysMonDash.
 *
 * sysMonDash is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * sysMonDash is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with sysMonDash.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace SMD\Backend;

use Exts\Zabbix\ZabbixApiLoader;
use SMD\Backend\Event\Downtime;
use SMD\Backend\Event\Trigger;
use SMD\Core\Config;
use SMD\Core\ConfigBackendZabbix;
use SMD\Util\Util;

/**
 * Class Zabbix para la gestión de eventos de Zabbix
 *
 * @package SMD\Backend
 */
class Zabbix extends Backend implements BackendInterface
{
    /** @var \Exts\Zabbix\V225\ZabbixApi|\Exts\Zabbix\V245\ZabbixApi */
    private $Zabbix = null;
    /**
     * URL de la API de Zabbix
     *
     * @var string
     */
    private $url = '';
    /**
     * Usuario de conexión
     *
     * @var string
     */
    private $user = '';
    /**
     * Clave de conexión
     *
     * @var string
     */
    private $pass = '';
    /**
     * Versión de la API
     *
     * @var int
     */
    private $version = 0;
    /**
     * Array con los hosts en mantenimiento
     *
     * @var array
     */
    private $hostsMaintenance = array();
    /**
     * Array con las paradas programadas
     *
     * @var array
     */
    private $downtimes = array();

    /**
     * Zabbix constructor.
     *
     * @param ConfigBackendZabbix $backend
     * @throws \Exception
     */
    public function __construct(ConfigBackendZabbix $backend)
    {
        $this->backend = $backend;
        $this->version = $backend->getVersion();
        $this->url = $backend->getUrl();
        $this->user = $backend->getUser();
        $this->pass = $backend->getPass();

        $this->connect();
    }

    /**
     * Conectar con la API de Zabbix
     *
     * @throws \Exception
     */
    private function connect()
    {
        try {
            $this->Zabbix = ZabbixApiLoader::getAPI($this->version);
            $this->Zabbix->setApiUrl($this->url);
            $this->Zabbix->userLogin(array('user' => $this->user, 'password' => $this->pass));
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getHostsProblems()
    {
        return $this->getProblems();
    }

    /**
     * Devuelve el array de eventos
     *
     * @return array
     */
    public function getProblems()
    {
        return $this->getTriggersError();
    }

    /**
     * Obtener los eventos generados
     *
     * @return array|Event\EventInterface[]
     */
    private function getTriggersError()
    {
        $this->getScheduledDowntimes();

        $params = array(
            'groupids' => null,
            'hostids' => null,
            'monitored' => true,
            //'maintenance'   => false,
            'filter' => array('value' => 1),
            'skipDependent' => true,
            'expandComment ' => true,
            'expandDescription' => true,
            'expandExpression' => true,
            'output' => array('triggerid', 'state', 'status', 'error', 'url', 'expression', 'description', 'priority', 'lastchange', 'value'),
            'selectHosts' => array('hostid', 'name', 'maintenance_status'),
            'selectLastEvent' => array('eventid', 'acknowledged', 'objectid', 'clock', 'ns', 'value'),
            'selectItems' => array('name'),
            'sortfield' => array('lastchange'),
            'sortorder' => array('DESC'),
            'limit' => Config::getConfig()->getMaxDisplayItems()
        );

        $triggers = $this->Zabbix->triggerGet($params);
        $events = array();

        foreach ($triggers as $event) {
            foreach ($event->hosts as $host) {
                $Event = new Trigger();
                $Event->setState($event->priority);
                $Event->setStateType($event->state);
                $Event->setAcknowledged($event->lastEvent->acknowledged);
                $Event->setHostDisplayName($host->name);
                $Event->setDisplayName($this->getItemsNames($event->items));
                $Event->setCheckCommand($event->triggerid);
                $Event->setPluginOutput($event->description);
                $Event->setLastCheck($event->lastEvent->clock);
                $Event->setLastHardStateChange($event->lastEvent->clock);
                $Event->setLastHardState($event->lastEvent->clock);
                $Event->setActiveChecksEnabled($event->status);
                $Event->setScheduledDowntimeDepth($host->maintenance_status);
                $Event->setCurrentAttempt($event->value);
                $Event->setNotificationsEnabled(true);
                $Event->setBackendAlias($this->backend->getAlias());
                $Event->setBackendUrl($this->backend->getUrl());

                $events[] = $Event;
            }
        }

        return $events;
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array
     */
    public function getScheduledDowntimes()
    {
        // Reutilizar la caché
        if (count($this->downtimes) > 0) {
            return $this->downtimes;
        }

        $params = array(
            'output' => array('active_since', 'active_till', 'description'),
            'selectHosts' => 'extend',
            'selectTimeperiods' => 'extend'
        );

        $maintenances = $this->Zabbix->maintenanceGet($params);

        foreach ($maintenances as $maintenance) {
            $this->setHostsInMaintenance($maintenance->hosts);

            if (time() <= $maintenance->active_till) {
                $period = $this->getTimePeriod($maintenance->timeperiods);

                $Downtime = new Downtime();
                $Downtime->setAuthor('Zabbix');
                $Downtime->setComment($maintenance->description);
                $Downtime->setHostName($this->getHostsForMaintenance($maintenance->maintenanceid));
                $Downtime->setIsService(false);
                $Downtime->setServiceDisplayName('-');
                $Downtime->setStartTime($period['start']);
                $Downtime->setEndTime($period['end']);
                $Downtime->setBackendAlias($this->backend->getAlias());

                $this->downtimes[] = $Downtime;
            }
        }

        return $this->downtimes;
    }

    /**
     * Obtener los hosts en mantenimiento
     *
     * @param array $hosts
     * @return array
     */
    private function setHostsInMaintenance(array $hosts)
    {
        foreach ($hosts as $host) {
            $this->hostsMaintenance[$host->hostid] = array(
                'host' => $host->host,
                'maintenanceid' => (int)$host->maintenanceid
            );
        }
    }

    /**
     * Obtener el periodo de tiempo más cercano al tiempo actual
     *
     * @param array $timePeriods
     * @return int
     */
    private function getTimePeriod(array $timePeriods)
    {
        $result = array('start' => 0, 'end' => 0);

        foreach ($timePeriods as $timePeriod) {
            $end = $timePeriod->start_date + $timePeriod->period;

            if (time() <= $end) {
                $result[] = array('start' => $timePeriod->start_date, 'end' => $end);
            }
        }

        Util::arraySortByKey($result, 'end');

        return (count($result) > 0) ? $result[0] : $result;
    }

    /**
     * Obtener los hosts de un mantenimiento
     *
     * @param $maintenanceId
     * @return string
     */
    private function getHostsForMaintenance($maintenanceId)
    {
        $hosts = array();

        foreach ($this->hostsMaintenance as $host) {
            if ((int)$host['maintenanceid'] === (int)$maintenanceId) {
                $hosts[] = $host['host'];
            }
        }

        return (count($hosts) > 0) ? $hosts : '';
    }

    /**
     * Devolver el nombre de los items
     *
     * @param array $items
     * @return string
     */
    protected static function getItemsNames(array $items)
    {
        $names = '';

        foreach ($items as $item) {
            $names .= $item->name . ';';
        }

        return trim($names, ';');
    }

    /**
     * Devuelve los eventos de los servicios
     *
     * @return array|bool
     */
    public function getServicesProblems()
    {
        return array();
    }

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     */
    public function getScheduledDowntimesGroupped()
    {
        return $this->getScheduledDowntimes();
    }

    /**
     * @param ConfigBackendZabbix $backend
     */
    public function setBackend(ConfigBackendZabbix $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Obtener los triggers en estado OK
     *
     * @return array|Event\EventInterface[]
     */
    protected function getTriggersOk()
    {
        $this->getScheduledDowntimes();

        $params = array(
            'groupids' => null,
            'hostids' => null,
            'monitored' => true,
            //'maintenance'   => false,
            'filter' => array('value' => 0, 'lastChangeSince' => time() - (Config::getConfig()->getNewItemTime() / 2)),
            'skipDependent' => true,
            'expandDescription' => true,
            'output' => array('triggerid', 'state', 'status', 'error', 'url', 'expression', 'description', 'priority', 'lastchange', 'value'),
            'selectHosts' => array('hostid', 'name', 'maintenance_status', 'errors_from'),
            'selectLastEvent' => array('eventid', 'acknowledged', 'objectid', 'clock', 'ns', 'value'),
            'sortfield' => array('lastchange'),
            'sortorder' => array('DESC'),
            'limit' => Config::getConfig()->getMaxDisplayItems()
        );

        $triggers = $this->Zabbix->triggerGet($params);
        $events = array();

        foreach ($triggers as $event) {
            foreach ($event->hosts as $host) {
                $Event = new Trigger();
                $Event->setState($host->value);
                $Event->setStateType($event->state);
                $Event->setAcknowledged(intval($event->lastEvent->acknowledged));
                $Event->setHostDisplayName($host->name);
                $Event->setDisplayName($host->name);
                $Event->setCheckCommand($event->triggerid);
                $Event->setPluginOutput($event->description);
                $Event->setLastCheck($event->lastchange);
                $Event->setLastTimeUnreachable($host->errors_from);
                $Event->setLastHardStateChange($event->lastchange);
                $Event->setLastHardState($event->lastchange);
                $Event->setActiveChecksEnabled($event->status);
                $Event->setScheduledDowntimeDepth($host->maintenance_status);
                $Event->setCurrentAttempt($event->value);
                $Event->setNotificationsEnabled(true);
                $Event->setBackendAlias($this->backend->getAlias());
                $Event->setBackendUrl($this->backend->getUrl());

                $events[] = $Event;
            }
        }

        return $events;
    }
}