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

namespace SMD\Util;

use mysqli;

/**
 * Class NagiosQL con utilidades para obtener datos desde la BD de NagiosQL
 *
 * @package SMD\Util
 */
class NagiosQL
{
    /**
     * Función para obtener Información de la BD de NagiosQL
     *
     * @return array|bool
     */
    public static function getHostsDBInfo()
    {
        global $dbServer, $dbName, $dbUser, $dbUserPass;

        $mysqli = new mysqli($dbServer, $dbUser, $dbUserPass, $dbName);

        if ($mysqli->connect_errno) {
            error_log('(' . __FUNCTION__ . ') Fallo al conectar a MySQL: ' . $mysqli->connect_error);
            return false;
        }

        if (!$resQuery = $mysqli->query("SELECT host_name,alias FROM tbl_host")) {
            error_log('(' . __FUNCTION__ . ') Fallo al obtener los registros: ' . $mysqli->connect_error);
            return false;
        }

        $result = array();

        while ($row = $resQuery->fetch_assoc()) {
            $result[$row['host_name']] = $row['alias'];
        }

        // Devolvemos un array con los registros.
        // La clave es el nombre corto del host y el valor es el alias
        return $result;
    }
}