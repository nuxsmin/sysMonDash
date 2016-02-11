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

namespace SMD\Core;

use SMD\Core\ConfigData;
use SMD\Core\Language;
use SMD\Core\Session;
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

        $msg = <<<EOD
<div id="result" class="error">{Language::t($e->getMessage())}</div>
EOD;
        return $msg;
    }
}