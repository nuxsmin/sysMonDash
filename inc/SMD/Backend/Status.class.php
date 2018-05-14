<?php
/**
 * sysMonDash
 *
 * @author     nuxsmin
 * @link       https://github.com/nuxsmin/sysMonDash
 * @copyright  2012-2018 Rubén Domínguez nuxsmin@cygnux.org
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
 * along with sysMonDash. If not, see <http://www.gnu.org/licenses/gpl-3.0-standalone.html>.
 */

namespace SMD\Backend;

use SMD\Backend\Event\Host;
use SMD\Backend\Event\Service;
use SMD\Core\Config;
use SMD\Core\ConfigBackendStatus;
use SMD\Util\NagiosQL;

/**
 * Class Status para la obtención de datos desde el archivo status.dat
 * @package SMD\Backend
 */
class Status extends Backend implements BackendInterface
{
    /**
     * @var string
     */
    protected $fileData;
    /**
     * @var string
     */
    protected $path = '';

    /**
     * Status constructor.
     * @param ConfigBackendStatus $backend
     * @throws \Exception
     */
    public function __construct(ConfigBackendStatus $backend)
    {
        $this->backend = $backend;
        $this->path = $backend->getPath();

        $this->getStatusData();
    }

    /**
     * Obtener los datos de monitorización mediante el archivo status.dat
     */
    private function getStatusData()
    {
        // Archivo con información de estado de Icinga
        if (!$this->fileData = file_get_contents($this->path)) {
            throw new \Exception('Error al obtener los datos de monitorización');
        }

        if (Config::getConfig()->isUseNagiosQLInfo() && !isset($_SESSION['dash_hostinfo'])) {
            $_SESSION['dash_hostinfo'] = NagiosQL::getHostsDBInfo();
        }
    }

    /**
     * @return mixed
     */
    public function getScheduledDowntimes()
    {
        return array();
    }

    /**
     * @return mixed
     */
    public function getScheduledDowntimesGroupped()
    {
        return array();
    }

    /**
     * @return array
     */
    public function getProblems()
    {
        return array_merge($this->getHostsProblems(), $this->getServicesProblems());
    }

    /**
     * @return mixed
     */
    public function getHostsProblems()
    {
        // Obtenemos los bloques que corresponden al estado de los hosts y servicios
        preg_match_all('/hoststatus {.*}/isU', $this->fileData, $hostsData);

        $events = array();

        foreach ($this->getItemsArray($hostsData) as $event) {
            $Event = new Host();
            $Event->setAcknowledged($event['problem_has_been_acknowledged']);
            $Event->setActiveChecksEnabled($event['active_checks_enabled']);
            $Event->setCheckCommand($event['check_command']);
            $Event->setCurrentAttempt($event['current_attempt']);
            $Event->setDisplayName($event['host_name']);
            $Event->setHostAlias($event['host_name']);
            $Event->setHostDisplayName($event['host_name']);
            $Event->setFlapping($event['is_flapping']);
            $Event->setLastCheck($event['last_check']);
            $Event->setLastHardState($event['last_hard_state']);
            $Event->setLastHardStateChange($event['last_hard_state_change']);
            $Event->setLastTimeDown($event['last_time_down']);
            $Event->setLastTimeUp($event['last_time_up']);
            $Event->setLastTimeUnreachable($event['last_time_unreachable']);
            $Event->setPluginOutput($event['plugin_output']);
            $Event->setState($event['current_state']);
            $Event->setStateType($event['state_type']);
            $Event->setScheduledDowntimeDepth($event['scheduled_downtime_depth']);
            $Event->setMaxCheckAttempts($event['max_attempts']);
            $Event->setNotificationsEnabled($event['notifications_enabled']);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());
            $Event->setBackendLevel($this->backend->getLevel());

            $events[] = $Event;
        }

