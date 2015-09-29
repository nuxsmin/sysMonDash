<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2014-2015 Rubén Domínguez nuxsmin@cygnux.org
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

require 'sysMonDash.php';

$raw = (isset($_GET['raw']) && intval($_GET['raw']) === 1);
$allHeaders = (isset($_GET['allheaders']) && intval($_GET['allheaders']) === 1);

if ($raw) {
    echo '<pre>';
    echo 'Hosts', PHP_EOL;
    print_r(sysMonDash::sortByTime(sysMonDash::getHostsProblems($allHeaders), 'last_hard_state_change'));
    echo 'Servicios', PHP_EOL;
    print_r(sysMonDash::sortByTime(sysMonDash::getServicesProblems($allHeaders), 'last_hard_state_change'));
    echo 'Paradas', PHP_EOL;
    print_r(sysMonDash::sortByTime(sysMonDash::getScheduledDowntimes($allHeaders), 'start_time', false));
    echo '</pre>';
}
