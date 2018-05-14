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

use SMD\Core\Config;
use SMD\Core\ConfigBackendCheckMK;

/**
 * Class CheckMK para la obtención de datos desde el socket de CheckMK
 *
 * @package SMD\Backend
 */
class CheckMK extends Livestatus
{
    /**
     * @param ConfigBackendCheckMK $backend
     */
    public function setBackend(ConfigBackendCheckMK $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Devolver la consulta a realizar en el socket para los hosts
     *
     * @param array $fields
     * @return string
     */
    protected function getHostsFilter(array $fields)
    {
        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET hosts',
                'ResponseHeader: fixed16',
                'Filter: host_checks_enabled = 1',
                'Filter: state != ' . HOST_UP,
                'Filter: last_hard_state_change > ' . (time() - Config::getConfig()->getNewItemTime() / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET hosts\nFilter: state != " . HOST_UP . "\nFilter: host_checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        return $dataQuery;
    }

    /**
     * Devolver la consulta a realizar en el socket para los servicios
     *
     * @param array $fields
     * @return string
     */
    protected function getServicesFilter(array $fields)
    {
        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET services',
                'ResponseHeader: fixed16',
                'Filter: host_checks_enabled = 1',
                'Filter: state != ' . SERVICE_OK,
                'Filter: last_hard_state_change > ' . (time() - Config::getConfig()->getNewItemTime() / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET services\nFilter: state != " . SERVICE_OK . "\nFilter: host_checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        return $dataQuery;
    }
}