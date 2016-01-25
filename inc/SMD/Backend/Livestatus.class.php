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

use Exception;
use SMD\IO\Socket;
use SMD\Util\Util;

/**
 * Class Livestatus para la obtención de datos desde el socket de livestatus
 * @package SMD\Backend
 */
class Livestatus extends Backend implements BackendInterface
{
    /**
     * Obtener el listado de hosts
     *
     * @return mixed
     */
    public function getHostsProblems()
    {
        global $newItemTime;

        $fields = array(
            'alias',
            'state',
            'check_command',
            'display_name',
            'current_attempt',
            'max_check_attempts',
            'hard_state',
            'is_flapping',
            'plugin_output',
            'notes',
            'acknowledged',
            'acknowledgement_type',
            'action_url_expanded',
            'active_checks_enabled',
            'last_hard_state',
            'scheduled_downtime_depth',
            'last_check',
            'last_hard_state_change',
            'last_time_down',
            'last_time_unreachable',
            'last_time_up',
            'host_alias',
            'host_is_flapping',
            'state_type'
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET hosts',
                'Filter: checks_enabled = 1',
                'Filter: state != ' . HOST_UP,
                'Filter: last_hard_state_change > ' . (time() - $newItemTime / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET hosts\nFilter: state != " . HOST_UP . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        return ($this->isAllHeaders() === false) ? $this->mapDataValues($fields, $data) : $data;
    }

    /**
     * Obtener el listado de servicios
     *
     * @return mixed
     */
    public function getServicesProblems()
    {
        global $newItemTime;

        $fields = array(
            'acknowledged',
            'acknowledgement_type',
            'action_url_expanded',
            'active_checks_enabled',
            'check_command',
            'checks_enabled',
            'current_attempt',
            'display_name',
            'has_been_checked',
            'host_action_url_expanded',
            'host_active_checks_enabled',
            'host_alias',
            'host_checks_enabled',
            'host_current_attempt',
            'host_is_flapping',
            'host_last_check',
            'host_last_hard_state_change',
            'host_last_state',
            'host_last_state_change',
            'host_last_time_down',
            'host_last_time_unreachable',
            'host_last_time_up',
            'host_state',
            'host_display_name',
            'host_scheduled_downtime_depth',
            'is_flapping',
            'last_check',
            'last_hard_state',
            'last_hard_state_change',
            'last_state',
            'last_state_change',
            'last_time_critical',
            'last_time_ok',
            'last_time_unknown',
            'last_time_warning',
            'perf_data',
            'plugin_output',
            'pnpgraph_present',
            'state',
            'state_type',
            'scheduled_downtime_depth',
            'max_check_attempts'
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET services',
                'Filter: checks_enabled = 1',
                'Filter: state != ' . SERVICE_OK,
                'Filter: last_hard_state_change > ' . (time() - $newItemTime / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET services\nFilter: state != " . SERVICE_OK . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        return ($this->isAllHeaders() === false) ? $this->mapDataValues($fields, $data) : $data;
    }

    /**
     * Obtener el listado de paradas programadas.
     *
     * @return mixed
     */
    public function getScheduledDowntimes()
    {
        $fields = array(
            'author',
            'comment',
            'duration',
            'host_alias',
            'host_name',
            'is_service',
            'service_display_name',
            'start_time',
            'end_time'
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET downtimes',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );
            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET downtimes\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        return ($this->isAllHeaders() === false) ? $this->mapDataValues($fields, $data) : $data;
    }

    /**
     * Obtener los datos desde el socket y parsear el JSON devuelto
     *
     * @param string $dataQuery La consulta a realizar
     * @return bool|mixed
     */
    private function getJsonFromSocket(&$dataQuery)
    {
        try {
            $Socket = new Socket();
            $data = json_decode($Socket->getDataFromSocket($dataQuery));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(json_last_error_msg(), json_last_error());
            }
        } catch (Exception $e) {
            error_log('ERROR: ' . $e->getMessage());
            return false;
        }

        return $data;
    }

    /**
     * Agrupar las paradas programadas para aquellos hosts que tengan mas de X servicios programados.
     *
     * @return array Con los eventos programados
     */
    public function getScheduledDowntimesGroupped()
    {
        $groupCount = 5;
        $hosts = array();
        $out = array();
        $downtimes = Util::arraySortByKey($this->getScheduledDowntimes(), 'start_time', false);

        // Recorrer el array de eventos y contabilizar el número de veces que aparece cada host
        foreach ($downtimes as $downtime) {
            // Incrementar el contador y silenciar avisos
            @$hosts[$downtime['host_alias']]['count']++;
        }

        // Recorrer el array de eventos y agrupar aquellos eventos de un host que se repitan más de $groupCount.
        // Se utiliza un nuevo array con la clave el nombre del host
        foreach ($downtimes as $downtime) {
            $hostName = $downtime['host_alias'];
            $hostCounter = $hosts[$hostName]['count'];

            if (isset($out[$hostName])) {
                continue;
            } elseif ($hostCounter > $groupCount) {
                $out[$hostName]['host_alias'] = $hostName;
                $out[$hostName]['service_display_name'] = 'Programado para ' . $hostCounter . ' servicios';
                $out[$hostName]['start_time'] = $downtime['start_time'];
                $out[$hostName]['end_time'] = $downtime['end_time'];
                $out[$hostName]['author'] = $downtime['author'];
                $out[$hostName]['comment'] = $downtime['comment'];
            } else {
                $out[$hostName] = $downtime;
            }
        }

        return $out;
    }
}