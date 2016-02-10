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


/**
 * Class ConfigBackendStatus
 * @package SMD\Core
 */
class ConfigBackendStatus extends ConfigBackend
{
    /**
     * ConfigBackendStatus constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->setType(self::TYPE_STATUS);
        $this->setPath($path);
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function setPath($path)
    {
        if (!file_exists($path) || filetype($path) !== 'file') {
            throw new \Exception('Ruta o archivo no válido');
        }

        $this->path = $path;
    }
}