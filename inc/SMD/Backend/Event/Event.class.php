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

namespace SMD\Backend\Event;

/**
 * Class Event
 * @package SMD\Backend
 */
abstract class Event implements EventInterface
{
    const TYPE_HOST = 0;
    const TYPE_SERVICE = 1;
    const TYPE_TRIGGER = 2;
    const TYPE_SMD = 3;

    /**
     * @var int
     */
    public $type = 0;
    /**
     * @var int
     */
    public $state = 0;
    /**
     * @var int
     */
    public $stateType = 0;
    /**
     * @var bool
     */
    public $acknowledged = false;
    /**
     * @var string
     */
    public $hostDisplayName = '';
    /**
     * @var string
     */
    public $displayName = '';
    /**
     * @var string
     */
    public $checkCommand = '';
    /**
     * @var string
     */
    public $pluginOutput = '';
    /**
     * @var int
     */
    public $lastCheck = 0;
    /**
     * @var int
     */
    public $lastTimeUp = 0;
    /**
     * @var int
     */
    public $lastTimeOk = 0;
    /**
     * @var int
     */
    public $lastTimeUnreachable = 0;
    /**
     * @var int
     */
    public $lastHardStateChange = 0;
    /**
     * @var int
     */
    public $lastHardState = 0;
    /**
     * @var int
     */
    public $lastTimeDown = 0;
    /**
     * @var bool
     */
    public $activeChecksEnabled = false;
    /**
     * @var int
     */
    public $scheduledDowntimeDepth = 0;
    /**
     * @var int
     */
    public $currentAttempt = 0;
    /**
     * @var int
     */
    public $maxCheckAttempts = 0;
    /**
     * @var bool
     */
    public $flapping = false;
    /**
     * @var bool
     */
    public $notificationsEnabled = false;
    /**
     * @var string
     */
    public $hostAlias = '';
    /**
     * @var string
     */
    public $alias = '';
    /**
     * @var int
     */
    public $hostScheduledDowntimeDepth = 0;
    /**
     * @var int
     */
    public $hostLastTimeUnreachable = 0;
    /**
     * @var int
     */
    public $hostLastTimeUp = 0;
    /**
     * @var int
     */
    public $hostState = 0;
    /**
     * @var string
     */
    public $backendAlias = '';
    /**
     * @var string
     */
    public $backendUrl = '';
    /**
     * @var string
     */
    public $filterStatus = '';

    /**
     * Event constructor.
     * @param int $type
     */
    public function __construct($type)
    {
        $this->type = intval($type);
    }

    /**
     * @return string
     */
    public function getBackendAlias()
    {
        return $this->backendAlias;
    }

    /**
     * @param string $alias
     */
    public function setBackendAlias($alias)
    {
        $this->backendAlias = $alias;
    }

    /**
     * @return int
     */
    public function getHostLastTimeUp()
    {
        return $this->hostLastTimeUp;
    }

    /**
     * @param $time int
     */
    public function setHostLastTimeUp($time)
    {
        $this->hostLastTimeUp = intval($time);
    }

    /**
     * @return int
     */
    public function getHostLastTimeUnreachable()
    {
        return $this->hostLastTimeUnreachable;
    }

    /**
     * @param $time int
     */
    public function setHostLastTimeUnreachable($time)
    {
        $this->hostLastTimeUnreachable = intval($time);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = intval($type);
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = intval($state);
    }

    /**
     * @return int
     */
    public function getStateType()
    {
        return $this->stateType;
    }

    /**
     * @param int $stateType
     */
    public function setStateType($stateType)
    {
        $this->stateType = intval($stateType);
    }

    /**
     * @return boolean
     */
    public function isAcknowledged()
    {
        return $this->acknowledged;
    }

    /**
     * @param boolean $acknowledged
     */
    public function setAcknowledged($acknowledged)
    {
        $this->acknowledged = (bool)$acknowledged;
    }

    /**
     * @return string
     */
    public function getHostDisplayName()
    {
        return $this->hostDisplayName;
    }

