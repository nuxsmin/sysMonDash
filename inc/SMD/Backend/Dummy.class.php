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

namespace SMD\Backend;

use SMD\Backend\Event\Downtime;
use SMD\Backend\Event\Event;
use SMD\Backend\Event\Trigger;
use SMD\Core\ConfigBackendDummy;

/**
 * Class Dummy
 * @package SMD\Backend
 */
class Dummy extends Backend implements BackendInterface
{
    const EVENT_RND_TIME_OLD = 7200;
    const MAINTENANCE_RND_TIME = 5400;
    const MAINTENANCE_RND_TIME_OLD = 86400;
    const MAINTENANCE_RND_TIME_MIN = 28800;
    const MAINTENANCE_RND_TIME_MAX = 32400;

    /**
     * @var string
     */
    protected $path;
    /**
     * @var mixed
     */
    protected $fileData;

    /**
     * Dummy constructor.
     * @param ConfigBackendDummy $backend
     * @throws \Exception
     */
    public function __construct(ConfigBackendDummy $backend)
    {
        $this->backend = $backend;
        $this->path = $backend->getPath();

        $this->getStatusData();
    }

    /**
     * Obtener los datos de monitorización mediante el archivo status.dat
     * @throws \Exception
     */
    private function getStatusData()
    {
        $data = file_get_contents($this->path);

        // Archivo con información de estado de Dummy
        if (!$data) {
            throw new \Exception('Error al obtener los datos de monitorización');
        }

        $this->fileData = json_decode($data);
    }

    /**
     * Devuelve los eventos
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getProblems()
    {
        return $this->getHostsProblems();
    }

    /**
     * Devuelve los eventos de los hosts
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getHostsProblems()
    {
        $changed = false;
        $events = [];

        foreach ($this->fileData->triggers as $event) {
            // Camiar la hora de los eventos de forma aleatoria
            $time = $event->last_state_change;

            if ($time < (time() - self::EVENT_RND_TIME_OLD)) {
                $time = mt_rand(time() - self::EVENT_RND_TIME_OLD, time());

                $event->last_state_change = $time;
                $changed = true;
            }

            $Event = new Trigger();
            $Event->setType(Event::TYPE_TRIGGER);
            $Event->setState($event->state);
            $Event->setStateType($event->state);
            $Event->setAcknowledged($event->acknowledged);
            $Event->setHostDisplayName($event->host_name);
            $Event->setDisplayName($event->display_name);
            $Event->setCheckCommand($event->trigger_id);
            $Event->setPluginOutput($event->description);
            $Event->setLastCheck($time);
            $Event->setLastHardState($time);
            $Event->setLastHardStateChange($time);
            $Event->setActiveChecksEnabled($event->status);
            $Event->setScheduledDowntimeDepth($event->maintenance_status);
            $Event->setCurrentAttempt($event->value);
            $Event->setNotificationsEnabled(true);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());
            $Event->setBackendLevel($this->backend->getLevel());
            $Event->setBackendImage($this->backend->getImagePath());

            $events[] = $Event;
        }

        // Escribir los datos si ha cambiado el tiempo de los eventos
        if ($changed) {
            $this->writeStatusData();
        }

        return $events;
    }

    /**
     * Escribir los datos de monitorización
     * @throws \Exception
     */
    private function writeStatusData()
    {
        // Archivo con información de estado de Dummy
        if (!file_put_contents($this->path, json_encode($this->fileData))) {
            throw new \Exception('Error al escribir los datos de monitorización');
        }
    }

    /**
     * Devuelve los eventos de los servicios
     *
     * @return void
     */
    public function getServicesProblems()
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getScheduledDowntimesGroupped()
    {
        return $this->getScheduledDowntimes();
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getScheduledDowntimes()
    {
        if (!isset($this->fileData->downtimes)) {
            return [];
        }

        $changed = false;
        $downtimes = [];

        foreach ($this->fileData->downtimes as $maintenance) {
            // Camiar la hora de los mantenimientos de forma aleatoria
            // dentro de las 8-9 horas siguientes
            $start = $maintenance->start;

            if ($start < (time() - self::MAINTENANCE_RND_TIME_OLD)) {
                $start = mt_rand(time() + self::MAINTENANCE_RND_TIME_MIN, time() + self::MAINTENANCE_RND_TIME_MAX);

                $maintenance->start = $start;
                $maintenance->active_till = $start + self::MAINTENANCE_RND_TIME;

                $changed = true;
            }

            if (time() <= $maintenance->active_till) {
                $downtime = new Downtime();
                $downtime->setAuthor('Zabbix');
                $downtime->setComment($maintenance->description);
                $downtime->setHostName($maintenance->host_name);
                $downtime->setServiceDisplayName('-');
                $downtime->setStartTime($start);
                $downtime->setEndTime($maintenance->active_till);
                $downtime->setBackendAlias($this->backend->getAlias());

                $downtimes[] = $downtime;
            }
        }

        if ($changed) {
            $this->writeStatusData();
        }

        return $downtimes;
    }
}