<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2014-2016 Rubén Domínguez nuxsmin@cygnux.org
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
use SMD\Storage\XmlHandler;

defined('APP_ROOT') || die(_('No es posible acceder directamente a este archivo'));

define('XML_CONFIG_FILE', DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'sysMonDash' . DIRECTORY_SEPARATOR . 'config.xml');
define('CONSTANTS_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'constants.php');
define('MODEL_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'SMD');
define('LOCALES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'locales');
define('CSS_PATH', __DIR__ . DIRECTORY_SEPARATOR . APP_ROOT . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'styles.css');

define('DEBUG', false);

// Empezar a calcular el tiempo y memoria utilizados
$time_start = microtime(true);
$memInit = memory_get_usage();
$file = substr($_SERVER['SCRIPT_FILENAME'], strrpos($_SERVER['SCRIPT_FILENAME'], '/') + 1);

require CONSTANTS_FILE;

require 'SplClassLoader.php';

$ClassLoader = new SplClassLoader();
$ClassLoader->setFileExtension('.class.php');
$ClassLoader->register();

session_start();

try {
    Config::loadConfig(new XmlHandler(XML_CONFIG_FILE));
} catch (\Exception $e) {
    error_log(\SMD\Core\Language::t($e->getMessage()));

    \SMD\Core\Session::setConfig(new \SMD\Core\ConfigData());

    if ($file !== 'config.php') {
        header('Location: config.php');
    }
}