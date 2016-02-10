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
 * Class DowntimeInterface
 * @package SMD\Backend\Event
 */
interface DowntimeInterface
{
    /**
     * @return string
     */
    public function getHostHash();

    /**
     * @param $author string
     */
    public function setAuthor($author);

    /**
     * @return string
     */
    public function getAuthor();

    /**
     * @param $comment string
     */
    public function setComment($comment);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param $duration int
     */
    public function setDuration($duration);

    /**
     * @return int
     */
    public function getDuration();

    /**
     * @param $alias string
     */
    public function setHostAlias($alias);

    /**
     * @return string
     */
    public function getHostAlias();

    /**
     * @param $name string
     */
    public function setHostName($name);

    /**
     * @return string
     */
    public function getHostName();

    /**
     * @param $service bool
     */
    public function setIsService($service);

    /**
     * @return bool
     */
    public function isService();

    /**
     * @param $name string
     */
    public function setServiceDisplayName($name);

    /**
     * @return string
     */
    public function getServiceDisplayName();

    /**
     * @param $time int
     */
    public function setStartTime($time);

    /**
     * @return int
     */
    public function getStartTime();

    /**
     * @param $time int
     */
    public function setEndTime($time);

    /**
     * @return int
     */
    public function getEndTime();

    /**
     * @return string
     */
    public function getBackendAlias();

    /**
     * @param $alias string
     */
    public function setBackendAlias($alias);
}