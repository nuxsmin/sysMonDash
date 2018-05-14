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

use Exts\Zabbix\ZabbixApiLoader;
use SMD\Core\Init;
use SMD\Http\Request;
use SMD\Http\Response;
use SMD\Util\Util;

define('APP_ROOT', '..');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start();

$action = Request::analyze('action');
$data = Request::analyze('data');

if (!$action || !$data) {
    Response::printJSON('Petición inválida');
} else {
    $data = json_decode($data);

    if (json_last_error() !== 0) {
        Response::printJSON('Petición inválida');
    }
}

try {
    switch ($action) {
        case 'smdBackend':
            $url = $data->url . '?action=' . $data->action . '&token=' . $data->token;
            $json = Util::getDataFromUrl($url);
            break;
        case 'zabbixBackend':
            $ZabbixLoader = new ZabbixApiLoader();
            $Zabbix = $ZabbixLoader->getAPI($data->version);
            $Zabbix->setApiUrl($data->url);
            $Zabbix->userLogin(array('user' => $data->user, 'password' => $data->pass));
            $version = $Zabbix->apiinfoVersion();

            Response::printJSON('V ' . $version, 0);
            break;
        default:
            Response::printJSON('Petición inválida');
    }
} catch (Exception $e) {
    Response::printJSON($e->getMessage());
}


if (isset($json) && !empty($json)) {
    header('Content-type: application/json');
    exit($json);
}