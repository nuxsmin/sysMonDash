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

namespace SMD\Core;

use SMD\Storage\XmlHandler;

/**
 * Class Initpara inicialización de sysMonDash
 *
 * @package SMD\Core
 */
class Init
{
    /**
     * Inicializar
     * @param bool $loadConfig
     */
    public static function start($loadConfig = true)
    {
        try {
            self::checkPhpVersion();
            self::loadSession();

            if ($loadConfig) {
                self::loadConfig();
            }
        } catch (\Exception $e) {
            die(self::showError($e));
        }
    }

    /**
     * Inicializar la sesión
     *
     * @throws \Exception
     */
    public static function loadSession()
    {
        if (session_start() === false) {
            throw new \Exception('No es posible inicializar la sesión');
        }
    }

    /**
     * Cargar la configuración
     */
    private static function loadConfig()
    {
        try {
            Config::loadConfig(new XmlHandler(XML_CONFIG_FILE));
        } catch (\Exception $e) {
            error_log(Language::t($e->getMessage()));

            Session::setConfig(new ConfigData());

            if (self::getCurrentScript() !== 'config.php') {
                header('Location: config.php');
            }
        }
    }

    /**
     * Devolver el script actual
     *
     * @return string
     */
    public static function getCurrentScript()
    {
        return substr($_SERVER['SCRIPT_FILENAME'], strrpos($_SERVER['SCRIPT_FILENAME'], '/') + 1);
    }

    /**
     * @param \Exception $e
     * @return string
     */
    public static function showError(\Exception $e)
    {
        error_log(Language::t($e->getMessage()));

        return '<div id="result" class="error">' . Language::t($e->getMessage()) . '</div>';
    }

    /**
     * Comprobar la versión de PHP
     *
     * @throws \Exception
     */
    public static function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, '5.3.0') === -1) {
            throw new \Exception('Versión de PHP necesaria >= 5.3');
        }
    }
}