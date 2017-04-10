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

namespace SMD\IO;

use Exception;

/**
 * Class SocketTcp para conectar a un socket TCP
 *
 * @package SMD\IO
 */
class SocketTcp extends SocketBase
{

    /**
     * Obtener un recurso del tipo Socket utilizando el socket tcp
     * @return bool|resource
     * @throws Exception
     */
    protected function getLiveSocket()
    {
        $this->socket = stream_socket_client('tcp://' . $this->getUrl(), $errno, $errstr, 10);

        if (!$this->socket) {
            throw new Exception("ERROR: $errno - $errstr");
        }

        return true;
    }

    /**
     * Devolver la URL del socket sin protocolo
     *
     * @return string
     */
    protected function getUrl()
    {
        if (preg_match('#^https?://([\w.:]+)/?#', $this->socketPath, $match)) {
            return $match[1];
        }

        return $this->socketPath;
    }
}