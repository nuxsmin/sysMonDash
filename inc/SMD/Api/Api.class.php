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
 */

namespace SMD\Api;

use SMD\Core\Config;
use SMD\Core\sysMonDash;
use SMD\Util\Json;

/**
 * Class Api para la implementación de un backend de sysMonDash remoto
 *
 * @package SMD\Api
 */
class Api
{
    const ACTION_EVENTS = 1;
    const ACTION_DOWNTIMES = 2;

    /**
     * Devolver los eventos en formato JSON
     *
     * @return string
     * @throws \Exception
     */
    public function getEvents()
    {
        $SMD = new sysMonDash();
        $events = $SMD->getRawEvents();

        return Json::getJson($events);
    }

    /**
     * Devolver las paradas programadas en formato JSON
     *
     * @return string
     * @throws \Exception
     */
    public function getDowntimes()
    {
        $SMD = new sysMonDash();
        $downtimes = $SMD->getRawDowntimes();

        return Json::getJson($downtimes);
    }

    /**
     * Comprobar el token de seguridad
     *
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        return ($token === Config::getConfig()->getAPIToken());
    }
}