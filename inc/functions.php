<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2014-2015 Rubén Domínguez nuxsmin@cygnux.org
 *
 * This file is part of sysPass.
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

require 'config.php';
require 'constants.php';

session_start();

$out = array();

/**
 * Obtener un recurso del tipo Socket utilizando el socket unix de mklivestatus
 *
 * @return bool|resource
 */
function getLiveSocket()
{
    global $livestatus_socket_path;

    $socket = stream_socket_client('unix://' . $livestatus_socket_path, $errno, $errstr);

    if (!$socket) {
        error_log("ERROR: $errno - $errstr");
        return false;
    }

    return $socket;
}

/**
 * Obtener el listado de hosts utilizando el socket de mklivestatus
 *
 * @param bool $allHeaders Obtener todas las cabeceras
 * @return array|mixed
 */
function getHostsProblems($allHeaders = false)
{
    global $newItemTime;

    $fields = array(
        'alias',
        'state',
        'check_command',
        'display_name',
        'current_attempt',
        'max_check_attempts',
        'hard_state',
        'is_flapping',
        'plugin_output',
        'notes',
        'acknowledged',
        'acknowledgement_type',
        'action_url_expanded',
        'active_checks_enabled',
        'last_hard_state',
        'scheduled_downtime_depth',
        'last_check',
        'last_hard_state_change',
        'last_time_down',
        'last_time_unreachable',
        'last_time_up',
        'max_check_attempts',
        'host_alias',
        'host_is_flapping',
        'state_type'
    );

    if ($allHeaders === false) {
        $filter = array(
            'GET hosts',
            'Filter: checks_enabled = 1',
            'Filter: state != ' . HOST_UP,
            'Filter: last_hard_state_change > ' . (time() - $newItemTime / 2),
            'Filter: is_flapping = 1',
            'Or: 3',
            'Columns: ' . implode(' ', $fields),
            'ColumnHeaders: off',
            'OutputFormat: json'
        );

        $inData = implode("\n", $filter) . "\n\n";
        $data = json_decode(getDataFromSocket($inData));
    } else {
        $inData = "GET hosts\nFilter: state != " . HOST_UP . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        $data = json_decode(getDataFromSocket($inData));
    }

    if ($allHeaders === false) {
        $fulldata = array();

        foreach ($data as $hostdata) {
            $fulldata[] = array_combine($fields, $hostdata);
        }

        return $fulldata;
    }

    return $data;
}

/**
 * Obtener el listado de servicios utilizando el socket de mklivestatus
 *
 * @param bool $allHeaders Obtener todas las cabeceras
 * @return array|mixed
 */
function getServicesProblems($allHeaders = false)
{
    global $newItemTime;

    $fields = array(
        'acknowledged',
        'acknowledgement_type',
        'action_url_expanded',
        'active_checks_enabled',
        'check_command',
        'checks_enabled',
        'current_attempt',
        'display_name',
        'has_been_checked',
        'host_action_url_expanded',
        'host_active_checks_enabled',
        'host_alias',
        'host_checks_enabled',
        'host_current_attempt',
        'host_is_flapping',
        'host_last_check',
        'host_last_hard_state_change',
        'host_last_state',
        'host_last_state_change',
        'host_last_time_down',
        'host_last_time_unreachable',
        'host_last_time_up',
        'host_state',
        'host_display_name',
        'host_scheduled_downtime_depth',
        'is_flapping',
        'last_check',
        'last_hard_state',
        'last_hard_state_change',
        'last_state',
        'last_state_change',
        'last_time_critical',
        'last_time_ok',
        'last_time_unknown',
        'last_time_warning',
        'perf_data',
        'plugin_output',
        'pnpgraph_present',
        'state',
        'state_type',
        'scheduled_downtime_depth',
        'max_check_attempts'
    );

    if ($allHeaders === false) {
        $filter = array(
            'GET services',
            'Filter: checks_enabled = 1',
            'Filter: state != ' . SERVICE_OK,
            'Filter: last_hard_state_change > ' . (time() - $newItemTime / 2),
            'Filter: is_flapping = 1',
            'Or: 3',
            'Columns: ' . implode(' ', $fields),
            'ColumnHeaders: off',
            'OutputFormat: json'
        );

        $inData = implode("\n", $filter) . "\n\n";
        $data = json_decode(getDataFromSocket($inData));
    } else {
        $inData = "GET services\nFilter: state != " . SERVICE_OK . "\nFilter: checks_enabled = 1\nColumnHeaders: off\nOutputFormat: json\n\n";
        $data = json_decode(getDataFromSocket($inData));
    }

    if ($allHeaders === false) {
        $fulldata = array();

        foreach ($data as $servicedata) {
            $fulldata[] = array_combine($fields, $servicedata);
        }

        return $fulldata;
    }

    return $data;
}

