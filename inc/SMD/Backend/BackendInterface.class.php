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
     * Devuelve los eventos
     *
     * @return array|bool
     */
    public function getProblems();

    /**
     * Devuelve los eventos de los hosts
     *
     * @return array|bool
     */
    public function getHostsProblems();

    /**
     * Devuelve los eventos de los servicios
     *
     * @return array|bool
     */
    public function getServicesProblems();

    /**
     * Devuelve los eventos programados
     *
     * @return array|bool
     */
    public function getScheduledDowntimes();

    /**
     * Devuelve los eventos programados agrupados
     *
     * @return array|bool
     */
    public function getScheduledDowntimesGroupped();

    /**
     * Si se deben de devolver todas las cabeceras de los eventos
     *
     * @return bool
     */
    public function isAllHeaders();

    /**
     * Devolver todas las cabeceras de los eventos
     *
     * @param $allHeaders
     */
    public function setAllHeaders($allHeaders);
}