<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2014-2016 Rubén Domínguez nuxsmin@cygnux.org
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

namespace SMD\Core;

use Exception;
use SMD\Backend\BackendInterface;
use SMD\Backend\Event\DowntimeInterface;
use SMD\Backend\Event\Event;
use SMD\Backend\Event\EventInterface;
use SMD\Backend\Event\EventState;
use SMD\Backend\Event\EventStateHost;
use SMD\Backend\Event\EventStateInterface;
use SMD\Backend\Event\EventStateService;
use SMD\Backend\Event\EventStateTrigger;
use SMD\Backend\Livestatus;
use SMD\Backend\SMD;
use SMD\Backend\Status;
use SMD\Backend\Zabbix;
use SMD\Core\Exceptions\BackendException;
use SMD\Core\Exceptions\NoDataException;
use SMD\Util\Util;

/**
 * Class sysMonDash
 * @package SMD\Core
 */
class sysMonDash
{
    /**
     * Tipos de llamada
     */
    const CALL_TYPE_NORMAL = 0;
    const CALL_TYPE_API = 1;
    /**
     * @var int
     */
    private $totalItems = 0;
    /**
     * @var int
     */
    private $displayedItems = 0;
    /**
     * @var DowntimeInterface[]
     */
    private $downtimes = array();
    /**
     * @var int
     */
    private $viewType = 0;
    /**
     * @var int
     */
    private $callType = 0;
    /**
     * @var array
     */
    private $errors = array();

    /**
     * Función para obtener los eventos de los backends y devolver los avisos en formato HTML
     *
     * @return array
     */
    public function getItems()
    {
        $htmlItems = array();
        $rawItems = array();

        try {
            // Obtener los avisos desde la monitorización
            foreach ($this->getBackends() as $Backend) {
                try {
                    $rawItems = array_merge($rawItems, $Backend->getProblems());
                    $this->downtimes = array_merge($this->downtimes, $Backend->getScheduledDowntimesGroupped());
                } catch (Exception $e) {
                    $this->errors[] = $Backend->getBackend()->getAlias() . ': ' . $e->getMessage();
                }
            }

            if ($rawItems === false) {
                throw new NoDataException(Language::t('No hay datos desde el backend'));
            }

            // Ordenar los rawItems por tiempo de último cambio
            Util::arraySortByProperty($rawItems, 'lastHardStateChange');

            $newItemTime = Config::getConfig()->getNewItemTime();

            // Recorremos el array y mostramos los elementos
            foreach ($rawItems as $item) {
                /** @var $item EventInterface */

                // Detectar si es un evento de recuperación
                $newItemUp = ($item->getState() === 0 && ($item->getLastTimeUp() || $item->getLastTimeOk())) ? (abs(time() - $item->getLastHardStateChange()) < $newItemTime / 2) : false;

                // Detectar si es un elemento nuevo, no se trata de un "RECOVERY" y no está "ACKNOWLEDGED"
                $newItem = (time() - $item->getLastHardState() <= $newItemTime && !$newItemUp && !$item->isAcknowledged());

                // Calcular los filtros de cada evento
                $runFilters = $this->filterItems($item);

                // Filtrar los eventos a mostrar
                if (($this->viewType !== VIEW_FRONTLINE && $this->viewType !== VIEW_DISPLAY)
                    || ($newItem === true
                        || $newItemUp === true
                        || $runFilters === false)
                ) {
                    $htmlItems[] = $this->getHtmlItems($item, $newItem, $newItemUp);
                    $this->displayedItems++;
                }

                // Contador del no. de elementos
                $this->totalItems++;
            }
        } catch (NoDataException $e) {
            $this->errors[] = $e->getMessage();
        } catch (BackendException $e) {
            $this->errors[] = $e->getMessage();
            //header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error - ' . utf8_decode(Language::t($e->getMessage())), true, 500);
        }

        return $htmlItems;
    }

    /**
     * Seleccionar el backend
     *
     * @return BackendInterface[]
     */
    public function getBackends()
    {
        $backends = array();

        foreach (Config::getConfig()->getBackend() as $Backend) {
            /** @var $Backend ConfigBackendLivestatus|ConfigBackendStatus|ConfigBackendZabbix|ConfigBackendSMD */
            if ($Backend->isActive()) {
                switch ($Backend->getType()) {
                    case ConfigBackend::TYPE_LIVESTATUS:
                        $backends[] = new Livestatus($Backend);
                        break;
                    case ConfigBackend::TYPE_STATUS:
                        $backends[] = new Status($Backend);
                        break;
                    case ConfigBackend::TYPE_ZABBIX:
                        $backends[] = new Zabbix($Backend);
                        break;
                    case ((ConfigBackend::TYPE_SMD && $this->callType !== self::CALL_TYPE_API) ? true : false):
                        $backends[] = new SMD($Backend);
                        break;
                }
            }
        }

        return $backends;
    }

