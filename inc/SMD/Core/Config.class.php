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
use ReflectionObject;
use SMD\Storage\StorageInterface;
use SMD\Util\Util;

/**
 * Class Config
 * @package SMD\Core
 */
class Config
{
    /**
     * Tiempo de espera para refrescar
     */
    const TIMEOUT_REFRESH = 3600;

    /**
     * Obtener la configuración o devolver una nueva
     *
     * @return ConfigData
     */
    public static function getConfig()
    {
        $Config = Session::getConfig();

        return (gettype($Config) === 'object') ? $Config : new ConfigData();
    }

    /**
     * Cargar la configuración desde el archivo
     *
     * @param StorageInterface $Storage
     */
    public static function loadConfig(StorageInterface $Storage)
    {
        if (Util::checkReload()
            || gettype(Session::getConfig()) !== 'object'
            || time() >= (Session::getConfigTime() + self::TIMEOUT_REFRESH)
        ) {
            Session::setConfig(self::arrayMapper($Storage));
            Session::setConfigTime(time());
        }
    }

    /**
     * @param StorageInterface $Storage
     * @param ConfigData $Config
     */
    public static function saveConfig(StorageInterface $Storage, ConfigData $Config = null)
    {
        if (is_null($Config)){
            $Storage->setItems(self::getConfig());
        } else {
            $Config->setHash(uniqid());
            $Storage->setItems($Config);
        }

        $Storage->save('config');

        Session::setConfigTime(0);
    }

    /**
     * Mapear el array de elementos de configuración con las propieades de la
     * clase ConfigData
     *
     * @param StorageInterface $Storage
     * @return ConfigData
     */
    private static function arrayMapper(StorageInterface $Storage)
    {
        $items = $Storage->load('config')->getItems();

        $ConfigData = new ConfigData();
        $Reflection = new ReflectionObject($ConfigData);

        foreach($Reflection->getProperties() as $property){
            $property->setAccessible(true);
            $property->setValue($ConfigData, $items[$property->getName()]);
            $property->setAccessible(false);
        }

        return $ConfigData;
    }
}