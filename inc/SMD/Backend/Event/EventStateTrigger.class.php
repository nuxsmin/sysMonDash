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
 * Class EventStateTrigger
 * @package SMD\Backend\Event
 */
class EventStateTrigger implements EventStateInterface
{
    /**
     * @var array
     */
    private static $states = array(
        0 => array('DESCONOCIDO', 'unknown'),
        1 => array('INFORMACION', 'information'),
        2 => array('AVISO', 'warning'),
        3 => array('MEDIO', 'average'),
        4 => array('ALTO', 'high'),
        5 => array('CRITICO', 'critical'),
        self::STATE_ACKNOWLEDGED => array('RECONOCIDO', 'acknowledged'),
        self::STATE_SCHEDULED => array('PROGRAMADO', 'downtime')
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