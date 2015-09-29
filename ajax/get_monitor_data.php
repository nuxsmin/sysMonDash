<?php
/**
 * sysMonDash
 *
 * @author    nuxsmin
 * @link      http://cygnux.org
 * @copyright 2014-2015 Rubén Domínguez nuxsmin@cygnux.org
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

$time_start = microtime(true);
define('APP_ROOT', '..');
session_start();

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'sysMonDash.php';

$type = (isset($_GET['t']) && !empty($_GET['t'])) ? intval($_GET['t']) : 0;
$timeout = (isset($_GET['to']) && !empty($_GET['to'])) ? intval($_GET['to']) : $refreshValue;

$downtimes = sysMonDash::getScheduledDowntimesGroupped();

ob_start();

// Obtener los avisos desde la monitorización y ordenarlos por tiempo de último cambio
$hostsProblems = sysMonDash::getHostsProblems();
$servicesProblems = sysMonDash::getServicesProblems();

if ($hostsProblems === false || $servicesProblems === false) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error - No data from socket', true, 500);
    exit();
}

$items = sysMonDash::sortByTime(array_merge($hostsProblems, $servicesProblems), 'last_hard_state_change');

// Array con los avisos filtrados
$res = sysMonDash::printItems($items);

$showAll = ($type !== 1) ? '(<a href="index.php?t=' . VIEW_ALL . '" title="Mostrar los avisos ocultos">Mostrar Todos</a>)' : '(<a href="index.php?t=' . VIEW_FRONTLINE . '" title="Mostrar sólo avisos importantes">Mostrar Menos</a>)';
?>

    <table id="tblBoard" width="90%" border="0" class="boldtable" align="center">
        <thead class="head">
        <th width="3%">Estado</th>
        <?php if ($colLastcheck == true): ?>
            <th width="13%">Desde</th>
        <?php endif; ?>
        <?php if ($colHost == true): ?>
            <th width="25%">Host</th>
        <?php endif; ?>
        <?php if ($colStatusInfo == true): ?>
            <th width="30%">Información de Estado</th>
        <?php endif; ?>
        <?php if ($colService == true): ?>
            <th width="20%">Servicio</th>
        <?php endif; ?>
        </thead>

        <?php if (sysMonDash::$displayedItems === 0): ?>
            <tr>
                <td colspan="5">
                    <div id="nomessages">
                        <img src="imgs/smile.png"/>
                        <br>
                        No hay avisos para mostrar
                    </div>
                </td>
            </tr>
            <script>jQuery("#tblBoard thead").hide()</script>
        <?php elseif (sysMonDash::$displayedItems > $maxDisplayItems): ?>
            <tr>
                <td colspan="5">
                    <div id="nomessages" class="error">
                        Upss...parece que hay problemas
                        <br>
                        Demasiados avisos (<?php echo sysMonDash::$displayedItems; ?>)
                        <br>
                        Revisar incidencias en web de <a href="<?php echo $monitorServerUrl; ?>">monitorización</a>
                    </div>
                </td>
            </tr>
            <script>jQuery("#tblBoard thead").hide()</script>
        <?php else: ?>
        <?php foreach ($res as $line): ?>
            <?php echo $line; ?>
        <?php endforeach; ?>
            <script>jQuery("#tblBoard thead").show()</script>
        <?php endif; ?>
        <tr id="total">
            <td colspan="5">
                <?php printf('%s %d@%.3fs (auto en %ds)', date('H:i:s', time()), sysMonDash::$displayedItems, microtime(true) - $time_start, $timeout); ?>
                <br>
                <?php echo sysMonDash::$totalItems - sysMonDash::$displayedItems, ' avisos ocultos ', $showAll; ?>
            </td>
        </tr>
    </table>

<?php if (count($downtimes) > 0): ?>
    <div class="title">Apagados Programados</div>

    <table id="tblDowntime" border="0" align="center">
        <thead class="head">
        <tr>
            <th>Servidor</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Autor</th>
            <th>Comentario</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($downtimes as $hostName => $downtime): ?>
            <?php $tiempoRestante = $downtime['start_time'] - time(); ?>
            <tr>
                <td><?php echo $hostName; ?></td>
                <td><?php echo (!empty($downtime['service_display_name'])) ? $downtime['service_display_name'] : $downtime['host_name']; ?></td>
                <td><?php echo ($tiempoRestante > 0) ? 'Quedan ' . sysMonDash::timeElapsed($tiempoRestante) : 'En parada'; ?></td>
                <td><?php echo date('d-m-Y H:i', $downtime['start_time']); ?></td>
                <td><?php echo date('d-m-Y H:i', $downtime['end_time']); ?></td>
                <td><?php echo $downtime['author']; ?></td>
                <td><?php echo $downtime['comment']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php ob_end_flush(); ?>

<?php if (sysMonDash::checkRefreshSession()): ?>
    <script>
        console.log('RELOAD');
        window.location.href = window.location.href;
    </script>
<?php endif; ?>