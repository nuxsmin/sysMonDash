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

/**
 * Class SocketFile para conectar a un socket unix
 * @package SMD\IO
 */
class SocketUnix extends SocketBase
{
    /**
     * @return mixed
     * @throws Exception
     */
    protected function getLiveSocket()
    {
        if (file_exists($this->socketPath) && filetype($this->socketPath) === 'socket') {
            $this->socket = stream_socket_client('unix://' . $this->socketPath, $errno, $errstr);

            if (!$this->socket) {
                throw new Exception("ERROR: $errno - $errstr");
            }
        } else {
            throw new Exception("ERROR: unable to read file $this->socketPath");
        }

        return $this->socket;
    }
}