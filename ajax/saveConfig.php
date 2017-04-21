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
use SMD\Core\Init;
use SMD\Core\Session;
use SMD\Html\Html;
use SMD\Http\Request;
use SMD\Http\Response;
use SMD\Storage\XmlHandler;

define('APP_ROOT', '..');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start(false);

$hash = Request::analyze('hash');

$hashOk = ($hash === Session::getConfig()->getHash() || Session::getConfig()->getHash() === '');
$passOK = ($hash === (string)Session::getConfig()->getConfigPassword());

if (!$hashOk && !$passOK) {
    Response::printJSON('Hash de configuración incorrecto');
}

$siteLanguage = Request::analyze('site_language');
$siteTitle = Request::analyze('site_title');
$eventRefresh = Request::analyze('event_refresh', 10);
$eventNewItemTime = Request::analyze('event_new_item_time', 900);
$eventMaxItems = Request::analyze('event_max_items', 200);
$eventNewItemAudio = Request::analyze('event_new_item_audio', false, false, true);
$showColLastCheck = Request::analyze('col_last_check', false, false, true);
$showColHost = Request::analyze('col_host', false, false, true);
$showColService = Request::analyze('col_service', false, false, true);
$showColInfo = Request::analyze('col_info', false, false, true);
$showColBackend = Request::analyze('col_backend', false, false, true);
$showScheduled = Request::analyze('show_scheduled', false, false, true);
$regexHostShow = Request::analyze('regex_host_show');
$regexServicesNoShow = Request::analyze('regex_services_no_show');
$criticalItems = Request::analyze('critical_items');
$specialClientURL = Request::analyze('special_client_url');
$specialRemoteServerURL = Request::analyze('special_remote_server_url');
$specialMonitorServerUrl = Request::analyze('special_monitor_server_url');
$specialAPIToken = Request::analyze('special_api_token');
$specialConfigPass = Request::analyze('special_config_pass');

try {
    $Backends = Html::processFormBackends(Request::analyze('backend'));
} catch (Exception $e) {
    Response::printJSON(\SMD\Core\Language::t($e->getMessage()));
}

$ConfigData = new ConfigData();
$ConfigData->setLanguage($siteLanguage);
$ConfigData->setPageTitle($siteTitle);
$ConfigData->setRefreshValue($eventRefresh);
$ConfigData->setNewItemTime($eventNewItemTime);
$ConfigData->setMaxDisplayItems($eventMaxItems);
$ConfigData->setNewItemAudioEnabled($eventNewItemAudio);
$ConfigData->setColLastcheck($showColLastCheck);
$ConfigData->setColHost($showColHost);
$ConfigData->setColService($showColService);
$ConfigData->setColStatusInfo($showColInfo);
$ConfigData->setColBackend($showColBackend);
$ConfigData->setShowScheduled($showScheduled);
$ConfigData->setRegexHostShow($regexHostShow);
$ConfigData->setRegexServiceNoShow($regexServicesNoShow);
$ConfigData->setCriticalItems(!empty($criticalItems) ? explode(',', $criticalItems) : []);
$ConfigData->setBackend($Backends);
$ConfigData->setClientURL($specialClientURL);
$ConfigData->setRemoteServer($specialRemoteServerURL);
$ConfigData->setMonitorServerUrl($specialMonitorServerUrl);
$ConfigData->setAPIToken($specialAPIToken);

if (!empty($specialConfigPass)
    && $specialConfigPass !== (string)Session::getConfig()->getConfigPassword()
) {
    $ConfigData->setConfigPassword(sha1($specialConfigPass));
} else {
    $ConfigData->setConfigPassword($specialConfigPass);
}

try {
    Config::saveConfig(new XmlHandler(XML_CONFIG_FILE), $ConfigData);
    Response::printJSON('Configuración guardada', 0);
} catch (Exception $e) {
    Response::printJSON('Error al guardar la configuración');
}