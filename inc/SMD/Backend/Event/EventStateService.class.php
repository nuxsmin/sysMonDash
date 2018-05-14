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

namespace SMD\Backend\Event;

/**
 * Class EventStateService
 * @package SMD\Backend\Event
 */
class EventStateService implements EventStateInterface
{
    /**
     * @var array
     */
    private static $states = array(
        0 => array('OK', 'up'),
        1 => array('AVISO', 'warning'),
        2 => array('CRITICO', 'critical'),
        3 => array('DESCONOCIDO', 'unknown'),
        self::STATE_ACKNOWLEDGED => array('RECONOCIDO', 'acknowledged'),
        self::STATE_RECOVER => array('RECUPERADO', 'new-up'),
        self::STATE_FLAPPING => array('CAMBIANTE', 'flapping'),
        self::STATE_SCHEDULED => array('PROGRAMADO', 'downtime'),
        self::STATE_UNREACHABLE => array('INALCANZABLE', 'unknown')
    );

    /**
     * Devuelve la clase CSS a utilizar para mostrar el estado
     *
     * @param $state
     * @return string
     */
    public static function getStateClass($state)
    {
        return isset(self::$states[$state]) ? self::$states[$state][1] : null;
    }

    /**
     * Devuelve el nombre del estado
     *
     * @param $state
     * @return string
     */
    public static function getStateName($state)
    {
        return isset(self::$states[$state]) ? self::$states[$state][0] : null;
    }

    /**
     * Devuelve los estados soportados
     * 
     * @return array
     */
    public static function getStates()
    {
        return self::$states;
    }
}