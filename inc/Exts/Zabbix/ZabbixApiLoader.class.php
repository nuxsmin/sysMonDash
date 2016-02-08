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

namespace Exts\Zabbix;

/**
 * Class ZabbixApiLoader para cargar la API de Zabbix
 *
 * @package Exts\Zabbix
 */
class ZabbixApiLoader
{
    /**
     * Tipos de objetos de los eventos
     */
    const EVENT_OBJECT_TRIGGER = 0;
    const EVENT_OBJECT_DISCOVERY_HOST = 1;
    const EVENT_OBJECT_DISCOVERY_SERVICE = 2;
    const EVENT_OBJECT_AUTO_HOST = 3;
    const EVENT_OBJECT_INTERNAL_ITEM = 4;
    const EVENT_OBJECT_INTERNAL_LLD = 5;
    /**
     * Tipos de valores de los eventos
     */
    const EVENT_VALUE_OK = 0;
    const EVENT_VALUE_PROBLEM = 1;

    /**
     * Obtener la API de Zabbix según versión
     *
     * @param $version int la versión de la API
     *
     * @return V223\ZabbixApi|V243\ZabbixApi
     * @throws \Exception
     */
    public static function getAPI($version)
    {
        $version = 'V' . $version;
        $apiDir = __DIR__ . DIRECTORY_SEPARATOR . $version;

        if (!file_exists($apiDir)) {
            throw new \Exception('API de Zabbix no soportada');
        }

        $zabbixApiClass = __NAMESPACE__ . '\\' . $version . '\\ZabbixApi';
        return new $zabbixApiClass();
    }
}