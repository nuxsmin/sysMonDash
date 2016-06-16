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
 * Class Downtime
 * @package SMD\Backend\Event
 */
class Downtime implements DowntimeInterface
{
    /**
     * @var string
     */
    public $author = '';
    /**
     * @var string
     */
    public $comment = '';
    /**
     * @var int
     */
    public $duration = 0;
    /**
     * @var string
     */
    public $hostAlias = '';
    /**
     * @var string
     */
    public $hostName = '';
    /**
     * @var bool
     */
    public $isService = false;
    /**
     * @var string
     */
    public $serviceDisplayName = '';
    /**
     * @var int
     */
    public $startTime = 0;
    /**
     * @var int
     */
    public $endTime = 0;
    /**
     * @var string
     */
    private $hostHash = '';
    /**
     * @var string
     */
    public $backendAlias = '';

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param $author string
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param $comment string
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param $duration int
     */
    public function setDuration($duration)
    {
        $this->duration = intval($duration);
    }

    /**
     * @return string
     */
    public function getHostAlias()
    {
        return $this->hostAlias;
    }

    /**
     * @param $alias string
     */
    public function setHostAlias($alias)
    {
        $this->hostAlias = $alias;
    }

    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->hostName;
    }

    /**
     * @param $name string
     */
    public function setHostName($name)
    {
        $this->hostHash = (is_array($name)) ? md5(implode(',', $name)) : md5($name);
        $this->hostName = $name;
    }

    /**
     * @param $service bool
     */
    public function setIsService($service)
    {
        $this->isService = (bool)$service;
    }

    /**
     * @return bool
     */
    public function isService()
    {
        return $this->isService;
    }

    /**
     * @return string
     */
    public function getServiceDisplayName()
    {
        return $this->serviceDisplayName;
    }

    /**
     * @param $name string
     */
    public function setServiceDisplayName($name)
    {
        $this->serviceDisplayName = $name;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param $time int
     */
    public function setStartTime($time)
    {
        $this->startTime = intval($time);
    }

    /**
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param $time int
     */
    public function setEndTime($time)
    {
        $this->endTime = intval($time);
    }

    /**
     * @return string
     */
    public function getHostHash()
    {
        return $this->hostHash;
    }

    /**
     * @return string
     */
    public function getBackendAlias()
    {
        return $this->backendAlias;
    }

    /**
     * @param $alias string
     */
    public function setBackendAlias($alias)
    {
        $this->backendAlias = $alias;
    }
}