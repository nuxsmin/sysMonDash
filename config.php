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
use SMD\Core\ConfigBackend;
use SMD\Core\Init;
use SMD\Core\Language;
use SMD\Core\Session;
use SMD\Http\Request;
use SMD\Util\Util;

define('APP_ROOT', '.');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start();

$hash = Request::analyze('h');
$hashOk = ($hash === Session::getConfig()->getHash() || Session::getConfig()->getHash() === '');
$passOK = (sha1($hash) === (string)Session::getConfig()->getConfigPassword());

$i = 0;
$j = 0;
$k = 0;
$l = 0;
?>
<!DOCTYPE html>
<html>
<head xmlns="http://www.w3.org/1999/html">
    <meta charset="UTF-8">
    <title><?php echo Language::t(Config::getConfig()->getPageTitle()); ?></title>
    <meta name="author" content="Rubén Domínguez">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="imgs/logo_small.png">
    <link rel="stylesheet" type="text/css" href="css/reset.min.css">
    <link rel="stylesheet" type="text/css" href="css/pure-min.css">
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="css/alertify.min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.min.css?v=<?php echo Session::getCssHash(); ?>">
    <link rel="stylesheet" type="text/css" href="css/config.min.css">
</head>
<body>
<div id="logo">
    <img src="imgs/logo.png"/>
    <div id="hora"><h1></h1></div>
    <div id="titulo">
        <h1><?php echo Language::t('Panel Monitorización'); ?></h1>
        <h2><?php echo Language::t('Dpto. Sistemas'); ?></h2>
    </div>
