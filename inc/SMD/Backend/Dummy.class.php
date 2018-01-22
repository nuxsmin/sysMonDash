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

use SMD\Backend\Event\Host;
use SMD\Backend\Event\Service;
use SMD\Core\ConfigBackendDummy;

/**
 * Class Dummy
 * @package SMD\Backend
 */
class Dummy extends Backend implements BackendInterface
{
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
     */
    public function getProblems()
    {
        return array_merge($this->getHostsProblems(), $this->getServicesProblems());
    }

    /**
     * Devuelve los eventos de los hosts
     *
     * @return array|bool
     */
    public function getHostsProblems()
    {
        $events = [];

        foreach ($this->fileData as $event) {
            $Event = new Host();
            $Event->setAcknowledged($event->problem_has_been_acknowledged);
            $Event->setActiveChecksEnabled($event->active_checks_enabled);
            $Event->setCheckCommand($event->check_command);
            $Event->setCurrentAttempt($event->current_attempt);
            $Event->setDisplayName($event->host_name);
            $Event->setHostAlias($event->host_name);
            $Event->setHostDisplayName($event->host_name);
            $Event->setFlapping($event->is_flapping);
            $Event->setLastCheck($event->last_check);
            $Event->setLastHardState($event->last_hard_state);
            $Event->setLastHardStateChange($event->last_hard_state_change);
            $Event->setLastTimeDown($event->last_time_down);
            $Event->setLastTimeUp($event->last_time_up);
            $Event->setLastTimeUnreachable($event->last_time_unreachable);
            $Event->setPluginOutput($event->plugin_output);
            $Event->setState($event->current_state);
            $Event->setStateType($event->state_type);
            $Event->setScheduledDowntimeDepth($event->scheduled_downtime_depth);
            $Event->setMaxCheckAttempts($event->max_attempts);
            $Event->setNotificationsEnabled($event->notifications_enabled);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());
            $Event->setBackendLevel($this->backend->getLevel());
            $Event->setBackendImage($this->backend->getImagePath());

            $events[] = $Event;
        }

        return $events;
    }

    /**
     * Devuelve los eventos de los servicios
     *
     * @return array|bool
     */
    public function getServicesProblems()
    {
        $events = [];

        foreach ($this->fileData as $event) {
            $Event = new Service();
            $Event->setAcknowledged($event->problem_has_been_acknowledged);
            $Event->setActiveChecksEnabled($event->active_checks_enabled);
            $Event->setCheckCommand($event->check_command);
            $Event->setCurrentAttempt($event->current_attempt);
            $Event->setDisplayName($event->service_description);
            $Event->setHostAlias($event->host_name);
            $Event->setHostDisplayName($event->host_name);
            $Event->setFlapping($event->is_flapping);
            $Event->setLastCheck($event->last_check);
            $Event->setLastHardState($event->last_hard_state);
            $Event->setLastHardStateChange($event->last_hard_state_change);
            $Event->setPluginOutput($event->plugin_output);
            $Event->setState($event->current_state);
            $Event->setStateType($event->state_type);
            $Event->setScheduledDowntimeDepth($event->scheduled_downtime_depth);
            $Event->setMaxCheckAttempts($event->max_attempts);
            $Event->setNotificationsEnabled($event->notifications_enabled);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());
            $Event->setBackendLevel($this->backend->getLevel());
            $Event->setBackendImage($this->backend->getImagePath());

            $events[] = $Event;
        }

        return $events;
    }

    /**
     * Devuelve los eventos programados
     *
     * @return array|bool
     */
    public function getScheduledDowntimes()
    {
        return [];
    }

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     */
    public function getScheduledDowntimesGroupped()
    {
        return [];
    }
}