/**
 * Obtener el listado de paradas programadas.
 *
 * @param bool $allHeaders obtiene todas las cabeceras de la consulta
 * @return array|mixed
 */
function getScheduledDowntimes($allHeaders = false)
{
    $fields = array(
        'author',
        'comment',
        'duration',
        'host_alias',
        'host_name',
        'is_service',
        'service_display_name',
        'start_time',
        'end_time'
    );

    if ($allHeaders === false) {
        $filter = array('GET downtimes', 'Columns: ' . implode(' ', $fields), 'ColumnHeaders: off', 'OutputFormat: json');
        $inData = implode("\n", $filter) . "\n\n";
        $data = json_decode(getDataFromSocket($inData));
    } else {
        $inData = "GET downtimes\nColumnHeaders: off\nOutputFormat: json\n\n";
        $data = json_decode(getDataFromSocket($inData));
    }

    if ($allHeaders === false) {
        $fulldata = array();

        foreach ($data as $servicedata) {
            $fulldata[] = array_combine($fields, $servicedata);
        }

        return $fulldata;
    }

    return $data;
}

/**
 * Realizar una petición a un socket y obtener los resultados.
 *
 * @param string $inData Los datos a consultar
 * @return bool|string
 */
function getDataFromSocket(&$inData)
{
    $socket = getLiveSocket();

    if (!is_resource($socket)) {
        return false;
    }

    try {
        fwrite($socket, $inData);
        $outData = stream_get_contents($socket);
        fclose($socket);
        return $outData;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Ordenar un array por una clave dada.
 *
 * @param string $data El array a ordenar
 * @param string $fieldName La clave de ordenación
 * @param bool $sortAsc El orden de ordenación
 * @return mixed
 */
function sortByTime(&$data, $fieldName, $sortAsc = true)
{
    // Ordenar el array multidimensional por la clave $fielName de mayor a menor
    usort($data, function ($a, $b) use ($fieldName, $sortAsc) {
        if ($sortAsc) {
            return ($a[$fieldName] < $b[$fieldName]);
        } else {
            return ($a[$fieldName] > $b[$fieldName]);
        }
    });

    return $data;
}

/**
 * Función para mostrar los elementos del Dashboard
 *
 * @param array $item El elemento que contiene los datos.
 * @param bool $newItem Si es un nuevo elemento
 * @param bool $newItemUp Si es un nuevo elemento recuperado
 * @return bool
 */
function dashDisplay(array &$item, $newItem = false, $newItemUp = false)
{
    global $colLastcheck, $colHost, $colStatusInfo, $colService, $cgiURL, $type, $newItemTime, $out;

    $statusId = $item['state'];
    $ack = $item['acknowledged'];
    $lastStateTime = date("m-d-Y H:i:s", $item['last_hard_state_change']);
    $lastStateDuration = timeElapsed(time() - $item['last_hard_state_change']);
    $lastCheckDuration = timeElapsed(time() - $item['last_check']);
    $serviceDesc = $item['display_name'];
    $hostname = (isset($item['host_display_name'])) ? $item['host_display_name'] : $item['display_name'];
    $hostAlias = (isset($item['host_alias'])) ? $item['host_alias'] : $item['alias'];
    $scheduled = ($item['scheduled_downtime_depth'] >= 1 || (isset($item['host_scheduled_downtime_depth']) && $item['host_scheduled_downtime_depth'] >= 1));
    $trTitle = '';
    $tdClass = '';
    $statusName = '';


    if (($type === VIEW_FRONTLINE || $type === VIEW_DISPLAY)
        && $newItem === false
        && $newItemUp === false
        && filterItems($item) === false
    ) {
        return false;
    }

    switch ($statusId) {
        case 0:
            $trTitle = "OK";
            $trClass = "new-up";
            $statusName = 'OK';
            break;
        case 1:
            $trTitle = "AVISO";
            $trClass = "warning";
            $statusName = 'AVISO';
            break;
        case 2:
            $trTitle = "CRITICO";
            $trClass = "critical";
            $statusName = 'CRITICO';
            break;
        case 3:
            $trTitle = "DESCONOCIDO";
            $trClass = "unknown";
            $statusName = 'DESCONOCIDO';
            break;
    }

    if ((isset($item['host_last_time_unreachable']) && $item['host_last_time_unreachable'] >= $item['host_last_time_up'] && !$newItemUp) ||
        (isset($item['last_time_unreachable']) && $item['last_time_unreachable'] > $item['last_check'] && $item['state_type'] === 1)
    ) {
        $trTitle = "INALCANZABLE - Verificar objeto padre";
        $trClass = "unknown";
        $statusName = 'INALCANZABLE';
    }

    if ($scheduled) {
        $trTitle = "PROGRAMADO - Parada programada";
        $trClass = "downtime";
        $statusName = 'PROGRAMADO';
    }

    if ($newItem === true && $ack === 0 && !$scheduled && !$newItemUp) {
        $tdClass = "new";
    } elseif ($newItemUp && time() - $item['last_hard_state_change'] <= $newItemTime / 2) {
        $trTitle = "OK - Recuperado";
        $trClass = "new-up";
        $statusName = 'RECUPERADO';
    } elseif ($item['is_flapping']) {
        $trTitle = "CAMBIANTE - Frecuente cambio entre estados ";
        $trClass = "flapping";
        $statusName = 'CAMBIANTE';
    } elseif ($ack === 1) {
        $trTitle = "RECONOCIDO - Error conocido";
        $trClass = "acknowledged";
        $statusName = 'RECONOCIDO';
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

    $line = '<tr class="item-data ' . $trClass . '" title="Estado ' . $trTitle . ' desde ' . $lastStateTime . '">' . PHP_EOL;
    $line .= '<td>' . $statusName . ' </td>';
    $line .= ($colLastcheck == true) ? '<td title="Último check: ' . $lastCheckDuration . '" class="' . $tdClass . '">' . $lastStateDuration . '</td>' . PHP_EOL : '';
    $line .= ($colHost == true) ? '<td><a href="' . $link . '" target="blank" title="' . $hostname . '">' . $hostAlias . '</a>' . $actionHostLink . '</td>' . PHP_EOL : '';
    $line .= ($colStatusInfo == true) ? '<td class="statusinfo">' . $item['plugin_output'] . '</td>' . PHP_EOL : '';

    if ($colService == true) {
        $line .= ($serviceDesc) ? '<td>' . $serviceDesc . $actionServiceLink . '</td>' . PHP_EOL : '<td>' . $item['check_command'] . $actionServiceLink . '</td>' . PHP_EOL;
    }

    $line .= '</tr>' . PHP_EOL;

    $out[] = $line;

    return true;
}

/**
 * Función para devolver el formato correcto de los valores de un array
 *
 * @param string $value El valor a formatear
 * @return int|string
 */
function clearArrayValues($value)
{
    return (is_numeric($value)) ? intval($value) : htmlentities($value);
}

/**
 * Función para calcular el tiempo transcurrido
 *
 * @param int $secs El tiempo en formato UNIX
 * @return string Cadena con las hora:minutos:segundos
 */
function timeElapsed($secs)
{
    $bit = array(
//        'a' => $secs / 31556926 % 12,
//        'w' => $secs / 604800 % 52,
        'd' => abs($secs) / 86400 % 365,
        'h' => abs($secs) / 3600 % 24,
        'm' => abs($secs) / 60 % 60,
        's' => abs($secs) % 60
    );

    foreach ($bit as $k => $v) {
        if ($v > 0) {
            $ret[] = $v . $k;
        }
    }

    return join(' ', $ret);
}

/**
 * Función para obtener Información de la BD de NagiosQL
 *
 * @return array|bool
 */
function getDBInfo()
{
    global $dbServer, $dbName, $dbUser, $dbUserPass;

    $mysqli = new mysqli($dbServer, $dbUser, $dbUserPass, $dbName);

    if ($mysqli->connect_errno) {
        error_log('(' . __FUNCTION__ . ') Fallo al conectar a MySQL: ' . $mysqli->connect_error);
        return false;
    }

    if (!$resQuery = $mysqli->query("SELECT host_name,alias FROM tbl_host")) {
        error_log('(' . __FUNCTION__ . ') Fallo al obtener los registros: ' . $mysqli->connect_error);
        return false;
    }

    $result = array();

    while ($row = $resQuery->fetch_assoc()) {
        $result[$row['host_name']] = $row['alias'];
    }

    // Devolvemos un array con los registros.
    // La clave es el nombre corto del host y el valor es el alias
    return $result;
}

/**
 * Obtener los datos de monitorización mediante el archivo status.dat
 *
 * @deprecated
 */
function getStatusData()
{
    global $statusFile, $useNagiosQLInfo, $timeout;

    // Archivo con información de estado de Icinga
    $file = file_get_contents($statusFile);

    if (!$file) {
        echo '<table>';
        echo '<tr id="total"><td>';
        echo '<p id="error">Error al obtener los datos de monitorización</p>';
        echo '<p id="refreshing">Recarga en <span id="refreshing_countdown">' . $timeout . '</span> segundos</p>';
        echo '<div id="loading"></div>';
        echo '</td></tr>';
        echo '</table>';
        exit();
    }

    // Campos a obtener desde la información de estado
    $regex_fields = array(
        'current_state',
        'problem_has_been_acknowledged',
        'host_name',
        'check_command',
        'plugin_output',
        'last_check',
        'last_time_up',
        'last_time_ok',
        'last_time_unreachable',
        'last_hard_state_change',
        'active_checks_enabled',
        'scheduled_downtime_depth',
        'current_attempt',
        'max_attempts',
        'last_hard_state',
        'last_time_down',
        'service_description',
        'active_checks_enabled',
        'is_flapping',
        'notifications_enabled');

    $regex_pattern = implode('|', $regex_fields);
    if ($useNagiosQLInfo && !isset($_SESSION['dash_hostinfo'])) {
        $_SESSION['dash_hostinfo'] = getDBInfo();
    }

    // Obtenemos los bloques que corresponden al estado de los hosts y servicios
    preg_match_all('/(hoststatus|servicestatus) {.*}/isU', $file, $monitorData);

    // Creamos un array por cada elemento con los valores de los datos filtrados en $$monitorData
    /*
     * Ejemplo:
    Array
    (
        [0] => Array
            (
                [host_name] => IMM-ITV-ZAL_1
                [service_description] => IPMI
                [current_state] => 3
                [plugin_output] => /usr/sbin/ipmi-sensors: connection timeout
                [last_check] => 1403521039
                [active_checks_enabled] => 1
                [problem_has_been_acknowledged] => 0
            )
    */

    foreach ($monitorData[0] as $monitorDataParam) {
        preg_match_all('/\t(' . $regex_pattern . ')=(.*)/', $monitorDataParam, $monitorDataValues);
        // Array de datos de hosts. Se utiliza la función "clearArrayValues" para transformar los valores
        $items[] = array_combine($monitorDataValues[1], array_map('clearArrayValues', $monitorDataValues[2]));
    }

    // Ordenar el array multidimensional por la clave "last_hard_state_change" de mayor a menor
    usort($items, function ($a, $b) {
        //    return ($a['current_state'] < $b['current_state']);
        return ($a['last_hard_state_change'] < $b['last_hard_state_change']);
    });
}

/**
 * Función para mostrar los avisos
 *
 * @param array $items Los elementos obtenidos desde Nagios/Icinga
 * @return array Con el número total de elementos y mostrados
 */
function printItems(&$items)
{
    global $newItemTime, $out;

    // Contador del no. de elementos
    $totalItems = 0;
    // Contador de elementos mostrados
    $displayedItems = 0;

    // Recorremos el array y mostramos los elementos
    foreach ($items as $item) {
        $newItemUp = ($item['state'] === 0 && (isset($item['last_time_up']) || isset($item['last_time_ok']))) ? (abs(time() - $item['last_hard_state_change']) < $newItemTime / 2) : false;


        // Detectar si es un elemento nuevo, no se trata de un "RECOVERY" y no está "ACKNOWLEDGED"
        $newItem = (time() - $item['last_hard_state_change'] <= $newItemTime && !$newItemUp && $item['acknowledged'] === 0);

        // Mostrar elemento
        if (dashDisplay($item, $newItem, $newItemUp)) {
            $displayedItems++;
        }

        $totalItems++;
    }

    return array('totalItems' => $totalItems, 'displayedItems' => $displayedItems, 'items' => &$out);
}

/**
 * Función para filtrar los avisos a mostrar
 *
 * @param array $item El elemento a verificar
 * @return bool
 */
function filterItems(array &$item)
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
 * Comprobar si es necesario reiniciar la página para actualizar
 *
 * @return bool
 */
function checkRefreshSession()
{
    $version = 1;

    if (!isset($_SESSION['EXPIRE'], $_SESSION['VERSION'])
        || $_SESSION['EXPIRE'] - time() < 0
        || $_SESSION['VERSION'] < $version
    ) {
        $_SESSION['VERSION'] = $version;
        $_SESSION['EXPIRE'] = time() + 7200;
        $_SESSION['CSS_HASH'] = getCssHash();
        return true;
    }

    return false;
}

/**
 * Agrupar las paradas programadas para aquellos hosts que tengan mas de X servicios programados.
 *
 * @return array Con los eventos programados
 */
function getScheduledDowntimesGroupped()
{
    $groupCount = 5;
    $hosts = array();
    $out = array();
    $downtimes = sortByTime(getScheduledDowntimes(), 'start_time', false);

    // Recorrer el array de eventos y contabilizar el número de veces que aparece cada host
    foreach ($downtimes as $downtime) {
        // Incrementar el contador y silenciar avisos
        @$hosts[$downtime['host_alias']]['count']++;
    }

    // Recorrer el array de eventos y agrupar aquellos eventos de un host que se repitan más de $groupCount.
    // Se utiliza un nuevo array con la clave el nombre del host
    foreach ($downtimes as $downtime) {
        $hostName = $downtime['host_alias'];
        $hostCounter = $hosts[$hostName]['count'];

        if (isset($out[$hostName])) {
            continue;
        } elseif ($hostCounter > $groupCount) {
            $out[$hostName]['host_alias'] = $hostName;
            $out[$hostName]['service_display_name'] = 'Programado para ' . $hostCounter . ' servicios';
            $out[$hostName]['start_time'] = $downtime['start_time'];
            $out[$hostName]['end_time'] = $downtime['end_time'];
            $out[$hostName]['author'] = $downtime['author'];
            $out[$hostName]['comment'] = $downtime['comment'];
        } else {
            $out[$hostName] = $downtime;
        }
    }

    return $out;
}

/**
 * Devuelve el hash del archivo CSS
 *
 * @return string
 */
function getCssHash()
{
    return hash_file('md5', APP_ROOT . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'styles.css');
}

if (isset($_GET['raw']) && intval($_GET['raw']) == 1) {

    $allHeaders = (isset($_GET['allheaders']) && intval($_GET['allheaders']) === 1);

    echo '<pre>';
    echo 'Hosts', PHP_EOL;
    print_r(sortByTime(getHostsProblems($allHeaders), 'last_hard_state_change'));
    echo 'Servicios', PHP_EOL;
    print_r(sortByTime(getServicesProblems($allHeaders), 'last_hard_state_change'));
    echo 'Paradas', PHP_EOL;
    print_r(sortByTime(getScheduledDowntimes($allHeaders), 'start_time', false));
    echo '</pre>';
}