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

use SMD\Core\Config;
use SMD\Core\Init;
use SMD\Core\Language;
use SMD\Core\Session;
use SMD\Http\Request;
use SMD\Util\Util;

define('APP_ROOT', '.');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start();

$type = Request::analyze('t', VIEW_FRONTLINE);
$timeout = Request::analyze('to', Config::getConfig()->getRefreshValue());
$scroll = Request::analyze('scroll', ($type === VIEW_FRONTLINE || $type === VIEW_DISPLAY) ? 1 : 0);

$ajaxFile = '/ajax/getData.php?t=' . $type . '&to=' . $timeout;

Session::setCssHash(Util::getCssHash());
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>sysMonDash :: <?php echo Config::getConfig()->getPageTitle(); ?></title>
    <meta name="author" content="Rubén Domínguez">
    <link rel="icon" type="image/png" href="imgs/logo_small.png">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/pure-min.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css?v=<?php echo Session::getCssHash(); ?>">
</head>
<body>
<div id="logo">
    <img src="imgs/logo.png"/>
    <div id="hora"><h1></h1></div>
    <div id="titulo">
        <h1><?php echo Config::getConfig()->getPageTitle(); ?></h1>
        <h2><?php echo Language::t('Dpto. Sistemas'); ?></h2>
    </div>
</div>

<div id="monitor-data"></div>

<footer>
    <div id="project">
        <span id="updates"></span>
        <?php echo Util::getAppInfo('appVersion'), ' :: ', Util::getAppInfo('appCode'), ' :: ', Util::getAppInfo('appAuthor'); ?>
    </div>
</footer>

<script type="text/javascript" src="js/functions.js"></script>
<script type="text/javascript">
    (function () {
        config.setRemoteServer('<?php echo Config::getConfig()->getRemoteServer(); ?>');
        config.setAjaxFile('<?php echo $ajaxFile; ?>');
        config.setScroll(<?php echo ($scroll) ? 'true' : 'false'; ?>);
        config.setTimeout(<?php echo $timeout; ?>);
        config.setLang('<?php echo Language::t('Error al obtener los eventos de monitorización'); ?>');

        smd.setConfig(config);
        smd.startSMD();
        smd.getUpdates();
    }());
</script>
</body>
</html>