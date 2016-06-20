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
use SMD\Backend\Event\EventStateHost;

/**
 * Class ConfigBackendLivestatus
 * @package SMD\Core
 */
class ConfigBackendLivestatus extends ConfigBackend
{
    /**
     * ConfigBackendLivestatus constructor.
     * @param $path
     * @param int $level
     * @throws \Exception
     */
    public function __construct($path, $level = 0)
    {
        $this->setType(self::TYPE_LIVESTATUS);
        $this->setPath($path);
        $this->setLevel($level);
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function setPath($path)
    {
        if (!file_exists($path) || filetype($path) !== 'socket') {
            throw new \Exception('Ruta o archivo no válido');
        }

        $this->path = $path;
    }

    /**
     * @param int $level
     * @return mixed
     */
    public function setLevel($level)
    {
        $levels = EventStateHost::getStates();

        if (isset($levels[$level])) {
            $this->level = $level;
        } else {
            throw new \InvalidArgumentException('Nivel no disponible');
        }
    }
}