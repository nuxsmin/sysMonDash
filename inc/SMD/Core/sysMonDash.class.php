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

namespace SMD\Core;

use SMD\Backend\BackendInterface;
use SMD\Backend\Livestatus;
use SMD\Backend\Status;
use SMD\Backend\Zabbix;
use SMD\Util\Util;

class sysMonDash
{
    public static $totalItems;
    public static $displayedItems;
    /**
     * @var array Los eventos a mostrar
     */
    private static $_outData;
    /**
     * @var BackendInterface
     */
    protected $_backend;

    /**
     * sysMonDash constructor.
     *
     * @param $type string El tipo de backend a utilizar
     * @throws \Exception
     */
    public function __construct($type)
    {
        switch (strtolower($type)) {
            case 'livestatus':
                $this->_backend = new Livestatus();
                break;
            case 'status':
                $this->_backend = new Status();
                break;
            case 'zabbix':
                global $zabbix_version, $zabbix_url, $zabbix_user, $zabbix_pass;

                $this->_backend = new Zabbix($zabbix_version, $zabbix_url, $zabbix_user, $zabbix_pass);
                break;
            default:
                throw new \Exception('Backend no soportado');
        }
    }

    /**
     * Función para mostrar los avisos
     *
     * @param array $items Los elementos obtenidos desde Nagios/Icinga
     * @return array Con el número total de elementos y mostrados
     */
    public static function getItems(&$items)
    {
        global $newItemTime;

        // Contador del no. de elementos
        self::$totalItems = 0;
        // Contador de elementos mostrados
        self::$displayedItems = 0;

        // Recorremos el array y mostramos los elementos
        foreach ($items as $item) {
            $newItemUp = ($item['state'] === 0 && (isset($item['last_time_up']) || isset($item['last_time_ok']))) ? (abs(time() - $item['last_hard_state_change']) < $newItemTime / 2) : false;

            // Detectar si es un elemento nuevo, no se trata de un "RECOVERY" y no está "ACKNOWLEDGED"
            $newItem = (time() - $item['last_hard_state_change'] <= $newItemTime && !$newItemUp && $item['acknowledged'] === 0);

            // Mostrar elemento
            if (self::dashDisplay($item, $newItem, $newItemUp)) {
                self::$displayedItems++;
            }

            self::$totalItems++;
        }

        return self::$_outData;
    }

