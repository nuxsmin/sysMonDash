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

namespace SMD\Backend;

use SMD\Api\Api;
use SMD\Core\ConfigBackendSMD;
use SMD\Core\Exceptions\BackendException;
use SMD\Core\Language;
use SMD\Util\Util;

/**
 * Class SMD para el backend remoto sysMonDash
 * @package SMD\Backend
 */
class SMD extends Backend implements BackendInterface
{
    /**
     * Livestatus constructor.
     * @param ConfigBackendSMD $backend
     */
    public function __construct(ConfigBackendSMD $backend)
    {
        $this->backend = $backend;
    }

    /**
     * Devuelve los eventos
     *
     * @return array|bool
     */
    public function getProblems()
    {
        return $this->getHostsProblems();
    }

    /**
     * Devuelve los eventos de los hosts
     *
     * @return array|bool
     */
    public function getHostsProblems()
    {
        $url = $this->backend->getUrl() . '?action=' . Api::ACTION_EVENTS . '&token=' . $this->backend->getToken();

        return $this->getRemoteData($url);
    }

    /**
     * Obtener los datos remotos desde la API de sysMonDash con CURL
     *
     * Devuelve los datos deserializados
     *
     * @param $url
     * @return mixed
     * @throws \Exception
     */
    protected function getRemoteData($url)
    {
        $data = json_decode(Util::getDataFromUrl($url));

        if (is_object($data) && isset($data->status) && $data->status === 1) {
            $msg = sprintf('%s (%s): %s', $this->getBackend()->getAlias(), 'SMD', $data->description);

            error_log($msg);
            throw new BackendException($msg);
        } elseif (!is_object($data)) {
            $msg = sprintf('%s (%s): %s', $this->getBackend()->getAlias(), 'SMD', Language::t('Error al acceder a la API'));

            error_log($msg);
            throw new BackendException($msg);
        }

        return unserialize(base64_decode($data->data));
    }

    /**
     * Devuelve los eventos de los servicios
     *
     * @return array|bool
     */
    public function getServicesProblems()
    {
        return array();
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array|bool
     */
    public function getScheduledDowntimes()
    {
        return $this->getScheduledDowntimesGroupped();
    }

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     */
    public function getScheduledDowntimesGroupped()
    {
        $url = $this->backend->getUrl() . '?action=' . Api::ACTION_DOWNTIMES . '&token=' . $this->backend->getToken();

        return $this->getRemoteData($url);
    }
}