    /**
     * Función para filtrar los avisos a mostrar
     *
     * @param EventInterface $item El elemento a verificar
     * @return bool
     */
    private function filterItems(EventInterface $item)
    {
        return ($item->isAcknowledged()
            || $this->getFilterHosts($item)
            || $this->getFilterServices($item)
            || $this->getFilterIsFlapping($item)
            || $this->getFilterState($item)
            || $this->getFilterUnreachable($item)
            || $this->getFilterScheduled($item)
            || $this->getFilterLevel($item)
        );
    }

    /**
     * Comprobar si el host se encuentra en la expresión regular
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterHosts(EventInterface $item)
    {
        $hostname = ($item->getHostDisplayName()) ? $item->getHostDisplayName() : $item->getDisplayName();

        if (!preg_match('#' . Config::getConfig()->getRegexHostShow() . '#i', $hostname)
            && !in_array($hostname, Config::getConfig()->getCriticalItems())
        ) {
            $item->setFilterStatus('No Regex Host & No Critical');
            return true;
        }

        return false;
    }

    /**
     * Comprobar si el servicio se encuentra en la expresión regular y si es un elemento crítico
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterServices(EventInterface $item)
    {

        if (Config::getConfig()->getRegexServiceNoShow() !== ''
            && preg_match('#' . Config::getConfig()->getRegexServiceNoShow() . '#i', $item->getDisplayName())
            && !in_array($item->getDisplayName(), Config::getConfig()->getCriticalItems())
        ) {
            $item->setFilterStatus('Regex Service & No Critical');
            return true;
        }

        return false;
    }

    /**
     * Comprobar si el estado es cambiante
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterIsFlapping(EventInterface $item)
    {
        if ($item->getCurrentAttempt() <= $item->getMaxCheckAttempts()
            && $item->getStateType() === 0
            && !$item->isFlapping()
        ) {
            $item->setFilterStatus('OK & No Flapping');
            return true;
        }

        return false;
    }

    /**
     * Comprobar si el host está caído y el servicio en alerta
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterState(EventInterface $item)
    {
        if ($item->getHostState()
            && $item->getState() > SERVICE_WARNING
            && $item->getHostState() >= HOST_DOWN
        ) {
            $item->setFilterStatus('Host Status');
            return true;
        }

        return false;
    }

    /**
     * Comprobar si está inalcanzable
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterUnreachable(EventInterface $item)
    {
        if ($item->getStateType() === 1
            && $item->getLastTimeUnreachable() > $item->getLastCheck()
        ) {
            $item->setFilterStatus('Unreachable');
            return true;
        }

        return false;
    }

    /**
     * Comprobar si está programado y se debe mostrar
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterScheduled(EventInterface $item)
    {
        if ($item->getScheduledDowntimeDepth() >= 1 || $item->getHostScheduledDowntimeDepth() >= 1) {
            if (!Config::getConfig()->isShowScheduled()) {
                $item->setFilterStatus('Scheduled & Show');
                return true;
            }
        }

        return false;
    }

    /**
     * Comprobar si el evento supera el nivel mínimo para mostrarlo
     *
     * @param EventInterface $item
     * @return bool
     */
    private function getFilterLevel(EventInterface $item)
    {
        if (null !== $item->getBackendLevel() && $item->getState() < $item->getBackendLevel()) {
            $item->setFilterStatus('Backend level');
            return true;
        }

        return false;
    }

