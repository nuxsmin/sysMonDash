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
    /** @var array */
    private $backend = [];
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
    /** @var bool */
    private $colBackend = true;
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
    private $regexHostShow = '.*';
    /** @var string */
    private $regexServiceNoShow = '';
    /** @var array */
    private $criticalItems = [];
    /** @var string */
    private $hash = '';

    /**
     * @var string
     */
    private $configHash = '';

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Calcular el hash de la configuración
     */
    public function setHash()
    {
        $this->hash = uniqid();
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
     * @return array|ConfigBackendStatus[]|ConfigBackendLivestatus[]|ConfigBackendZabbix[]
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

    /**
     * @return string
     */
    public function getConfigHash()
    {
        return $this->configHash;
    }

    /**
     * Generar el hash de la configuración
     */
    public function setConfigHash()
    {
        $this->configHash = md5(serialize($this));
    }

    /**
     * @return boolean
     */
    public function isColBackend()
    {
        return $this->colBackend;
    }

    /**
     * @param boolean $colBackend
     */
    public function setColBackend($colBackend)
    {
        $this->colBackend = $colBackend;
    }
}