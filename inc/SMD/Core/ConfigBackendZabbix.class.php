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

namespace SMD\Core;

use SMD\Backend\Event\EventStateTrigger;

/**
 * Class ConfigBackendZabbix
 *
 * @package SMD\Core
 */
class ConfigBackendZabbix extends ConfigBackend
{
    /**
     * @var int
     */
    protected $version = 0;
    /**
     * @var string
     */
    protected $user = '';
    /**
     * @var string
     */
    protected $pass = '';

    /**
     * ConfigBackendZabbix constructor.
     * @param $version
     * @param $url
     * @param $user
     * @param $pass
     * @param int $level
     */
    public function __construct($version, $url, $user, $pass, $level = 0)
    {
        $this->setType(self::TYPE_ZABBIX);
        $this->setVersion($version);
        $this->setUrl($url);
        $this->setUser($user);
        $this->setPass($pass);
        $this->setLevel($level);
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = (int)$version;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param string $pass
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * @param int $level
     * @return mixed|void
     */
    public function setLevel($level)
    {
        $levels = EventStateTrigger::getStates();

        if (isset($levels[$level])) {
            $this->level = $level;
        } else {
            throw new \InvalidArgumentException('Nivel no disponible');
        }
    }
}