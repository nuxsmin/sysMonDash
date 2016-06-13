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

namespace SMD\Backend\Event;

/**
 * Interface EventStateInterface
 * @package SMD\Backend\Event
 */
interface EventStateInterface
{
    /**
     * Constantes con tipos de eventos especiales
     */
    const STATE_RECOVER = 10;
    const STATE_ACKNOWLEDGED = 11;
    const STATE_FLAPPING = 12;
    const STATE_SCHEDULED = 13;
    const STATE_UNREACHABLE = 14;

    /**
     * Devuelve la clase CSS a utilizar para mostrar el estado
     *
     * @param $state
     * @return string
     */
    public static function getStateClass($state);

    /**
     * Devuelve el nombre del estado
     *
     * @param $state
     * @return string
     */
    public static function getStateName($state);
}