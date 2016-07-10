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
 *
 */

namespace SMD\IO;

use Exception;

/**
 * Class Socket
 * @package SMD\IO
 */
abstract class SocketBase implements SocketInterface
{
    /**
     * @var null
     */
    protected $socketPath = null;
    /**
     * @var resource
     */
    protected $socket = null;

    /**
     * Socket constructor.
     * @param null $socketPath
     */
    public function __construct($socketPath)
    {
        $this->socketPath = $socketPath;
    }

    /**
     * @return string
     */
    public function getSocketPath()
    {
        return $this->socketPath;
    }

    /**
     * @param string $socketPath
     * @return mixed
     */
    public function setSocketPath($socketPath)
    {
        $this->socketPath = $socketPath;
    }

    /**
     * Realizar una petición a un socket y obtener los resultados.
     *
     * @param string $inData Los datos a consultar
     * @return bool|string
     * @throws Exception
     */
    public function getDataFromSocket(&$inData)
    {
        try {
            if (!is_resource($this->socket)) {
                $this->getLiveSocket();
            }

            fwrite($this->socket, $inData);
            $outData = stream_get_contents($this->socket);
            fclose($this->socket);
        } catch (Exception $e) {
            throw $e;
        }

        return $outData;
    }

    /**
     * Obtener un recurso del tipo Socket utilizando el socket
     * @return bool|resource
     * @throws Exception
     */
    protected abstract function getLiveSocket();
}