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

namespace SMD\Util;

use SMD\Core\Config;
use SMD\Core\Exceptions\CurlException;
use SMD\Core\Language;
use SMD\Core\Session;

/**
 * Class Util
 * @package SMD\Util
 */
class Util
{
    /**
     * Ordenar un array por una clave dada.
     *
     * @param array $data El array a ordenar
     * @param string $fieldName La clave de ordenación
     * @param bool $sortAsc El orden de ordenación
     * @return mixed
     */
    public static function arraySortByKey(array &$data, $fieldName, $sortAsc = true)
    {
        // Ordenar el array multidimensional por la clave $fielName de mayor a menor
        usort($data, function ($a, $b) use ($fieldName, $sortAsc) {
            return $sortAsc ? ($a[$fieldName] < $b[$fieldName]) : ($a[$fieldName] > $b[$fieldName]);
        });

        return $data;
    }

    /**
     * Ordenar un array por una propiedad dada.
     *
     * @param array $data El array a ordenar
     * @param string $propertyName La clave de ordenación
     * @param bool $sortAsc El orden de ordenación
     * @return mixed
     */
    public static function arraySortByProperty(array &$data, $propertyName, $sortAsc = true)
    {
        // Ordenar el array multidimensional por la propiedad $propertyName de mayor a menor
        usort($data, function ($a, $b) use ($propertyName, $sortAsc) {
            return $sortAsc ? ($a->{$propertyName} < $b->{$propertyName}) : ($a->{$propertyName} > $b->{$propertyName});
        });

        return $data;
    }

    /**
     * Función para calcular el tiempo transcurrido
     *
     * @param int $secs El tiempo en formato UNIX
     * @return string Cadena con las hora:minutos:segundos
     */
    public static function timeElapsed($secs)
    {
        $bit = array(
//        'a' => $secs / 31556926 % 12,
//        'w' => $secs / 604800 % 52,
            'd' => abs($secs) / 86400 % 365,
            'h' => abs($secs) / 3600 % 24,
            'm' => abs($secs) / 60 % 60,
            's' => abs($secs) % 60
        );

        $ret = array();

        foreach ($bit as $k => $v) {
            if ($v > 0) {
                $ret[] = $v . $k;
            }
        }

        return implode(' ', $ret);
    }

    /**
     * Comprobar si es necesario reiniciar la página para actualizar
     * Este método es util cuando se tienen varios paneles de monitorización
     * y se requiere de actualizar el estilo visual.
     *
     * @return bool
     */
    public static function checkRefreshSession()
    {
        $version = (int)implode('', self::getVersion(true));

        if (self::getSessionActive()) {
            if (!isset($_SESSION['VERSION'])) {
                $_SESSION['VERSION'] = $version;
                Session::setCssHash(self::getCssHash());
            } elseif ($_SESSION['VERSION'] < $version) {
                $_SESSION['VERSION'] = $version;
                return true;
            }
        }

        return false;
    }

    /**
     * Devuelve la versión de sysMonDash
     *
     * @param bool $retBuild
     * @return array|int
     */
    public static function getVersion($retBuild = false)
    {
        $build = 2017041001;
        $version = array(1, 0);

        if ($retBuild) {
            $version[] = $build;
        }

        return $version;
    }

    /**
     * Devolver si la sesión está activa
     *
     * @return bool
     */
    public static function getSessionActive()
    {
        if (function_exists('\session_status')) {
            return (session_status() === PHP_SESSION_ACTIVE);
        }

        return (session_id() !== '');
    }

    /**
     * Devuelve el hash del archivo CSS
     *
     * @return string
     */
    public static function getCssHash()
    {
        return hash_file('md5', APP_ROOT . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'styles.css');
    }

    /**
     * Comprobar si el archivo se puede leer/escribir
     *
     * @return bool
     */
    public static function checkConfigFile()
    {
        if (!file_exists(XML_CONFIG_FILE)) {
            return touch(XML_CONFIG_FILE);
        }

        return is_writable(XML_CONFIG_FILE);
    }

    /**
     * Comprobar si se realiza una recarga de la página
     *
     * @return bool
     */
    public static function checkReload()
    {
        return (self::getRequestHeaders('Cache-Control') === 'max-age=0');
    }