    /**
     * Función para mostrar los elementos del Dashboard
     *
     * @param array $item El elemento que contiene los datos.
     * @param bool $newItem Si es un nuevo elemento
     * @param bool $newItemUp Si es un nuevo elemento recuperado
     * @return bool
     */
    private static function dashDisplay(array &$item, $newItem = false, $newItemUp = false)
    {
        global $colLastcheck, $colHost, $colStatusInfo, $colService, $cgiURL, $type, $newItemTime;

        $statusId = $item['state'];
        $ack = $item['acknowledged'];
        $lastStateTime = date("m-d-Y H:i:s", $item['last_hard_state_change']);
        $lastStateDuration = Util::timeElapsed(time() - $item['last_hard_state_change']);
        $lastCheckDuration = Util::timeElapsed(time() - $item['last_check']);
        $serviceDesc = $item['display_name'];
        $hostname = (isset($item['host_display_name'])) ? $item['host_display_name'] : $item['display_name'];
        $hostAlias = (isset($item['host_alias'])) ? $item['host_alias'] : ((isset($item['alias'])) ? $item['alias'] : $hostname);
        $scheduled = ($item['scheduled_downtime_depth'] >= 1 || (isset($item['host_scheduled_downtime_depth']) && $item['host_scheduled_downtime_depth'] >= 1));
        $tdClass = '';
        $statusName = '';


        if (($type === VIEW_FRONTLINE || $type === VIEW_DISPLAY)
            && $newItem === false
            && $newItemUp === false
            && self::filterItems($item) === false
        ) {
            return false;
        }

        switch ($statusId) {
            case 0:
                $trClass = "new-up";
                $statusName = Language::t('OK');
                break;
            case 1:
                $trClass = "warning";
                $statusName = Language::t('AVISO');
                break;
            case 2:
                $trClass = "critical";
                $statusName = Language::t('CRITICO');
                break;
            case 3:
                $trClass = "unknown";
                $statusName = Language::t('DESCONOCIDO');
                break;
        }

        if ((isset($item['host_last_time_unreachable']) && $item['host_last_time_unreachable'] >= $item['host_last_time_up'] && !$newItemUp) ||
            (isset($item['last_time_unreachable']) && $item['last_time_unreachable'] > $item['last_check'] && $item['state_type'] === 1)
        ) {
//            $trTitle = Language::t("INALCANZABLE - Verificar objeto padre");
            $trClass = "unknown";
            $statusName = Language::t('INALCANZABLE');
        }

        if ($scheduled) {
//            $trTitle = Language::t("PROGRAMADO - Parada programada");
            $trClass = "downtime";
            $statusName = Language::t('PROGRAMADO');
        }

        if ($newItem === true && $ack === 0 && !$scheduled && !$newItemUp) {
            $tdClass = "new";
        } elseif ($newItemUp && time() - $item['last_hard_state_change'] <= $newItemTime / 2) {
//            $trTitle = Language::t("OK - Recuperado");
            $trClass = "new-up";
            $statusName = Language::t('RECUPERADO');
        } elseif ($item['is_flapping']) {
//            $trTitle = Language::t("CAMBIANTE - Frecuente cambio entre estados");
            $trClass = "flapping";
            $statusName = Language::t('CAMBIANTE');
        } elseif ($ack === 1) {
//            $trTitle = Language::t("RECONOCIDO - Error conocido");
            $trClass = "acknowledged";
            $statusName = Language::t('RECONOCIDO');
        }

        $actionHostLink = (isset($item['pnpgraph_present']) && $item['pnpgraph_present'] !== -1) ? '<a href="/pnp4nagios/index.php/graph?host=' . $hostname . '&srv=_HOST_" rel="/pnp4nagios/index.php/popup?host=' . $hostname . '&srv=_HOST_" class="action-link" target="blank"><img src="imgs/graph.png" /></a>' : '';

        // Si 'host_display_name' está presente, el item es un servicio
        if (!isset($item['host_display_name'])) {
            $link = $cgiURL . '/extinfo.cgi?type=1&host=' . $hostname;
            $actionServiceLink = '';
        } else {
            $link = $cgiURL . '/extinfo.cgi?type=2&host=' . $hostname . '&service=' . urlencode($serviceDesc);
            $actionServiceLink = '';
        }

        $line = '<tr class="item-data ' . $trClass . '" title="' . sprintf(Language::t('Estado %s desde %s'), $statusName, $lastStateTime) . '">' . PHP_EOL;
        $line .= '<td>' . $statusName . '</td>';
        $line .= ($colLastcheck == true) ? '<td title="' . sprintf('%s : %s', Language::t('Último check'), $lastCheckDuration) . '" class="' . $tdClass . '">' . $lastStateDuration . '</td>' . PHP_EOL : '';
        $line .= ($colHost == true) ? '<td><a href="' . $link . '" target="blank" title="' . $hostname . '">' . $hostAlias . '</a>' . $actionHostLink . '</td>' . PHP_EOL : '';
        $line .= ($colStatusInfo == true) ? '<td class="statusinfo">' . $item['plugin_output'] . '</td>' . PHP_EOL : '';

        if ($colService == true) {
            $line .= ($serviceDesc) ? '<td>' . $serviceDesc . $actionServiceLink . '</td>' . PHP_EOL : '<td>' . $item['check_command'] . $actionServiceLink . '</td>' . PHP_EOL;
        }

        $line .= '</tr>' . PHP_EOL;

        self::$_outData[] = $line;

        return true;
    }

    /**
     * Función para filtrar los avisos a mostrar
     *
     * @param array $item El elemento a verificar
     * @return bool
     */
    private static function filterItems(array &$item)
    {
        global $regexHostShow, $regexServiceNoShow, $criticalItems;

        $hostname = (isset($item['host_display_name'])) ? $item['host_display_name'] : $item['display_name'];

        if ($item['acknowledged'] === 1
            || (!preg_match($regexHostShow, $hostname) && !in_array($hostname, $criticalItems))
            || (preg_match($regexServiceNoShow, $item['display_name']) && !in_array($item['display_name'], $criticalItems))
            || ($item['current_attempt'] <= $item['max_check_attempts'] && $item['state_type'] === 0 && $item['is_flapping'] === 0)
            || (isset($item['host_state']) && $item['state'] > SERVICE_WARNING && $item['host_state'] >= HOST_DOWN)
            || ($item['state_type'] === 1 && isset($item['last_time_unreachable']) && $item['last_time_unreachable'] > $item['last_check'])
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return BackendInterface
     */
    public function getBackend()
    {
        return $this->_backend;
    }
}