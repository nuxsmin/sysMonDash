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

use SMD\Core\Config;
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
    protected $_fileData;

    /**
     * Status constructor.
     */
    public function __construct()
    {
        $this->getStatusData();
    }

    /**
     * Obtener los datos de monitorización mediante el archivo status.dat
     */
    private function getStatusData()
    {
        // Archivo con información de estado de Icinga
        if (!$this->_fileData = file_get_contents(Config::getConfig()->getStatusFile())) {
            throw new \Exception('Error al obtener los datos de monitorización');
        }

        if (Config::getConfig()->isUseNagiosQLInfo() && !isset($_SESSION['dash_hostinfo'])) {
            $_SESSION['dash_hostinfo'] = NagiosQL::getHostsDBInfo();
        }
    }

    /**
     * @return mixed
     */
    public function getHostsProblems()
    {
        // Obtenemos los bloques que corresponden al estado de los hosts y servicios
        preg_match_all('/hoststatus {.*}/isU', $this->_fileData, $hostsData);

        return $this->getItemsArray($hostsData);
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
        $masterKeys = $this->getFields();
        $regexPattern = implode('|', array_keys($masterKeys));

        foreach ($rawItems[0] as $rawItem) {
            preg_match_all('/\t(' . $regexPattern . ')=(.*)/', $rawItem, $itemsValues);

            // Combinar las claves originales con sus valores y ordenar por clave
            $itemData = array_combine($itemsValues[1], array_map(array($this, 'clearArrayValues'), $itemsValues[2]));
            ksort($itemData);

            // Obtener las claves unificadas para el array de valores del elemento
            // Es necesario obtener qué claves se encuentran en la expresión regular
            if (!isset($keys)) {
                foreach (array_keys($itemData) as $key){
                    $keys[] = $masterKeys[$key];
                }
            }

            if ($this->checkFilter($itemData)) {
                // Combinar las claves unificadas con los valores
                // Array de datos de hosts. Se utiliza la función "clearArrayValues" para transformar los valores
                $items[] = array_combine($keys, array_values($itemData));
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
            'current_state' => 'state',
            'state_type' => 'state_type',
            'problem_has_been_acknowledged' => 'acknowledged',
            'host_name' => 'host_display_name',
            'service_description' => 'display_name',
            'check_command' => 'check_command',
            'plugin_output' => 'plugin_output',
            'last_check' => 'last_check',
            'last_time_up' => 'last_time_up',
            'last_time_ok' => 'last_time_ok',
            'last_time_unreachable' => 'last_time_unreachable',
            'last_hard_state_change' => 'last_hard_state_change',
            'last_hard_state' => 'last_hard_state',
            'last_time_down' => 'last_time_down',
            'active_checks_enabled' => 'active_checks_enabled',
            'scheduled_downtime_depth' => 'scheduled_downtime_depth',
            'current_attempt' => 'current_attempt',
            'max_attempts' => 'max_check_attempts',
            'is_flapping' => 'is_flapping',
            'notifications_enabled' => 'notifications_enabled'
        );
    }

    /**
     * @return mixed
     */
    public function getServicesProblems()
    {
        // Obtenemos los bloques que corresponden al estado de los servicios
        preg_match_all('/servicestatus {.*}/isU', $this->_fileData, $servicesData);

        return $this->getItemsArray($servicesData);
    }

    /**
     * @return mixed
     */
    public function getScheduledDowntimes()
    {
        // TODO: Implement getScheduledDowntimes() method.
    }

    /**
     * @return mixed
     */
    public function getScheduledDowntimesGroupped()
    {
        // TODO: Implement getScheduledDowntimesGroupped() method.
    }

    /**
     * Función para devolver el formato correcto de los valores de un array
     *
     * @param string $value El valor a formatear
     * @return int|string
     */
    private function clearArrayValues($value)
    {
        return (is_numeric($value)) ? intval($value) : htmlentities($value);
    }

    /**
     * Filtro para determinar qué items devolver
     *
     * @param $item
     * @return bool
     */
    private function checkFilter(&$item)
    {
        return ($item['current_state'] != 0
            || $item['last_hard_state_change'] > (time() - Config::getConfig()->getNewItemTime() / 2)
            || $item['is_flapping'] === 1);
    }

    /**
     * @return array
     */
    public function getProblems()
    {
        return array_merge($this->getHostsProblems(), $this->getServicesProblems());
    }
}