        return $events;
    }

    /**
     * @param array $rawItems
     * @return array
     *
     * Array
     * (
     * [0] => Array
     * (
     * [host_name] => SRV-MAIL
     * [service_description] => IPMI
     * [current_state] => 3
     * [plugin_output] => /usr/sbin/ipmi-sensors: connection timeout
     * [last_check] => 1403521039
     * [active_checks_enabled] => 1
     * [problem_has_been_acknowledged] => 0
     * )
     */
    private function getItemsArray(array $rawItems)
    {
        $items = array();
        $regexPattern = implode('|', $this->getFields());

        foreach ($rawItems[0] as $rawItem) {
            preg_match_all('/\t(' . $regexPattern . ')=(.*)/', $rawItem, $itemsValues);

            // Combinar las claves originales con sus valores y ordenar por clave
            $itemData = array_combine($itemsValues[1], array_map(array($this, 'clearArrayValues'), $itemsValues[2]));

            // Comprobar si se muestra el evento
            if ($this->checkFilter($itemData)) {
                $items[] = $itemData;
            }
        }

        return $items;
    }

    /**
     * Devuelve los campos a obtener desde la información de estado
     *
     * @return array
     */
    private function getFields()
    {
        return array(
            'current_state',
            'state_type',
            'problem_has_been_acknowledged',
            'host_name',
            'service_description',
            'check_command',
            'plugin_output',
            'last_check',
            'last_time_up',
            'last_time_ok',
            'last_time_unreachable',
            'last_hard_state_change',
            'last_hard_state',
            'last_time_down',
            'active_checks_enabled',
            'scheduled_downtime_depth',
            'current_attempt',
            'max_attempts',
            'is_flapping',
            'notifications_enabled'
        );
    }

    /**
     * Filtro para determinar qué rawItems devolver
     *
     * @param $item
     * @return bool
     */
    private function checkFilter($item)
    {
        return ($item['current_state'] != 0
            || $item['last_hard_state_change'] > (time() - Config::getConfig()->getNewItemTime() / 2)
            || $item['is_flapping'] === 1);
    }

    /**
     * @return mixed
     */
    public function getServicesProblems()
    {
        // Obtenemos los bloques que corresponden al estado de los servicios
        preg_match_all('/servicestatus {.*}/isU', $this->fileData, $servicesData);

        $events = array();

        foreach ($this->getItemsArray($servicesData) as $event) {
            $Event = new Service();
            $Event->setAcknowledged($event['problem_has_been_acknowledged']);
            $Event->setActiveChecksEnabled($event['active_checks_enabled']);
            $Event->setCheckCommand($event['check_command']);
            $Event->setCurrentAttempt($event['current_attempt']);
            $Event->setDisplayName($event['service_description']);
            $Event->setHostAlias($event['host_name']);
            $Event->setHostDisplayName($event['host_name']);
            $Event->setFlapping($event['is_flapping']);
            $Event->setLastCheck($event['last_check']);
            $Event->setLastHardState($event['last_hard_state']);
            $Event->setLastHardStateChange($event['last_hard_state_change']);
            $Event->setPluginOutput($event['plugin_output']);
            $Event->setState($event['current_state']);
            $Event->setStateType($event['state_type']);
            $Event->setScheduledDowntimeDepth($event['scheduled_downtime_depth']);
            $Event->setMaxCheckAttempts($event['max_attempts']);
            $Event->setNotificationsEnabled($event['notifications_enabled']);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());
            $Event->setBackendLevel($this->backend->getLevel());

            $events[] = $Event;
        }

        return $events;
    }

    /**
     * Función para devolver el formato correcto de los valores de un array
     *
     * @param string $value El valor a formatear
     * @return int|string
     */
    private function clearArrayValues($value)
    {
        return is_numeric($value) ? (int)$value : htmlentities($value);
    }

    /**
     * @param ConfigBackendStatus $backend
     */
    public function setBackend($backend)
    {
        if ($backend instanceof ConfigBackendStatus) {
            $this->backend = $backend;
        }
    }
}