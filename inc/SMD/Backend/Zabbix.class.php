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

/**
 * Class Zabbix para la gestión de eventos de Zabbix
 *
 * @package SMD\Backend
 */
class Zabbix extends Backend implements BackendInterface
{
    /** @var \Exts\Zabbix\V222\ZabbixApi|\Exts\Zabbix\V223\ZabbixApi|\Exts\Zabbix\V242\ZabbixApi|\Exts\Zabbix\V243\ZabbixApi */
    private $_Zabbix = null;
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

        $this->connect();
    }

    /**
     * Conectar con la API de Zabbix
     *
     * @throws \Exception
     */
    private function connect()
    {
        $this->_Zabbix = ZabbixApiLoader::getAPI($this->version);
        $this->_Zabbix->setApiUrl($this->url);
        $this->_Zabbix->userLogin(['user' => $this->user, 'password' => $this->pass]);
    }

    /**
     * Devuelve el array de eventos
     *
     * @return array
     */
    public function getProblems()
    {
        return $this->getEvents();
    }

    /**
     * @return array
     */
    public function getHostsProblems()
    {
        return $this->getEvents();
    }

    /**
     * Obtener los eventos generados
     *
     * @return array
     */
    private function getEvents()
    {
        $params = [
            'output' => ['acknowledged', 'object', 'objectid', 'clock'],
            'value' => 1,
            'sortfield' => 'clock',
            'sortorder' => 'DESC'
        ];

        $result = [];
        $events = $this->_Zabbix->eventGet($params);

        foreach ($events as $event) {
            $trigger = $this->getTrigger($event->objectid);

            $result[] = [
                'state' => $this->getTriggerState($trigger->priority),
                'state_type' => $trigger->value,
                'acknowledged' => $event->acknowledged,
                'host_display_name' => $trigger->hostname,
                'display_name' => $trigger->host,
                'check_command' => $trigger->triggerid,
                'plugin_output' => $trigger->description,
                'last_check' => $trigger->lastchange,
                'last_time_up' => $trigger->lastchange,
                'last_time_ok' => $trigger->lastchange,
                'last_time_unreachable' => 0,
                'last_hard_state_change' => $event->clock,
                'last_hard_state' => $event->clock,
                'last_time_down' => 0,
                'active_checks_enabled' => $trigger->status,
                'scheduled_downtime_depth' => 0,
                'current_attempt' => 1,
                'max_attempts' => 0,
                'is_flapping' => 0,
                'notifications_enabled' => 1,
                'object' => $event->object,
                'clock' => $event->clock,
            ];
        }

        return $result;
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
            'output' => ['triggerid', 'description', 'priority', 'status', 'url', 'state', 'lastchange']
        ];

        $trigger = $this->_Zabbix->triggerGet($params);
        return $trigger[0];
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
     * Devuelve los eventos de los servicios
     *
     * @return array|bool
     */
    public function getServicesProblems()
    {
        // TODO: Implement getServicesProblems() method.
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array|bool
     */
    public function getScheduledDowntimes()
    {
        // TODO: Implement getScheduledDowntimes() method.
    }

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     */
    public function getScheduledDowntimesGroupped()
    {
        // TODO: Implement getScheduledDowntimesGroupped() method.
    }
}