    /**
     * Función para mostrar los elementos del Dashboard
     *
     * @param EventInterface $item El elemento que contiene los datos.
     * @param bool $newItem Si es un nuevo elemento
     * @param bool $newItemUp Si es un nuevo elemento recuperado
     * @return EventInterface
     */
    private function getHtmlItems(EventInterface $item, $newItem = false, $newItemUp = false)
    {
        $lastStateTime = date("m-d-Y H:i:s", $item->getLastHardStateChange());
        $lastStateDuration = Util::timeElapsed(time() - $item->getLastHardStateChange());
        $lastCheckDuration = Util::timeElapsed(time() - $item->getLastCheck());
        $serviceDesc = ($item->getDisplayName()) ? $item->getDisplayName() : $item->getCheckCommand();
        $hostname = ($item->getHostDisplayName()) ? $item->getHostDisplayName() : $item->getDisplayName();
        $hostAlias = ($item->getHostAlias()) ? $item->getHostAlias() : (($item->getAlias()) ? $item->getAlias() : $hostname);
        $scheduled = ($item->getScheduledDowntimeDepth() >= 1 || $item->getHostScheduledDowntimeDepth() >= 1);
        $tdClass = '';
        $trClass = EventState::getStateClass($item);
        $statusName = EventState::getStateName($item);
        $link = null;

        if (($item->getHostLastTimeUnreachable() > $item->getHostLastTimeUp() && !$newItemUp) ||
            ($item->getLastTimeUnreachable() > $item->getLastCheck() && $item->getStateType() === 1)
        ) {
            $trClass = EventState::getStateClass($item, EventStateInterface::STATE_UNREACHABLE);
            $statusName = EventState::getStateName($item, EventStateInterface::STATE_UNREACHABLE);
        }

        if ($scheduled) {
            $trClass = EventState::getStateClass($item, EventStateInterface::STATE_SCHEDULED);
            $statusName = EventState::getStateName($item, EventStateInterface::STATE_SCHEDULED);
        }

        if ($newItem && !$item->isAcknowledged() && !$scheduled && !$newItemUp) {
            $tdClass = "new";
        } elseif ($newItemUp
            && time() - $item->getLastHardStateChange() <= Config::getConfig()->getNewItemTime() / 2
        ) {
            $trClass = EventState::getStateClass($item, EventStateInterface::STATE_RECOVER);
            $statusName = EventState::getStateName($item, EventStateInterface::STATE_RECOVER);
        } elseif ($item->isFlapping()) {
            $trClass = EventState::getStateClass($item, EventStateInterface::STATE_FLAPPING);
            $statusName = EventState::getStateName($item, EventStateInterface::STATE_FLAPPING);
        } elseif ($item->isAcknowledged()) {
            $trClass = EventState::getStateClass($item, EventStateInterface::STATE_ACKNOWLEDGED);
            $statusName = EventState::getStateName($item, EventStateInterface::STATE_ACKNOWLEDGED);
        }

        $line = '<tr class="item-data ' . $trClass . '" title="' . sprintf(Language::t('Estado %s desde %s'), $statusName, $lastStateTime) . '">' . PHP_EOL;
        $line .= '<td class="center">' . $statusName . '</td>';

        if (Config::getConfig()->isColLastcheck()) {
            $line .= '<td title="' . sprintf('%s : %s', Language::t('Último check'), $lastCheckDuration) . '" class="center ' . $tdClass . '">' . $lastStateDuration . '</td>' . PHP_EOL;
        }

        if (Config::getConfig()->isColHost()) {
            if (!is_null($link)) {
                $line .= '<td><a href="' . $link . '" target="blank" title="' . $hostname . '">' . $hostAlias . '</a></td>' . PHP_EOL;
            } else {
                $line .= '<td>' . $hostAlias . '</td>' . PHP_EOL;
            }
        }

        if (Config::getConfig()->isColStatusInfo()) {
            if ($item->getFilterStatus() === '' || $newItem) {
                $line .= '<td class="statusinfo">' . $item->getPluginOutput() . '</td>' . PHP_EOL;
            } else {
                $line .= '<td class="statusinfo">' . $item->getPluginOutput() . '<br>Filter: ' . $item->getFilterStatus() . '</td>' . PHP_EOL;
            }
        }

        if (Config::getConfig()->isColService()) {
            $line .= '<td class="center">' . $serviceDesc . '</td>' . PHP_EOL;
        }

        if (Config::getConfig()->isColBackend()) {
            $line .= '<td class="center">' . $item->getBackendAlias() . '</td>' . PHP_EOL;
        }

        $line .= '</tr>' . PHP_EOL;

        return $line;
    }

    /**
     * Devolver los eventos sin formato HTML
     *
     * @return array
     * @throws Exception
     */
    public function getRawEvents()
    {
        $rawEvents = array();

        // Obtener los avisos desde la monitorización
        foreach ($this->getBackends() as $Backend) {
            $rawEvents = array_merge($rawEvents, $Backend->getProblems());
        }

        if ($rawEvents === false) {
            throw new Exception('No hay datos desde el backend');
        }

        return $rawEvents;
    }

    /**
     * Devolver las paradas sin formato HTML
     *
     * @return array
     * @throws Exception
     */
    public function getRawDowntimes()
    {
        $downtimes = array();

        // Obtener los avisos desde la monitorización
        foreach ($this->getBackends() as $Backend) {
            $downtimes = array_merge($downtimes, $Backend->getScheduledDowntimesGroupped());
        }

        if ($downtimes === false) {
            throw new Exception('No hay datos desde el backend');
        }

        return $downtimes;
    }

    /**
     * @return \SMD\Backend\Event\DowntimeInterface[]
     */
    public function getDowntimes()
    {
        return $this->downtimes;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getDisplayedItems()
    {
        return $this->displayedItems;
    }

    /**
     * @param int $viewType
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * @param int $callType
     */
    public function setCallType($callType)
    {
        $this->callType = $callType;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}