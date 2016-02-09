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
use SMD\Backend\Event\EventInterface;
use SMD\Backend\Event\Trigger;
use SMD\Core\Config;
use SMD\Util\Util;

/**
 * Class Zabbix para la gestión de eventos de Zabbix
 *
 * @package SMD\Backend
 */
class Zabbix extends Backend implements BackendInterface
{
    /** @var \Exts\Zabbix\V223\ZabbixApi|\Exts\Zabbix\V243\ZabbixApi */
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
     * Array con los eventos actuales
     *
     * @var EventInterface[]
     */
    private $events;
    /**
     * Array con las paradas programadas
     *
     * @var array
     */
    private $downtimes = array();

    /**
     * Zabbix constructor.
     *
     * @param $version int Versión de la API
     * @param $url string URL de la API
     * @param $user string Usuario de conexión
     * @param $pass string Clave de conexión
     * @throws \Exception
     */
    public function __construct($version, $url, $user, $pass)
    {
        if (empty($version)
            || empty($url)
            || empty($user)
            || empty($pass)
        ) {
            throw new \Exception('Argumentos inválidos');
        }

        $this->version = intval($version);
        $this->url = $url;
        $this->user = $user;
        $this->pass = $pass;
        $this->events = [];

        $this->connect();
    }

    /**
     * Conectar con la API de Zabbix
     *
     * @throws \Exception
     */
    private function connect()
    {
        $this->Zabbix = ZabbixApiLoader::getAPI($this->version);
        $this->Zabbix->setApiUrl($this->url);
        $this->Zabbix->userLogin(['user' => $this->user, 'password' => $this->pass]);
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
        return $this->retrieveEvents();
    }

    /**
     * Obtener los eventos generados
     *
     * @return array
     */
    private function retrieveEvents()
    {
        $this->getScheduledDowntimes();

        $params = [
            'groupids' => null,
            'hostids' => null,
            'monitored' => true,
            //'maintenance'   => false,
            'filter' => ['value' => 1],
            'skipDependent' => true,
            'expandDescription' => true,
            'output' => ['triggerid', 'state', 'status', 'error', 'url', 'expression', 'description', 'priority', 'lastchange', 'value'],
            'selectHosts' => ['hostid', 'name', 'maintenance_status'],
            'selectLastEvent' => ['eventid', 'acknowledged', 'objectid', 'clock', 'ns', 'value'],
            'sortfield' => ['lastchange'],
            'sortorder' => ['DESC'],
            'limit' => Config::getConfig()->getMaxDisplayItems()
        ];

        $eventsError = $this->Zabbix->triggerGet($params);

        foreach ($eventsError as $event) {
            foreach ($event->hosts as $host) {
                $Event = new Trigger();
                $Event->setState($this->getTriggerState($event->priority));
                $Event->setStateType($event->state);
                $Event->setAcknowledged($event->lastEvent->acknowledged);
                $Event->setHostDisplayName($host->name);
                $Event->setDisplayName($host->name);
                $Event->setCheckCommand($event->triggerid);
                $Event->setPluginOutput($event->description);
                $Event->setLastCheck($event->lastEvent->clock);
                $Event->setLastHardStateChange($event->lastEvent->clock);
                $Event->setLastHardState($event->lastEvent->clock);
                $Event->setActiveChecksEnabled($event->status);
                $Event->setScheduledDowntimeDepth($host->maintenance_status);
                $Event->setCurrentAttempt($event->value);
                $Event->setNotificationsEnabled(true);

                $this->events[] = $Event;
            }
        }

        // Obtener los eventos que están OK
        $params['filter'] = ['value' => 0, 'lastChangeSince' => time() - (Config::getConfig()->getNewItemTime() / 2 )];

        $eventsOk = $this->Zabbix->triggerGet($params);

        foreach ($eventsOk as $event) {
            foreach ($event->hosts as $host) {
                $Event = new Trigger();
                $Event->setState($host->value);
                $Event->setStateType($event->state);
                $Event->setAcknowledged($event->lastEvent->acknowledged);
                $Event->setHostDisplayName($host->name);
                $Event->setDisplayName($host->name);
                $Event->setCheckCommand($event->triggerid);
                $Event->setPluginOutput($event->description);
                $Event->setLastCheck($event->lastchange);
                $Event->setLastHardStateChange($event->lastchange);
                $Event->setLastHardState($event->lastchange);
                $Event->setActiveChecksEnabled($event->status);
                $Event->setScheduledDowntimeDepth($host->maintenance_status);
                $Event->setCurrentAttempt($event->value);
                $Event->setNotificationsEnabled(true);

                $this->events[] = $Event;
            }
        }

        return $this->events;
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array
     */
    public function getScheduledDowntimes()
    {
        if (count($this->downtimes) > 0) {
            return $this->downtimes;
        }

        $params = [
            'output' => ['active_since', 'active_till', 'description'],
            'selectHosts' => 'extend',
            'selectTimeperiods' => 'extend'
        ];

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
            if ((int)$host->maintenance_status === 1) {
                $this->hostsMaintenance[$host->hostid] = [
                    'host' => $host->host,
                    'maintenanceid' => (int)$host->maintenanceid
                ];
            }
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
        $result = [];

        foreach ($timePeriods as $timePeriod) {
            $end = $timePeriod->start_date + $timePeriod->period;

            if (time() <= $end) {
                $result[] = ['start' => $timePeriod->start_date, 'end' => $end];
            }
        }

        Util::arraySortByKey($result, 'end');

        return $result[0];
    }

    /**
     * Obtener los hosts de un mantenimiento
     *
     * @param $maintenanceId
     * @return string
     */
    private function getHostsForMaintenance($maintenanceId)
    {
        $hosts = [];

        foreach ($this->hostsMaintenance as $host) {
            if ((int)$host['maintenanceid'] === (int)$maintenanceId) {
                $hosts[] = $host['host'];
            }
        }

        return (count($hosts) > 0) ? implode(',', $hosts) : '';
    }

    /**
     * Unificar el tipo de estado según prioridad del trigger
     *
     * @param $state int El tipo de estado
     * @return int
     */
    private function getTriggerState($state)
    {
        switch ($state) {
            case 0:
                return SERVICE_UNKNOWN;
            case 1:
            case 2:
            case 3:
                return SERVICE_WARNING;
            case 4:
            case 5:
                return SERVICE_CRITICAL;
            default:
                return SERVICE_UNKNOWN;
        }
    }

    /**
     * Comprobar si el host del objeto trigger está en mantenimiento
     *
     * @param array $hosts
     * @return int
     */
    private function checkHostMaintenance(array $hosts)
    {
        return ((int)$hosts[0]->maintenance_status === 1) ? 1 : 0;
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
     * Obtener los datos de un trigger
     *
     * @param $id int El Id del trigger
     * @return object
     */
    private function getTrigger($id)
    {
        $params = [
            'triggerids' => $id,
            'expandData' => 1,
            'expandDescription' => 1,
            'selectHosts' => 'extend',
            'output' => ['triggerid', 'description', 'priority', 'status', 'url', 'state', 'lastchange', 'value']
        ];

        $trigger = $this->Zabbix->triggerGet($params);
        return $trigger[0];
    }
}