    /**
     * @param string $hostDisplayName
     */
    public function setHostDisplayName($hostDisplayName)
    {
        $this->hostDisplayName = $hostDisplayName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getCheckCommand()
    {
        return $this->checkCommand;
    }

    /**
     * @param string $checkCommand
     */
    public function setCheckCommand($checkCommand)
    {
        $this->checkCommand = $checkCommand;
    }

    /**
     * @return string
     */
    public function getPluginOutput()
    {
        return $this->pluginOutput;
    }

    /**
     * @param string $pluginOutput
     */
    public function setPluginOutput($pluginOutput)
    {
        $this->pluginOutput = $pluginOutput;
    }

    /**
     * @return int
     */
    public function getLastCheck()
    {
        return $this->lastCheck;
    }

    /**
     * @param int $lastCheck
     */
    public function setLastCheck($lastCheck)
    {
        $this->lastCheck = intval($lastCheck);
    }

    /**
     * @return int
     */
    public function getLastTimeUp()
    {
        return $this->lastTimeUp;
    }

    /**
     * @param int $lastTimeUp
     */
    public function setLastTimeUp($lastTimeUp)
    {
        $this->lastTimeUp = intval($lastTimeUp);
    }

    /**
     * @return int
     */
    public function getLastTimeOk()
    {
        return $this->lastTimeOk;
    }

    /**
     * @param int $lastTimeOk
     */
    public function setLastTimeOk($lastTimeOk)
    {
        $this->lastTimeOk = intval($lastTimeOk);
    }

    /**
     * @return int
     */
    public function getLastTimeUnreachable()
    {
        return $this->lastTimeUnreachable;
    }

    /**
     * @param int $lastTimeUnreachable
     */
    public function setLastTimeUnreachable($lastTimeUnreachable)
    {
        $this->lastTimeUnreachable = intval($lastTimeUnreachable);
    }

    /**
     * @return int
     */
    public function getLastHardStateChange()
    {
        return $this->lastHardStateChange;
    }

    /**
     * @param int $lastHardStateChange
     */
    public function setLastHardStateChange($lastHardStateChange)
    {
        $this->lastHardStateChange = intval($lastHardStateChange);
    }

    /**
     * @return int
     */
    public function getLastHardState()
    {
        return $this->lastHardState;
    }

    /**
     * @param int $lastHardState
     */
    public function setLastHardState($lastHardState)
    {
        $this->lastHardState = intval($lastHardState);
    }

    /**
     * @return int
     */
    public function getLastTimeDown()
    {
        return $this->lastTimeDown;
    }

    /**
     * @param int $lastTimeDown
     */
    public function setLastTimeDown($lastTimeDown)
    {
        $this->lastTimeDown = intval($lastTimeDown);
    }

    /**
     * @return boolean
     */
    public function isActiveChecksEnabled()
    {
        return $this->activeChecksEnabled;
    }

    /**
     * @param boolean $activeChecksEnabled
     */
    public function setActiveChecksEnabled($activeChecksEnabled)
    {
        $this->activeChecksEnabled = (bool)$activeChecksEnabled;
    }

    /**
     * @return int
     */
    public function getScheduledDowntimeDepth()
    {
        return $this->scheduledDowntimeDepth;
    }

    /**
     * @param int $scheduledDowntimeDepth
     */
    public function setScheduledDowntimeDepth($scheduledDowntimeDepth)
    {
        $this->scheduledDowntimeDepth = intval($scheduledDowntimeDepth);
    }

    /**
     * @return int
     */
    public function getCurrentAttempt()
    {
        return $this->currentAttempt;
    }

    /**
     * @param int $currentAttempt
     */
    public function setCurrentAttempt($currentAttempt)
    {
        $this->currentAttempt = intval($currentAttempt);
    }

    /**
     * @return int
     */
    public function getMaxCheckAttempts()
    {
        return $this->maxCheckAttempts;
    }

    /**
     * @param int $maxCheckAttempts
     */
    public function setMaxCheckAttempts($maxCheckAttempts)
    {
        $this->maxCheckAttempts = intval($maxCheckAttempts);
    }

    /**
     * @return boolean
     */
    public function isFlapping()
    {
        return $this->flapping;
    }

    /**
     * @param boolean $flapping
     */
    public function setFlapping($flapping)
    {
        $this->flapping = (bool)$flapping;
    }

    /**
     * @return boolean
     */
    public function isNotificationsEnabled()
    {
        return $this->notificationsEnabled;
    }

    /**
     * @param boolean $notificationsEnabled
     */
    public function setNotificationsEnabled($notificationsEnabled)
    {
        $this->notificationsEnabled = (bool)$notificationsEnabled;
    }

    /**
     * @return string
     */
    public function getHostAlias()
    {
        return $this->hostAlias;
    }

    /**
     * @param $hostAlias string
     */
    public function setHostAlias($hostAlias)
    {
        $this->hostAlias = $hostAlias;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param $alias string
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getHostScheduledDowntimeDepth()
    {
        return $this->hostScheduledDowntimeDepth;
    }

    /**
     * @param $time int
     */
    public function setHostScheduledDowntimeDepth($time)
    {
        $this->hostScheduledDowntimeDepth = intval($time);
    }

    /**
     * @return int
     */
    public function getHostState()
    {
        return $this->hostState;
    }

    /**
     * @param $state int
     */
    public function setHostState($state)
    {
        $this->hostState = intval($state);
    }

    /**
     * @return string
     */
    public function getBackendUrl()
    {
        return $this->backendUrl;
    }

    /**
     * @param string $backendUrl
     */
    public function setBackendUrl($backendUrl)
    {
        $this->backendUrl = $backendUrl;
    }

    /**
     * @param $status string
     */
    public function setFilterStatus($status)
    {
        $this->filterStatus = $status;
    }

    /**
     * @return string
     */
    public function getFilterStatus()
    {
        return $this->filterStatus;
    }
}