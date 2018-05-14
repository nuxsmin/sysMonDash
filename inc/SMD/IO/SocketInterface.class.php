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

namespace SMD\IO;

use Exception;

interface SocketInterface
{
    /**
     * Realizar una petición a un socket y obtener los resultados.
     *
     * @param string $inData Los datos a consultar
     * @return bool|string
     * @throws Exception
     */
    public function getDataFromSocket(&$inData);

    /**
     * @param string $socketPath
     * @return mixed
     */
    public function setSocketPath($socketPath);

    /**
     * @return string
     */
    public function getSocketPath();
}