</div>
<div id="wrap">
    <?php if ($hashOk || $passOK): ?>
        <?php if (Util::checkConfigFile()): ?>
            <form method="post" id="frmConfig" name="frmConfig" class="pure-form pure-form-aligned">
                <fieldset>
                    <legend>
                        <i class="fa fa-caret-up container-state" data-container="application-config-container"></i>
                        <?php echo Language::t('Aplicación'); ?>
                    </legend>
                    <div id="application-config-container" class="flex-wrapper" aria-expanded="true">
                        <div class="pure-control-group">
                            <label for="site_language"><?php echo Language::t('Idioma'); ?></label>
                            <select id="site_language" name="site_language"
                                    data-selected="<?php echo Config::getConfig()->getLanguage(); ?>">
                                <option value="es_ES">Español</option>
                                <option value="en_US">English</option>
                            </select>
                        </div class="pure-control-group">
                        <div class="pure-control-group">
                            <label for="site_title"><?php echo Language::t('Título del sitio'); ?></label>
                            <input type="text" id="site_title" name="site_title" class="pure-input-1-2"
                                   value="<?php echo Config::getConfig()->getPageTitle(); ?>"/>
                        </div>
                        <div class="pure-control-group">
                            <label for="event_refresh"><?php echo Language::t('Tiempo actualización (s)'); ?></label>
                            <input type="number" id="event_refresh" name="event_refresh" min="5" step="5"
                                   value="<?php echo Config::getConfig()->getRefreshValue(); ?>"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="event_new_item_time"><?php echo Language::t('Tiempo nuevo evento (s)'); ?></label>
                            <input type="number" id="event_new_item_time" name="event_new_item_time" min="60" step="60"
                                   value="<?php echo Config::getConfig()->getNewItemTime(); ?>"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="event_max_items"><?php echo Language::t('Número máximo de eventos a mostrar'); ?></label>
                            <input type="number" id="event_max_items" name="event_max_items" min="50"
                                   value="<?php echo Config::getConfig()->getMaxDisplayItems(); ?>"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="event_new_item_audio"><?php echo Language::t('Habilitar sonido en nuevos eventos'); ?></label>
                            <input type="checkbox" id="event_new_item_audio"
                                   name="event_new_item_audio" <?php echo (Config::getConfig()->isNewItemAudioEnabled()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label for="col_last_check"><?php echo Language::t('Mostrar hora de eventos'); ?></label>
                            <input type="checkbox" id="col_last_check"
                                   name="col_last_check" <?php echo (Config::getConfig()->isColLastcheck()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label for="col_host"><?php echo Language::t('Mostrar host de eventos'); ?></label>
                            <input type="checkbox" id="col_host"
                                   name="col_host" <?php echo (Config::getConfig()->isColHost()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label for="col_service"><?php echo Language::t('Mostrar servicio de eventos'); ?></label>
                            <input type="checkbox" id="col_service"
                                   name="col_service" <?php echo (Config::getConfig()->isColService()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label for="col_info"><?php echo Language::t('Mostrar info de eventos'); ?></label>
                            <input type="checkbox" id="col_info"
                                   name="col_info" <?php echo (Config::getConfig()->isColStatusInfo()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label for="col_backend"><?php echo Language::t('Mostrar nombre backend'); ?></label>
                            <input type="checkbox" id="col_backend"
                                   name="col_backend" <?php echo (Config::getConfig()->isColBackend()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="show_scheduled"><?php echo Language::t('Mostrar eventos programados'); ?></label>
                            <input type="checkbox" id="show_scheduled"
                                   name="show_scheduled" <?php echo (Config::getConfig()->isShowScheduled()) ? 'checked' : ''; ?>/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="regex_host_show"><?php echo Language::t('REGEX hosts visibles en inicio'); ?></label>
                            <input type="text" id="regex_host_show" name="regex_host_show" class="pure-input-1-2"
                                   value="<?php echo Config::getConfig()->getRegexHostShow(); ?>"
                                   placeholder="(SERVER-|VM-).*"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="regex_services_no_show"><?php echo Language::t('REGEX servicios ocultos en inicio'); ?></label>
                            <input type="text" id="regex_services_no_show" name="regex_services_no_show"
                                   class="pure-input-1-2"
                                   value="<?php echo Config::getConfig()->getRegexServiceNoShow(); ?>"
                                   placeholder="(PRINTER|OldServer).*"/>
                        </div>
                        <div class="pure-control-group">
                            <label for="critical_items"><?php echo Language::t('Elementos críticos'); ?></label>
                            <input type="text" id="critical_items" name="critical_items" class="pure-input-1-2"
                                   value="<?php echo implode(',', Config::getConfig()->getCriticalItems()); ?>"
                                   placeholder="Dataserver,MailServer,DBServer"/>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>
                        <i class="fa fa-caret-up container-state" data-container="backends-config-container"></i>
                        Backends
                    </legend>
                    <div id="backends-config-container" class="flex-wrapper" aria-expanded="true">
                        <div class="pure-menu pure-menu-horizontal">
                            <ul class="pure-menu-list">
                                <li class="pure-menu-item pure-menu-has-children pure-menu-allow-hover">
                                    <a href="#" id="menuLinkBackends" class="pure-menu-link pure-menu-selected">
                                        <i class="fa fa-plus-circle"></i>
                                        <?php echo Language::t('Añadir Backend'); ?>
                                    </a>
                                    <ul class="pure-menu-children">
                                        <li class="pure-menu-item">
                                            <a class="pure-menu-link" href="#" id="addLivestatusBackend">Livestatus</a>
                                        </li>
                                        <li class="pure-menu-item">
                                            <a class="pure-menu-link" href="#" id="addStatusBackend">Status</a>
                                        </li>
                                        <li class="pure-menu-item">
                                            <a class="pure-menu-link" href="#" id="addZabbixBackend">Zabbix</a>
                                        </li>
                                        <li class="pure-menu-item">
                                            <a class="pure-menu-link" href="#" id="addSMDBackend">sysMonDash</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <?php foreach (Config::getConfig()->getBackend() as $Backend): ?>
                        <?php if ($Backend->getType() === ConfigBackend::TYPE_STATUS): ?>
                            <div class="backendStatus backendConfig">
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Alias'); ?></label>
                                    <input type="text" name="backend[status][<?php echo $i; ?>][alias]"
                                           class="pure-input-1-2"
                                           value="<?php echo $Backend->getAlias(); ?>"/>
                                </div>
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Ruta archivo status.dat'); ?></label>
                                    <input type="text"
                                           name="backend[status][<?php echo $i; ?>][path]"
                                           class="pure-input-1-2 backend_status_file"
                                           value="<?php echo $Backend->getPath(); ?>"
                                           placeholder="/var/lib/icinga/status.dat"/>
                                </div>
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Activo'); ?></label>
                                    <input type="checkbox"
                                           name="backend[status][<?php echo $i; ?>][active]" <?php echo ($Backend->isActive()) ? 'checked' : ''; ?>/>
                                </div>
                                <div class="buttons">
                                    <button type="button" class="button-error pure-button backendDelete">
                                        <i class="fa fa-minus-circle"></i>
                                        <?php echo Language::t('Eliminar'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php $i++; ?>
                        <?php elseif ($Backend->getType() === ConfigBackend::TYPE_LIVESTATUS): ?>
                            <div class="backendLivestatus backendConfig">
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Alias'); ?></label>
                                    <input type="text" name="backend[livestatus][<?php echo $j; ?>][alias]"
                                           class="pure-input-1-2"
                                           value="<?php echo $Backend->getAlias(); ?>"/>
                                </div>
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Ruta socket livestatus'); ?></label>
                                    <input type="text"
                                           name="backend[livestatus][<?php echo $j; ?>][path]"
                                           class="pure-input-1-2 backend_livestatus_file"
                                           value="<?php echo $Backend->getPath(); ?>"
                                           placeholder="/var/lib/icinga/rw/live"/>
                                </div>
                                <div class="pure-control-group">
                                    <label><?php echo Language::t('Activo'); ?></label>
                                    <input type="checkbox"
                                           name="backend[livestatus][<?php echo $j; ?>][active]" <?php echo ($Backend->isActive()) ? 'checked' : ''; ?>/>
                                </div>
                                <div class="buttons">
                                    <button type="button" class="button-error pure-button backendDelete">
                                        <i class="fa fa-minus-circle"></i>
                                        <?php echo Language::t('Eliminar'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php $j++; ?>
                        <?php elseif ($Backend->getType() === ConfigBackend::TYPE_ZABBIX): ?>
                        <div class="backendZabbix backendConfig">
                            <div class="pure-control-group">
                                <label><?php echo Language::t('Alias'); ?></label>
                                <input type="text" name="backend[zabbix][<?php echo $k; ?>][alias]"
                                       class="pure-input-1-2"
                                       value="<?php echo $Backend->getAlias(); ?>"/>
                            </div>
                            <div class="pure-control-group">
                                <label><?php echo Language::t('URL API de Zabbix'); ?></label>
                                <input type="text"
                                       name="backend[zabbix][<?php echo $k; ?>][url]"
                                       class="pure-input-1-2 backend_zabbix_url"
                                       value="<?php echo $Backend->getUrl(); ?>"
                                       placeholder="http://foo.bar/zabbix/api_jsonrpc.php"/>
                            </div>
                            <div class="pure-control-group">
                                <label><?php echo Language::t('Versión API de Zabbix'); ?></label>
                                <select class="backend_zabbix_version"
                                        name="backend[zabbix][<?php echo $k; ?>][version]"
                                        data-selected="<?php echo $Backend->getVersion(); ?>">
                                    <option value="220">2.2</option>
                                    <option value="240">2.4</option>
                                </select>
                            </div>
                            <div class="pure-control-group">
                                <label><?php echo Language::t('Usuario API de Zabbix'); ?></label>
                                <input type="text" class="backend_zabbix_user"
                                       name="backend[zabbix][<?php echo $k; ?>][user]"
                                       value="<?php echo $Backend->getUser(); ?>"/>
                            </div>
                            <div class="pure-control-group">
                                <label><?php echo Language::t('Clave API de Zabbix'); ?></label>
                                <input type="password" class="backend_zabbix_pass"
                                       name="backend[zabbix][<?php echo $k; ?>][pass]"
                                       value="<?php echo $Backend->getPass(); ?>"/>
                            </div>
                            <div class="pure-control-group">
                                <label><?php echo Language::t('Activo'); ?></label>
                                <input type="checkbox"
                                       name="backend[zabbix][<?php echo $k; ?>][active]" <?php echo ($Backend->isActive()) ? 'checked' : ''; ?>/>
                            </div>
                            <div class="buttons">
                                <button type="button" class="button-secondary pure-button backendCheckZabbix">
                                    <i class="fa fa-check-circle"></i>
                                    <?php echo Language::t('Comprobar'); ?>
                                </button>
                                <button type="button" class="button-error pure-button backendDelete">
                                    <i class="fa fa-minus-circle"></i>
                                    <?php echo Language::t('Eliminar'); ?>
                                </button>
                            </div>
                            <?php $k++; ?>
                            <?php elseif ($Backend->getType() === ConfigBackend::TYPE_SMD): ?>
                                <div class="backendSMD backendConfig">
                                    <div class="pure-control-group">
                                        <label><?php echo Language::t('Alias'); ?></label>
                                        <input type="text" name="backend[smd][<?php echo $l; ?>][alias]"
                                               class="pure-input-1-2"
                                               value="<?php echo $Backend->getAlias(); ?>"/>
                                    </div>
                                    <div class="pure-control-group">
                                        <label><?php echo Language::t('URL API sysMonDash'); ?></label>
                                        <input type="text"
                                               name="backend[smd][<?php echo $l; ?>][url]"
                                               class="pure-input-1-2 backend_smd_url"
                                               value="<?php echo $Backend->getUrl(); ?>"
                                               placeholder="http://foo.bar/sysMonDash/api.php"/>
                                    </div>
                                    <div class="pure-control-group">
                                        <label><?php echo Language::t('Token'); ?></label>
                                        <input type="text" name="backend[smd][<?php echo $l; ?>][token]"
                                               class="pure-input-1-2 backend_smd_token"
                                               value="<?php echo $Backend->getToken(); ?>"/>
                                    </div>
                                    <div class="pure-control-group">
                                        <label><?php echo Language::t('Activo'); ?></label>
                                        <input type="checkbox"
                                               name="backend[smd][<?php echo $l; ?>][active]" <?php echo ($Backend->isActive()) ? 'checked' : ''; ?>/>
                                    </div>
                                    <div class="buttons">
                                        <button type="button" class="button-secondary pure-button backendCheckSMD">
                                            <i class="fa fa-check-circle"></i>
                                            <?php echo Language::t('Comprobar'); ?>
                                        </button>
                                        <button type="button" class="button-error pure-button backendDelete">
                                            <i class="fa fa-minus-circle"></i>
                                            <?php echo Language::t('Eliminar'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?php $l++; ?>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                </fieldset>
                <fieldset>
                    <legend>
                        <i class="fa fa-caret-up container-state" data-container="special-config-container"></i>
                        <?php echo Language::t('Especial'); ?>
                    </legend>
                    <div id="special-config-container" class="flex-wrapper" aria-expanded="true">
                        <div class="pure-control-group">
                            <label for="special_client_url"><?php echo Language::t('URL del cliente'); ?></label>
                            <input type="text" id="special_client_url" name="special_client_url"
                                   value="<?php echo Config::getConfig()->getClientURL(); ?>"
                                   placeholder="http://myclient.foo.bar"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="special_remote_server_url"><?php echo Language::t('URL del servidor remoto'); ?></label>
                            <input type="text" id="special_remote_server_url" name="special_remote_server_url"
                                   value="<?php echo Config::getConfig()->getRemoteServer(); ?>"
                                   placeholder="http://server.foo.bar/sysMonDash"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="special_monitor_server_url"><?php echo Language::t('URL del servidor de monitorización'); ?></label>
                            <input type="text" id="special_monitor_server_url" name="special_monitor_server_url"
                                   value="<?php echo Config::getConfig()->getMonitorServerUrl(); ?>"
                                   placeholder="http://cloud.foo.bar/icinga"/>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="special_api_token"><?php echo Language::t('Token API'); ?></label>
                            <input type="text" id="special_api_token" name="special_api_token"
                                   value="<?php echo Config::getConfig()->getAPIToken(); ?>"
                                   placeholder=""/>
                            <button class="btn-gen-token pure-button" type="button"
                                    title="<?php echo Language::t('Generar Token'); ?>"
                                    data-dst="special_api_token">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div>
                        <div class="pure-control-group">
                            <label
                                for="special_config_pass"><?php echo Language::t('Clave de configuración'); ?></label>
                            <input type="password" id="special_config_pass" name="special_config_pass"
                                   value="<?php echo Session::getConfig()->getConfigPassword(); ?>"
                                   placeholder=""/>
                            <button class="btn-gen-pass pure-button" type="button"
                                    title="<?php echo Language::t('Generar Clave'); ?>"
                                    data-dst="special_config_pass">
                                <i class="fa fa-refresh"></i>
                            </button>
                            <i class="fa fa-eye" aria-hidden="true" title=""></i>
                        </div>
                    </div>
                </fieldset>

                <div class="buttons">
                    <button type="button" id="btnBack"
                            class="pure-button button-secondary">
                        <i class="fa fa-chevron-left fa-lg"></i>
                        <?php echo Language::t('Volver'); ?>
                    </button>
                    <button type="submit"
                            class="button-success pure-button pure-button-primary">
                        <i class="fa fa-floppy-o fa-lg"></i>
                        <?php echo Language::t('Guardar'); ?>
                    </button>
                </div>

                <input type="hidden" name="hash" value="<?php echo ($passOK) ? Session::getConfig()->getConfigPassword() : $hash; ?>"/>
            </form>

            <div id="result">&nbsp;</div>

            <div class="livestatusTemplate backendTemplate" style="display: none">
                <div class="pure-control-group">
                    <label><?php echo Language::t('Alias'); ?></label>
                    <input type="text" name="backend[livestatus][alias]"
                           class="pure-input-1-2" placeholder=""/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Ruta socket livestatus'); ?></label>
                    <input type="text" name="backend[livestatus][path]"
                           class="pure-input-1-2 backend_livestatus_file" placeholder="/var/lib/icinga/rw/live"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Activo'); ?></label>
                    <input type="checkbox" name="backend[livestatus][active]"/>
                </div>
                <div class="buttons">
                    <button type="button" class="button-error pure-button backendDelete">
                        <i class="fa fa-minus-circle"></i>
                        <?php echo Language::t('Eliminar'); ?>
                    </button>
                </div>
            </div>
            <div class="statusTemplate backendTemplate" style="display: none">
                <div class="pure-control-group">
                    <label><?php echo Language::t('Alias'); ?></label>
                    <input type="text" name="backend[status][alias]"
                           class="pure-input-1-2" placeholder=""/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Ruta archivo status.dat'); ?></label>
                    <input type="text" name="backend[status][path]"
                           class="pure-input-1-2 backend_status_file" placeholder="/var/lib/icinga/status.dat"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Activo'); ?></label>
                    <input type="checkbox" name="backend[status][active]"/>
                </div>
                <div class="buttons">
                    <button type="button" class="button-error pure-button backendDelete">
                        <i class="fa fa-trash"></i>
                        <?php echo Language::t('Eliminar'); ?>
                    </button>
                </div>
            </div>
            <div class="zabbixTemplate backendTemplate" style="display: none">
                <div class="pure-control-group">
                    <label><?php echo Language::t('Alias'); ?></label>
                    <input type="text" name="backend[zabbix][alias]"
                           class="pure-input-1-2" placeholder=""/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('URL API de Zabbix'); ?></label>
                    <input type="text" name="backend[zabbix][url]" class="pure-input-1-2 backend_zabbix_url"
                           placeholder="http://foo.bar/zabbix/api_jsonrpc.php"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Versión API de Zabbix'); ?></label>
                    <select name="backend[zabbix][version]">
                        <option value="220">2.2</option>
                        <option value="240">2.4</option>
                    </select>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Usuario API de Zabbix'); ?></label>
                    <input type="text" name="backend[zabbix][user]" class="backend_zabbix_user"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Clave API de Zabbix'); ?></label>
                    <input type="password" name="backend[zabbix][pass]" class="backend_zabbix_pass"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Activo'); ?></label>
                    <input type="checkbox" name="backend[zabbix][active]"/>
                </div>
                <div class="buttons">
                    <button type="button" class="button-error pure-button backendDelete">
                        <i class="fa fa-minus-circle"></i>
                        <?php echo Language::t('Eliminar'); ?>
                    </button>
                </div>
            </div>
            <div class="SMDTemplate backendTemplate" style="display: none">
                <div class="pure-control-group">
                    <label><?php echo Language::t('Alias'); ?></label>
                    <input type="text" name="backend[smd][alias]"
                           class="pure-input-1-2" placeholder=""/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('URL API sysMonDash'); ?></label>
                    <input type="text" name="backend[smd][url]"
                           class="pure-input-1-2 backend_smd_url" placeholder="http://foo.bar/sysMonDash/api.php"/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Token'); ?></label>
                    <input type="text" name="backend[smd][token]"
                           class="pure-input-1-2 backend_smd_token" placeholder=""/>
                </div>
                <div class="pure-control-group">
                    <label><?php echo Language::t('Activo'); ?></label>
                    <input type="checkbox" name="backend[smd][active]"/>
                </div>
                <div class="buttons">
                    <button type="button" class="button-secondary pure-button backendCheckSMD">
                        <i class="fa fa-check-circle"></i>
                        <?php echo Language::t('Comprobar'); ?>
                    </button>
                    <button type="button" class="button-error pure-button backendDelete">
                        <i class="fa fa-minus-circle"></i>
                        <?php echo Language::t('Eliminar'); ?>
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div id="result" class="error">
                <?php echo Language::t('El archivo de configuración no se puede escribir'); ?>
                <p><?php echo XML_CONFIG_FILE; ?></p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <form method="post" action="config.php" id="frmHash" name="frmHash" class="pure-form">
            <fieldset>
                <legend><?php echo Language::t('Configuración'); ?></legend>
                <label for="hash"><?php echo Language::t('Hash de configuración'); ?></label>
                <input type="password" id="hash" name="h" class="pure-input-1-2" required/>
                <button type="submit"
                        class="pure-button pure-button-primary"><?php echo Language::t('Comprobar'); ?></button>
                <button type="button" id="btnBack"
                        class="pure-button pure-button-primary"><?php echo Language::t('Volver'); ?></button>
            </fieldset>
        </form>
    <?php endif; ?>
    <div id="help">
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        <?php printf(Language::t('Más información en %s'), Util::getAppInfo('appWiki')); ?>
    </div>
</div>

<footer>
    <div id="project">
        <span id="updates"></span>
        <?php printf('%s :: %s :: %s', Util::getAppInfo('appVersion'), Util::getAppInfo('appCode'), Util::getAppInfo('appAuthor')); ?>
    </div>
</footer>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/alertify.min.js"></script>
<script type="text/javascript" src="js/functions.min.js"></script>
<script>
    (function () {
        config.setLang('<?php echo Language::t('Seguro?'); ?>');
        config.setLang('<?php echo Language::t('Conexión correcta'); ?>');
        config.setLang('<?php echo Language::t('Respuesta:'); ?>');
        config.setLang('<?php echo Language::t('Error de conexión'); ?>');
        config.setLang('<?php echo Language::t('URL no indicada'); ?>');
        smd.setConfig(config);
        smd.setConfigHooks();
    }());
</script>
</body>
</html>
