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

use SMD\Api\Api;
use SMD\Core\Init;
use SMD\Http\Request;
use SMD\Http\Response;
use SMD\Util\Json;

define('APP_ROOT', '.');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start();

$apiToken = Request::analyze('token');
$action = Request::analyze('action', 0);

$Api = new Api();

if (!$Api->checkToken($apiToken)){
    Response::printJSON('Token inválido');
}

$data = null;

switch ($action){
    case Api::ACTION_EVENTS:
        $data = $Api->getEvents();
        break;
    case Api::ACTION_DOWNTIMES:
        $data = $Api->getDowntimes();
        break;
    default:
        Response::printJSON('Acción inválida');
}

header('Content-type: application/json');
$json = array(
    'status' => 0,
    'data' => $data,
    'action' => $action
);

die(Json::getJson($json));