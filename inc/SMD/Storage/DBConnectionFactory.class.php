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

namespace SMD\Storage;

use \PDO;

defined('APP_ROOT') || die(_('No es posible acceder directamente a este archivo'));

/**
 * Class DBConnectionFactory
 *
 * Esta clase se encarga de crear las conexiones a la BD
 */
class DBConnectionFactory
{
    /**
     * @var DBConnectionFactory
     */
    private static $_factory = null;
    /**
     * @var \PDO
     */
    private $_db;

    /**
     * Obtener una instancia de la clase
     *
     * @return DBConnectionFactory
     */
    public static function getFactory()
    {
        if (null === self::$_factory) {
            self::$_factory = new DBConnectionFactory();
        }

        return self::$_factory;
    }

    /**
     * Realizar la conexión con la BBDD.
     * Esta función utiliza PDO para conectar con la base de datos.
     * @return PDO
     * @throws \Exception
     */

    public function getConnection()
    {
        if (!$this->_db) {
            $dbhost = Config::getValue('dbhost');
            $dbuser = Config::getValue('dbuser');
            $dbpass = Config::getValue('dbpass');
            $dbname = Config::getValue('dbname');
            $dbport = Config::getValue('dbport', 3306);

            if (empty($dbhost) || empty($dbuser) || empty($dbpass) || empty($dbname)) {
                throw new \Exception(_('No es posible conectar con la BD'), 1);
            }

            try {
                $dsn = 'mysql:host=' . $dbhost . ';port=' . $dbport . ';dbname=' . $dbname . ';charset=utf8';
//                $this->db = new PDO($dsn, $dbuser, $dbpass, array(PDO::ATTR_PERSISTENT => true));
                $this->_db = new PDO($dsn, $dbuser, $dbpass);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        $this->_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $this->_db;
    }
}