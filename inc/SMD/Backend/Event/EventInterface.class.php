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
interface EventInterface
{
    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getState();

    /**
     * @param int $state
     */
    public function setState($state);

    /**
     * @return int
     */
    public function getStateType();

    /**
     * @param int $state_type
     */
    public function setStateType($state_type);

    /**
     * @return boolean
     */
    public function isAcknowledged();

    /**
     * @param boolean $acknowledged
     */
    public function setAcknowledged($acknowledged);

    /**
     * @return string
     */
    public function getHostDisplayName();

    /**
     * @param string $host_display_name
     */
    public function setHostDisplayName($host_display_name);

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param string $display_name
     */
    public function setDisplayName($display_name);

    /**
     * @return string
     */
    public function getCheckCommand();

    /**
     * @param string $check_command
     */
    public function setCheckCommand($check_command);

    /**
     * @return string
     */
    public function getPluginOutput();

    /**
     * @param string $plugin_output
     */
    public function setPluginOutput($plugin_output);

    /**
     * @return int
     */
    public function getLastCheck();

    /**
     * @param int $last_check
     */
    public function setLastCheck($last_check);

    /**
     * @return int
     */
    public function getLastTimeUp();

    /**
     * @param int $last_time_up
     */
    public function setLastTimeUp($last_time_up);

    /**
     * @return int
     */
    public function getLastTimeOk();

    /**
     * @param int $last_time_ok
     */
    public function setLastTimeOk($last_time_ok);

    /**
     * @return int
     */
    public function getLastTimeUnreachable();

    /**
     * @param int $last_time_unreachable
     */
    public function setLastTimeUnreachable($last_time_unreachable);

    /**
     * @return int
     */
    public function getLastHardStateChange();

    /**
     * @param int $last_hard_state_change
     */
    public function setLastHardStateChange($last_hard_state_change);

    /**
     * @return int
     */
    public function getLastHardState();

    /**
     * @param int $last_hard_state
     */
    public function setLastHardState($last_hard_state);

    /**
     * @return int
     */
    public function getLastTimeDown();

    /**
     * @param int $last_time_down
     */
    public function setLastTimeDown($last_time_down);

    /**
     * @return boolean
     */
    public function isActiveChecksEnabled();

    /**
     * @param boolean $active_checks_enabled
     */
    public function setActiveChecksEnabled($active_checks_enabled);

    /**
     * @return int
     */
    public function getScheduledDowntimeDepth();

    /**
     * @param int $scheduled_downtime_depth
     */
    public function setScheduledDowntimeDepth($scheduled_downtime_depth);

    /**
     * @return int
     */
    public function getCurrentAttempt();

    /**
     * @param int $current_attempt
     */
    public function setCurrentAttempt($current_attempt);

    /**
     * @return int
     */
    public function getMaxCheckAttempts();

    /**
     * @param int $max_check_attempts
     */
    public function setMaxCheckAttempts($max_check_attempts);

    /**
     * @return boolean
     */
    public function isFlapping();

    /**
     * @param boolean $is_flapping
     */
    public function setFlapping($is_flapping);

    /**
     * @return boolean
     */
    public function isNotificationsEnabled();

    /**
     * @param boolean $notifications_enabled
     */
    public function setNotificationsEnabled($notifications_enabled);

    /**
     * @return string
     */
    public function getHostAlias();

    /**
     * @param $hostAlias string
     */
    public function setHostAlias($hostAlias);

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @param $alias string
     */
    public function setAlias($alias);

    /**
     * @return int
     */
    public function getHostScheduledDowntimeDepth();

    /**
     * @param $time int
     */
    public function setHostScheduledDowntimeDepth($time);

    /**
     * @return int
     */
    public function getHostLastTimeUnreachable();

    /**
     * @param $time int
     */
    public function setHostLastTimeUnreachable($time);
    /**
     * @return int
     */
    public function getHostLastTimeUp();

    /**
     * @param $time int
     */
    public function setHostLastTimeUp($time);

    /**
     * @return int
     */
    public function getHostState();

    /**
     * @param $state int
     */
    public function setHostState($state);

    /**
     * @return string
     */
    public function getBackendAlias();

    /**
     * @param $alias string
     */
    public function setBackendAlias($alias);

    /**
     * @return string
     */
    public function getBackendUrl();

    /**
     * @param string $backendUrl
     */
    public function setBackendUrl($backendUrl);

    /**
     * @param $status string
     */
    public function setFilterStatus($status);

    /**
     * @return string
     */
    public function getFilterStatus();

    /**
     * @return int
     */
    public function getBackendLevel();

    /**
     * @param int $backendLevel
     */
    public function setBackendLevel($backendLevel);
}