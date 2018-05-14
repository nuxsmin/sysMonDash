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

namespace SMD\Core;

/**
 * Class ConfigBackend
 *
 * @package SMD\Core
 */
abstract class ConfigBackend
{
    const TYPE_LIVESTATUS = 1;
    const TYPE_STATUS = 2;
    const TYPE_ZABBIX = 3;
    const TYPE_SMD = 4;
    const TYPE_CHECKMK = 5;
    const TYPE_DUMMY = 99;

    /**
     * @var int
     */
    protected $type = 0;
    /**
     * @var string
     */
    protected $path = '';
    /**
     * @var string
     */
    protected $url = '';
    /**
     * @var bool
     */
    protected $active = true;
    /**
     * @var string
     */
    protected $alias = '';
    /**
     * @var int
     */
    protected $level = 0;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    protected function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param int $level
     * @return mixed
     */
    abstract public function setLevel($level);

    /**
     * @param $file
     * @return string
     * @throws \RuntimeException
     */
    protected function checkFile($file)
    {
        if (!file_exists($file) || filetype($file) !== 'file') {
            throw new \RuntimeException('Ruta o archivo no válido');
        }

        return $file;
    }
}