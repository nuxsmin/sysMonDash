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

class Language
{
    /**
     * @var string
     */
    private static $_lang = '';

    /**
     * Traducir una cadena
     *
     * @param $string
     * @return mixed
     */
    public static function t($string)
    {
        self::$_lang = self::getGlobalLang();

        return (self::$_lang === 'es_ES') ? $string : self::getTranslation($string);
    }

    /**
     * Obtener la traducción desde la sesión o el archivo de idioma
     *
     * @param $string
     * @return mixed
     */
    private static function getTranslation($string)
    {
        $sessionLang = Session::getLanguage();

        if ($sessionLang === false
            && self::checkLangFile(self::$_lang)
        ) {
            include_once self::getLangFile(self::$_lang);

            if (isset($LANG)
                && is_array($LANG)
            ) {
                Session::setLanguage($LANG);

                return (isset($LANG[$string])) ? $LANG[$string] : $string;
            }

            return $string;
        }

        return (isset($sessionLang[$string])) ? $sessionLang[$string] : $string;
    }

    /**
     * Establece el lenguaje de la aplicación.
     * Esta función establece el lenguaje según esté definido en la configuración o en el navegador.
     */
    private static function getGlobalLang()
    {
        $language = Config::getConfig()->getLanguage();
        $browserLang = self::getBrowserLang();

        // Establecer a es_ES si no existe la traducción o no está establecido el lenguaje
        if (!empty($language)
            && ((preg_match('/^es_.*/i', $browserLang)
            || !self::checkLangFile($browserLang)))
        ) {
            $lang = 'es_ES';
        } else {
            $lang = $browserLang;
        }

        return $lang;
    }

    /**
     * Devolver el lenguaje que acepta el navegador
     *
     * @return mixed
     */
    private static function getBrowserLang()
    {
        return str_replace("-", "_", substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5));
    }

    /**
     * Comprobar si el archivo de lenguaje existe
     *
     * @param string $lang El lenguaje a comprobar
     * @return bool
     */
    private static function checkLangFile($lang)
    {
        return file_exists(self::getLangFile($lang));
    }

    /**
     * Devolver el nombre del archivo de idioma
     *
     * @param $lang
     * @return string
     */
    private static function getLangFile($lang)
    {
        return LOCALES_PATH . DIRECTORY_SEPARATOR . "$lang.inc";
    }
}