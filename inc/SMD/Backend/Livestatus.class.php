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

namespace SMD\Backend;

use Exception;
use SMD\Backend\Event\Downtime;
use SMD\Backend\Event\DowntimeInterface;
use SMD\Backend\Event\Host;
use SMD\Backend\Event\Service;
use SMD\Core\Config;
use SMD\Core\ConfigBackendLivestatus;
use SMD\Core\Language;
use SMD\IO\Socket;
use SMD\Util\Util;

/**
 * Class Livestatus para la obtención de datos desde el socket de livestatus
 * @package SMD\Backend
 */
class Livestatus extends Backend implements BackendInterface
{
    /**
     * @var string
     */
    protected $path = '';

    /**
     * Livestatus constructor.
     * @param ConfigBackendLivestatus $backend
     */
    public function __construct(ConfigBackendLivestatus $backend)
    {
        $this->backend = $backend;
        $this->path = $backend->getPath();
    }

    /**
     * Obtener el listado de hosts
     *
     * @return mixed
     */
    public function getHostsProblems()
    {
        $fields = array(
            'alias',
            'state',
            'check_command',
            'display_name',
            'current_attempt',
            'max_check_attempts',
            'is_flapping',
            'plugin_output',
            'acknowledged',
            'active_checks_enabled',
            'last_hard_state',
            'scheduled_downtime_depth',
            'last_check',
            'last_hard_state_change',
            'last_time_down',
            'last_time_unreachable',
            'last_time_up',
            'host_alias',
            'state_type'
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET hosts',
                'Filter: checks_enabled = 1',
                'Filter: state != ' . HOST_UP,
                'Filter: last_hard_state_change > ' . (time() - Config::getConfig()->getNewItemTime() / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET hosts\nFilter: state != " . HOST_UP . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        $events = [];

        foreach ($data as $event) {
            $Event = new Host();
            $Event->setAlias($event[0]);
            $Event->setState($event[1]);
            $Event->setCheckCommand($event[2]);
            $Event->setDisplayName($event[3]);
            $Event->setCurrentAttempt($event[4]);
            $Event->setMaxCheckAttempts($event[5]);
            $Event->setFlapping($event[6]);
            $Event->setPluginOutput($event[7]);
            $Event->setAcknowledged($event[8]);
            $Event->setActiveChecksEnabled($event[9]);
            $Event->setLastHardState($event[10]);
            $Event->setScheduledDowntimeDepth($event[11]);
            $Event->setLastCheck($event[12]);
            $Event->setLastHardStateChange($event[13]);
            $Event->setLastTimeDown($event[14]);
            $Event->setLastTimeUnreachable($event[15]);
            $Event->setLastTimeUp($event[16]);
            $Event->setHostAlias($event[17]);
            $Event->setStateType($event[18]);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());

            $events[] = $Event;
        }

        return ($this->isAllHeaders() === false) ? $events : $data;
    }

    /**
     * Obtener el listado de servicios
     *
     * @return mixed
     */
    public function getServicesProblems()
    {
        $fields = array(
            'acknowledged',
            'active_checks_enabled',
            'check_command',
            'current_attempt',
            'display_name',
            'host_alias',
            'host_last_time_unreachable',
            'host_last_time_up',
            'host_state',
            'host_display_name',
            'host_scheduled_downtime_depth',
            'is_flapping',
            'last_check',
            'last_hard_state',
            'last_hard_state_change',
            'last_time_critical',
            'last_time_ok',
            'last_time_unknown',
            'plugin_output',
            'state',
            'state_type',
            'scheduled_downtime_depth',
            'max_check_attempts',
            'pnpgraph_present',
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET services',
                'Filter: checks_enabled = 1',
                'Filter: state != ' . SERVICE_OK,
                'Filter: last_hard_state_change > ' . (time() - Config::getConfig()->getNewItemTime() / 2),
                'Filter: is_flapping = 1',
                'Or: 3',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );

            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET services\nFilter: state != " . SERVICE_OK . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        $events = [];

        foreach ($data as $event) {
            $Event = new Service();
            $Event->setAcknowledged($event[0]);
            $Event->setActiveChecksEnabled($event[1]);
            $Event->setCheckCommand($event[2]);
            $Event->setCurrentAttempt($event[3]);
            $Event->setDisplayName($event[4]);
            $Event->setHostAlias($event[5]);
            $Event->setHostLastTimeUnreachable($event[6]);
            $Event->setHostLastTimeUp($event[7]);
            $Event->setHostState($event[8]);
            $Event->setHostDisplayName($event[9]);
            $Event->setHostScheduledDowntimeDepth($event[10]);
            $Event->setFlapping($event[11]);
            $Event->setLastCheck($event[12]);
            $Event->setLastHardState($event[13]);
            $Event->setLastHardStateChange($event[14]);
            $Event->setLastTimeDown($event[15]);
            $Event->setLastTimeUp($event[16]);
            $Event->setLastTimeUnreachable($event[17]);
            $Event->setPluginOutput($event[18]);
            $Event->setState($event[19]);
            $Event->setStateType($event[20]);
            $Event->setScheduledDowntimeDepth($event[21]);
            $Event->setMaxCheckAttempts($event[22]);
            $Event->setBackendAlias($this->backend->getAlias());
            $Event->setBackendUrl($this->backend->getUrl());

            $events[] = $Event;
        }

        return ($this->isAllHeaders() === false) ? $events : $data;
    }

    /**
     * Obtener el listado de paradas programadas.
     *
     * @return mixed
     */
    public function getScheduledDowntimes()
    {
        $fields = array(
            'author',
            'comment',
            'duration',
            'host_alias',
            'host_name',
            'is_service',
            'service_display_name',
            'start_time',
            'end_time'
        );

        if ($this->isAllHeaders() === false) {
            $filter = array(
                'GET downtimes',
                'Columns: ' . implode(' ', $fields),
                'ColumnHeaders: off',
                'OutputFormat: json'
            );
            $dataQuery = implode("\n", $filter) . "\n\n";
        } else {
            $dataQuery = "GET downtimes\nColumnHeaders: off\nOutputFormat: json\n\n";
        }

        $data = $this->getJsonFromSocket($dataQuery);

        $downtimes = [];

        foreach ($data as $downtime) {
            $Downtime = new Downtime();
            $Downtime->setAuthor($downtime[0]);
            $Downtime->setComment($downtime[1]);
            $Downtime->setDuration($downtime[2]);
            $Downtime->setHostAlias($downtime[3]);
            $Downtime->setHostName($downtime[4]);
            $Downtime->setIsService($downtime[5]);
            $Downtime->setServiceDisplayName($downtime[6]);
            $Downtime->setStartTime($downtime[7]);
            $Downtime->setEndTime($downtime[8]);

            $downtimes[] = $Downtime;
        }

        return ($this->isAllHeaders() === false) ? $downtimes : $data;
    }

    /**
     * Obtener los datos desde el socket y parsear el JSON devuelto
     *
     * @param string $dataQuery La consulta a realizar
     * @return bool|mixed
     */
    private function getJsonFromSocket(&$dataQuery)
    {
        try {
            $Socket = new Socket();
            $Socket->setSocketFile($this->path);
            $data = json_decode($Socket->getDataFromSocket($dataQuery));

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(json_last_error_msg(), json_last_error());
            }
        } catch (Exception $e) {
            error_log('ERROR: ' . $e->getMessage());
            return false;
        }

        return $data;
    }

