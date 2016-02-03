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

namespace SMD\Core;

/**
 * Class ConfigData para almacenar los datos de la configuración
 *
 * @package SMD\Core
 */
class ConfigData
{
    /** @var string */
    private $language = 'en_US';
    /** @var string */
    private $pageTitle = 'sysMonDash - Cuadro de Mandos';
    /** @var string */
    private $backend = 'livestatus';
    /** @var string */
    private $statusFile = '/var/lib/icinga/status.dat';
    /** @var string */
    private $livestatus_socket_path = '/var/lib/icinga/rw/live';
    /** @var int */
    private $zabbix_version = 222;
    /** @var string */
    private $zabbix_url = 'http://foo.bar/zabbix/api_jsonrpc.php';
    /** @var string */
    private $zabbix_user = 'zabbix';
    /** @var string */
    private $zabbix_pass = 'zabbix_pass';
    /** @var string */
    private $clientURL = '';
    /** @var string */
    private $remoteServer = '';
    /** @var string */
    private $monitorServerUrl = 'http://foo.bar';
    /** @var string */
    private $cgiURL = '';
    /** @var int */
    private $refreshValue = 10;
    /** @var bool */
    private $colLastcheck = true;
    /** @var bool */
    private $colHost = true;
    /** @var bool */
    private $colStatusInfo = true;
    /** @var bool */
    private $colService = true;
    /** @var int */
    private $newItemTime = 900;
    /** @var int */
    private $maxDisplayItems = 200;
    /** @var bool */
    private $useNagiosQLInfo = false;
    /** @var string */
    private $dbServer = 'localhost';
    /** @var string */
    private $dbName = 'nagiosql_db';
    /** @var string */
    private $dbUser = 'nagiosql_user';
    /** @var string */
    private $dbUserPass = 'nagiosql_pass';
    /** @var string */
    private $regexHostShow = '/.*/';
    /** @var string */
    private $regexServiceNoShow = '//';
    /** @var array */
    private $criticalItems = array();
    /** @var string */
    private $hash = '';

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param string $backend
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
    }

    /**
     * @return string
     */
    public function getStatusFile()
    {
        return $this->statusFile;
    }

    /**
     * @param string $statusFile
     */
    public function setStatusFile($statusFile)
    {
        $this->statusFile = $statusFile;
    }

    /**
     * @return string
     */
    public function getLivestatusSocketPath()
    {
        return $this->livestatus_socket_path;
    }

    /**
     * @param string $livestatus_socket_path
     */
    public function setLivestatusSocketPath($livestatus_socket_path)
    {
        $this->livestatus_socket_path = $livestatus_socket_path;
    }

    /**
     * @return int
     */
    public function getZabbixVersion()
    {
        return $this->zabbix_version;
    }

    /**
     * @param int $zabbix_version
     */
    public function setZabbixVersion($zabbix_version)
    {
        $this->zabbix_version = $zabbix_version;
    }

    /**
     * @return string
     */
    public function getZabbixUrl()
    {
        return $this->zabbix_url;
    }

    /**
     * @param string $zabbix_url
     */
    public function setZabbixUrl($zabbix_url)
    {
        $this->zabbix_url = $zabbix_url;
    }

    /**
     * @return string
     */
    public function getZabbixUser()
    {
        return $this->zabbix_user;
    }

    /**
     * @param string $zabbix_user
     */
    public function setZabbixUser($zabbix_user)
    {
        $this->zabbix_user = $zabbix_user;
    }

    /**
     * @return string
     */
    public function getZabbixPass()
    {
        return $this->zabbix_pass;
    }

    /**
     * @param string $zabbix_pass
     */
    public function setZabbixPass($zabbix_pass)
    {
        $this->zabbix_pass = $zabbix_pass;
    }

    /**
     * @return string
     */
    public function getClientURL()
    {
        return $this->clientURL;
    }

    /**
     * @param string $clientURL
     */
    public function setClientURL($clientURL)
    {
        $this->clientURL = $clientURL;
    }

    /**
     * @return string
     */
    public function getRemoteServer()
    {
        return $this->remoteServer;
    }

    /**
     * @param string $remoteServer
     */
    public function setRemoteServer($remoteServer)
    {
        $this->remoteServer = $remoteServer;
    }

    /**
     * @return string
     */
    public function getMonitorServerUrl()
    {
        return $this->monitorServerUrl;
    }

    /**
     * @param string $monitorServerUrl
     */
    public function setMonitorServerUrl($monitorServerUrl)
    {
        $this->monitorServerUrl = $monitorServerUrl;
    }

    /**
     * @return string
     */
    public function getCgiURL()
    {
        return $this->cgiURL;
    }

    /**
     * @param string $cgiURL
     */
    public function setCgiURL($cgiURL)
    {
        $this->cgiURL = $cgiURL;
    }

    /**
     * @return int
     */
    public function getRefreshValue()
    {
        return $this->refreshValue;
    }

    /**
     * @param int $refreshValue
     */
    public function setRefreshValue($refreshValue)
    {
        $this->refreshValue = $refreshValue;
    }

    /**
     * @return boolean
     */
    public function isColLastcheck()
    {
        return $this->colLastcheck;
    }

    /**
     * @param boolean $colLastcheck
     */
    public function setColLastcheck($colLastcheck)
    {
        $this->colLastcheck = $colLastcheck;
    }

    /**
     * @return boolean
     */
    public function isColHost()
    {
        return $this->colHost;
    }

    /**
     * @param boolean $colHost
     */
    public function setColHost($colHost)
    {
        $this->colHost = $colHost;
    }

    /**
     * @return boolean
     */
    public function isColStatusInfo()
    {
        return $this->colStatusInfo;
    }

    /**
     * @param boolean $colStatusInfo
     */
    public function setColStatusInfo($colStatusInfo)
    {
        $this->colStatusInfo = $colStatusInfo;
    }

    /**
     * @return boolean
     */
    public function isColService()
    {
        return $this->colService;
    }

    /**
     * @param boolean $colService
     */
    public function setColService($colService)
    {
        $this->colService = $colService;
    }

    /**
     * @return int
     */
    public function getNewItemTime()
    {
        return $this->newItemTime;
    }

    /**
     * @param int $newItemTime
     */
    public function setNewItemTime($newItemTime)
    {
        $this->newItemTime = $newItemTime;
    }

    /**
     * @return int
     */
    public function getMaxDisplayItems()
    {
        return $this->maxDisplayItems;
    }

    /**
     * @param int $maxDisplayItems
     */
    public function setMaxDisplayItems($maxDisplayItems)
    {
        $this->maxDisplayItems = $maxDisplayItems;
    }

    /**
     * @return boolean
     */
    public function isUseNagiosQLInfo()
    {
        return $this->useNagiosQLInfo;
    }

    /**
     * @param boolean $useNagiosQLInfo
     */
    public function setUseNagiosQLInfo($useNagiosQLInfo)
    {
        $this->useNagiosQLInfo = $useNagiosQLInfo;
    }

    /**
     * @return string
     */
    public function getDbServer()
    {
        return $this->dbServer;
    }

    /**
     * @param string $dbServer
     */
    public function setDbServer($dbServer)
    {
        $this->dbServer = $dbServer;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    /**
     * @return string
     */
    public function getDbUser()
    {
        return $this->dbUser;
    }

    /**
     * @param string $dbUser
     */
    public function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    /**
     * @return string
     */
    public function getDbUserPass()
    {
        return $this->dbUserPass;
    }

    /**
     * @param string $dbUserPass
     */
    public function setDbUserPass($dbUserPass)
    {
        $this->dbUserPass = $dbUserPass;
    }

    /**
     * @return string
     */
    public function getRegexHostShow()
    {
        return $this->regexHostShow;
    }

    /**
     * @param string $regexHostShow
     */
    public function setRegexHostShow($regexHostShow)
    {
        if (empty($regexHostShow)) {
            $regexHostShow = '/.*/';
        }

        $this->regexHostShow = $regexHostShow;
    }

    /**
     * @return string
     */
    public function getRegexServiceNoShow()
    {
        return $this->regexServiceNoShow;
    }

    /**
     * @param string $regexServiceNoShow
     */
    public function setRegexServiceNoShow($regexServiceNoShow)
    {
        $this->regexServiceNoShow = $regexServiceNoShow;
    }

    /**
     * @return array
     */
    public function getCriticalItems()
    {
        return $this->criticalItems;
    }

    /**
     * @param array $criticalItems
     */
    public function setCriticalItems($criticalItems)
    {
        $this->criticalItems = $criticalItems;
    }
}