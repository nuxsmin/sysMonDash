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

defined('APP_ROOT') || die(_('No es posible acceder directamente a este archivo'));

/**
 * Clase para manejar la variable de sesion
 */
class Session
{
    /**
     * Tipos de sesión
     */
    const SESSION_INTERACTIVE = 1;

    /**
     * Devolver una variable de sesión
     *
     * @param mixed $key
     * @param mixed $default
     * @return bool|int
     */
    public static function getSessionKey($key, $default = '')
    {
        if (isset($_SESSION[$key])) {
            if (is_numeric($default)) {
                return (int)$_SESSION[$key];
            }
            return $_SESSION[$key];
        }

        return $default;
    }

    /**
     * Establecer una variable de sesión
     *
     * @param mixed $key   El nombre de la variable
     * @param mixed $value El valor de la variable
     */
    public static function setSessionKey($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Establece el array de idioma
     *
     * @param $lang
     */
    public static function setLanguage(array $lang)
    {
        self::setSessionKey('language', $lang);
    }

    /**
     * Devlver el array de idioma
     *
     * @return bool|array
     */
    public static function getLanguage()
    {
        return self::getSessionKey('language', false);
    }

    /**
     * Establecer el hash del archivo CSS
     *
     * @param $hash
     */
    public static function setCssHash($hash)
    {
        self::setSessionKey('csshash', $hash);
    }

    /**
     * Devolver el hash del archivo CSS
     *
     * @return bool|string
     */
    public static function getCssHash()
    {
        return self::getSessionKey('csshash');
    }

    /**
     * Guardar la configuración
     *
     * @param $config
     */
    public static function setConfig(ConfigData $config)
    {
        self::setSessionKey('config', $config);
    }

    /**
     * Devolver la configuración
     *
     * @return ConfigData
     */
    public static function getConfig()
    {
        return self::getSessionKey('config');
    }

    /**
     * Establecer la hora de carga de la configuración
     *
     * @param $time int
     */
    public static function setConfigTime($time)
    {
        self::setSessionKey('configTime', $time);
    }

    /**
     * Devolver la hora de carga de la configuración
     *
     * @return int
     */
    public static function getConfigTime()
    {
        return self::getSessionKey('configTime', 0);
    }
}