    /**
     * Agrupar las paradas programadas para aquellos hosts que tengan mas de X servicios programados.
     *
     * @return array Con los eventos programados
     */
    public function getScheduledDowntimesGroupped()
    {
        $groupCount = 5;
        $hosts = array();
        $out = array();
        $downtimes = Util::arraySortByProperty($this->getScheduledDowntimes(), 'start_time', false);

        // Recorrer el array de eventos y contabilizar el número de veces que aparece cada host
        foreach ($downtimes as $downtime) {
            /** @var $downtime DowntimeInterface */

            // Incrementar el contador y silenciar avisos
            @$hosts[$downtime->getHostHash()]['count']++;
        }

        // Recorrer el array de eventos y agrupar aquellos eventos de un host que se repitan más de $groupCount.
        // Se utiliza un nuevo array con la clave el nombre del host
        foreach ($downtimes as $downtime) {
            /** @var $downtime DowntimeInterface */

            $hash = $downtime->getHostHash();
            $hostCounter = $hosts[$hash]['count'];

            if ($hostCounter > $groupCount) {
                $downtime->setServiceDisplayName(sprintf(Language::t('Programado para %d servicios'), $hostCounter));
                $out[$hash] = $downtime;
            } elseif (!isset($out[$hash])) {
                $out[$hash] = $downtime;
            }
        }

        return $out;
    }

    /**
     * @return mixed
     */
    public function getProblems()
    {
        return array_merge($this->getHostsProblems(), $this->getServicesProblems());
    }

    /**
     * @param ConfigBackendLivestatus $backend
     */
    public function setBackend(ConfigBackendLivestatus $backend)
    {
        $this->backend = $backend;
    }
}