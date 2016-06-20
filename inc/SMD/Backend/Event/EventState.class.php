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

use SMD\Core\Language;

/**
 * Class EventState
 * @package SMD\Backend\Event
 */
class EventState
{
    /**
     * Devuelve la clase CSS a utilizar para mostrar el estado
     *
     * @param EventInterface $item
     * @param null $state
     * @return bool|string
     */
    public static function getStateClass(EventInterface $item, $state = null)
    {
        $state = (is_null($state) ? $item->getState() : intval($state));

        switch ($item->getType()) {
            case Event::TYPE_HOST:
                return EventStateHost::getStateClass($state);
            case Event::TYPE_SERVICE:
                return EventStateService::getStateClass($state);
            case Event::TYPE_TRIGGER:
                return EventStateTrigger::getStateClass($state);
        }

        return false;
    }

    /**
     * Devuelve el nombre del estado
     *
     * @param EventInterface $item
     * @param null $state
     * @return bool|string
     */
    public static function getStateName(EventInterface $item, $state = null)
    {
        $state = (is_null($state) ? $item->getState() : intval($state));

        switch ($item->getType()) {
            case Event::TYPE_HOST:
                return Language::t(EventStateHost::getStateName($state));
            case Event::TYPE_SERVICE:
                return Language::t(EventStateService::getStateName($state));
            case Event::TYPE_TRIGGER:
                return Language::t(EventStateTrigger::getStateName($state));
        }

        return false;
    }
}