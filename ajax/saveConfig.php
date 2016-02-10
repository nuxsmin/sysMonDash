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

use SMD\Core\Config;
use SMD\Core\ConfigData;
use SMD\Core\Session;
use SMD\Html\Html;
use SMD\Http\Request;
use SMD\Http\Response;
use SMD\Storage\XmlHandler;

define('APP_ROOT', '..');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

$hash = Request::analyze('hash');

if (!empty(Session::getConfig()->getHash()) && $hash !== Session::getConfig()->getHash()){
    Response::printJSON('Hash de configuración incorrecto');
}

$siteLanguage = Request::analyze('site_language');
$siteTitle = Request::analyze('site_title');
$eventRefresh = Request::analyze('event_refresh', 10);
$eventNewItemTime = Request::analyze('event_new_item_time', 900);
$eventMaxItems = Request::analyze('event_max_items', 200);
$showColLastCheck = Request::analyze('col_last_check', false, false, true);
$showColHost = Request::analyze('col_host', false, false, true);
$showColService = Request::analyze('col_service', false, false, true);
$showColInfo = Request::analyze('col_info', false, false, true);
$regexHostShow = Request::analyze('regex_host_show');
$regexServicesNoShow = Request::analyze('regex_services_no_show');
$criticalItems = Request::analyze('critical_items');
$specialClientURL = Request::analyze('special_client_url');
$specialRemoteServerURL = Request::analyze('special_remote_server_url');
$specialMonitorServerUrl = Request::analyze('special_monitor_server_url');

try {
    $Backends = Html::processFormBackends(Request::analyze('backend'));
} catch (Exception $e){
    Response::printJSON(\SMD\Core\Language::t($e->getMessage()));
}

//if (isset($backend['status'])){
//    if (empty($backendStatusFile)) {
//        Response::printJSON('Es necesaria la ruta al backend');
//    } elseif (!is_readable($backendStatusFile)) {
//        Response::printJSON('No es posible acceder al archivo del backend');
//    }
//} elseif ($backend === 'livetatus'){
//    if (empty($backendLivestatusFile)) {
//        Response::printJSON('Es necesaria la ruta al backend');
//    } elseif (!is_readable($backendLivestatusFile)) {
//        Response::printJSON('No es posible acceder al archivo del backend');
//    }
//} elseif ($backend === 'zabbix'){
//    if (empty($backendZabbixURL)) {
//        Response::printJSON('Es necesaria la URL del backend');
//    } elseif (empty($backendZabbixUser) || (empty(Config::getConfig()->getZabbixPass()) && empty($backendZabbixPass))) {
//        Response::printJSON('Es necesario el usuario y clave del backend');
//    }
//}

$ConfigData = new ConfigData();
$ConfigData->setLanguage($siteLanguage);
$ConfigData->setPageTitle($siteTitle);
$ConfigData->setRefreshValue($eventRefresh);
$ConfigData->setNewItemTime($eventNewItemTime);
$ConfigData->setMaxDisplayItems($eventMaxItems);
$ConfigData->setColLastcheck($showColLastCheck);
$ConfigData->setColHost($showColHost);
$ConfigData->setColService($showColService);
$ConfigData->setRegexHostShow($regexHostShow);
$ConfigData->setRegexServiceNoShow($regexServicesNoShow);
$ConfigData->setCriticalItems(explode(',', $criticalItems));
$ConfigData->setBackend($Backends);
$ConfigData->setClientURL($specialClientURL);
$ConfigData->setRemoteServer($specialRemoteServerURL);
$ConfigData->setMonitorServerUrl($specialMonitorServerUrl);

$zabbixPass = (empty($backendZabbixPass)) ? Config::getConfig()->getZabbixPass() : $backendZabbixPass;
$ConfigData->setZabbixPass($zabbixPass);

try {
    Config::saveConfig(new XmlHandler(XML_CONFIG_FILE), $ConfigData);
    Response::printJSON('Configuración guardada', 0);
} catch (Exception $e) {
    Response::printJSON('Error al guardar la configuración');
}