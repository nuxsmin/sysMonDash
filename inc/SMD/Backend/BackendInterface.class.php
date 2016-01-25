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

interface BackendInterface
{
    /**
     * @return mixed
     */
    public function getHostsProblems();

    /**
     * @return mixed
     */
    public function getServicesProblems();

    /**
     * @return mixed
     */
    public function getScheduledDowntimes();

    /**
     * @return mixed
     */
    public function getScheduledDowntimesGroupped();

    /**
     * @return bool
     */
    public function isAllHeaders();

    /**
     * @param $allHeaders
     */
    public function setAllHeaders($allHeaders);
}