    /**
     * Devolver las cabeceras enviadas desde el cliente.
     *
     * @param string $header nombre de la cabecera a devolver
     * @return array|string
     */
    public static function getRequestHeaders($header = '')
    {
        if (!function_exists('\apache_request_headers')) {
            $headers = self::getApacheHeaders();
        } else {
            $headers = apache_request_headers();
        }

        if (!empty($header) && array_key_exists($header, $headers)) {
            return $headers[$header];
        }

        if (!empty($header)) {
            return '';
        }

        return $headers;
    }

    /**
     * Función que sustituye a apache_request_headers
     *
     * @return array
     */
    public static function getApacheHeaders()
    {
        $headers = array();

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$key] = $value;
            } else {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Comprobar si hay actualizaciones de sysPass disponibles desde internet (github.com)
     * Esta función hace una petición a GitHub y parsea el JSON devuelto para verificar
     * si la aplicación está actualizada
     *
     * @return array|bool
     */
    public static function checkUpdates()
    {
        try {
            $data = self::getDataFromUrl(self::getAppInfo('appupdates'));
        } catch (\Exception $e) {
            return false;
        }

        $updateInfo = json_decode($data);

        // $updateInfo[0]->tag_name
        // $updateInfo[0]->name
        // $updateInfo[0]->body
        // $updateInfo[0]->tarball_url
        // $updateInfo[0]->zipball_url
        // $updateInfo[0]->published_at
        // $updateInfo[0]->html_url

        $version = $updateInfo->tag_name;
        $url = $updateInfo->html_url;
        $title = $updateInfo->name;
        $description = $updateInfo->body;
        $date = $updateInfo->published_at;

//        preg_match('/v?(\d+)\.(\d+)\.(\d+)\.(\d+)(\-[a-z0-9.]+)?$/', $version, $realVer);
        preg_match('/v?(\d+)\.(\d+)\.(\d+)(\-[a-z0-9.]+)?$/', $version, $realVer);

        if (is_array($realVer)) {
            $appVersion = implode('', self::getVersion(true));
            $pubVersion = $realVer[1] . $realVer[2] . $realVer[3];

            if ($pubVersion > $appVersion) {
                return array(
                    'version' => $version,
                    'url' => $url,
                    'title' => $title,
                    'description' => $description,
                    'date' => $date);
            }

            return true;
        }

        return false;
    }

    /**
     * Obtener datos desde una URL usando CURL
     *
     * @param           $url string La URL
     * @return bool|string
     * @throws \Exception
     */
    public static function getDataFromUrl($url)
    {
        if (!self::curlIsAvailable()) {
            error_log('cURL not available');

            throw new CurlException('cURL not available');
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "sysMonDash-App");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, Config::getConfig()->getRefreshValue() / 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode >= 400 && $httpCode < 600) {
            throw new CurlException(curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        if ($data === false
            || curl_errno($ch) > 0
        ) {
            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        return $data;
    }

    /**
     * Comprobar si el módulo CURL está instalado.
     *
     * @return bool
     */
    public static function curlIsAvailable()
    {
        return function_exists('curl_init');
    }

    /**
     * Info de la aplicación
     *
     * @param $index
     * @return string
     */
    public static function getAppInfo($index)
    {
        $appInfo = array(
            'appupdates' => 'https://api.github.com/repos/nuxsmin/sysMonDash/releases/latest',
            'appVersion' => 'v' . implode('.', self::getVersion()),
            'appCode' => '<a href="https://github.com/nuxsmin/sysMonDash" target="_blank" title="sysMonDash - GitHub">sysMonDash</a>',
            'appWiki' => '<a href="https://github.com/nuxsmin/sysMonDash/wiki" target="_blank" title="sysMonDash Wiki - GitHub">sysMonDash Wiki</a>',
            'appAuthor' => '<a href="http://cygnux.org" target="_blank" title="' . Language::t('Un proyecto de cygnux.org') . '">cygnux.org</a>',

        );

        return isset($appInfo[$index]) ? $appInfo[$index] : '';
    }

    /**
     * Comprobar si la configuración se ha actualizado
     * 
     * @return bool
     */
    public static function checkConfigRefresh()
    {
        return (time() - Session::getConfigTime() <= Config::getConfig()->getRefreshValue());
    }
}