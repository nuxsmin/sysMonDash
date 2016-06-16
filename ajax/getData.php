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

use SMD\Core\Config;
use SMD\Core\Init;
use SMD\Core\Language;
use SMD\Core\sysMonDash;
use SMD\Http\Request;
use SMD\Util\Util;

define('APP_ROOT', '..');

require APP_ROOT . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Base.php';

Init::start();

Request::checkCORS();

$type = Request::analyze('t', VIEW_FRONTLINE);
$timeout = Request::analyze('to', Config::getConfig()->getRefreshValue());

$SMD = new sysMonDash();
$SMD->setViewType($type);

ob_start();

// Array con los avisos filtrados
$res = $SMD->getItems();

if ($type !== 1) {
    $showAll = '<a href="index.php?t=' . VIEW_ALL . '" title="' . Language::t('Mostrar los avisos ocultos') . '">' . Language::t('Mostrar Todos') . '</a>';
} else {
    $showAll = '<a href="index.php?t=' . VIEW_FRONTLINE . '" title="' . Language::t('Mostrar sólo avisos importantes') . '">' . Language::t('Mostrar Menos') . '</a>';
}

?>
    <table id="tblBoard" width="90%" border="0" class="boldtable" align="center">
        <thead class="head">
        <th width="3%"><?php echo Language::t('Nivel'); ?></th>
        <?php if (Config::getConfig()->isColLastcheck()): ?>
            <th width="13%"><?php echo Language::t('Desde'); ?></th>
        <?php endif; ?>
        <?php if (Config::getConfig()->isColHost()): ?>
            <th width="25%"><?php echo Language::t('Host'); ?></th>
        <?php endif; ?>
        <?php if (Config::getConfig()->isColStatusInfo()): ?>
            <th width="30%"><?php echo Language::t('Información de Estado'); ?></th>
        <?php endif; ?>
        <?php if (Config::getConfig()->isColService()): ?>
            <th width="20%"><?php echo Language::t('Servicio'); ?></th>
        <?php endif; ?>
        <?php if (Config::getConfig()->isColBackend()): ?>
            <th width="20%"><?php echo Language::t('Backend'); ?></th>
        <?php endif; ?>
        </thead>

        <?php if ($SMD->getDisplayedItems() === 0 && count($SMD->getErrors()) === 0): ?>
            <tr>
                <td colspan="5">
                    <div id="nomessages">
                        <img src="imgs/smile.png"/>
                        <br>
                        <?php echo Language::t('No hay avisos para mostrar'); ?>
                    </div>
                </td>
            </tr>
            <script>jQuery("#tblBoard").find("thead").hide()</script>
        <?php elseif ($SMD->getDisplayedItems() > Config::getConfig()->getMaxDisplayItems()): ?>
            <tr>
                <td colspan="5">
                    <div id="nomessages" class="error">
                        <?php echo Language::t('Upss...parece que hay problemas'); ?>
                        <br>
                        <?php echo Language::t('Demasiados avisos'); ?> (<?php echo $SMD->getDisplayedItems(); ?>)
                        <br>
                        <a href="<?php echo Config::getConfig()->getMonitorServerUrl(); ?>"><?php echo Language::t('Revisar incidencias en web de monitorización'); ?></a>
                    </div>
                </td>
            </tr>
            <script>jQuery("#tblBoard").find("thead").hide()</script>
        <?php else: ?>
        <?php foreach ($res as $line): ?>
            <?php echo $line; ?>
        <?php endforeach; ?>
            <script>jQuery("#tblBoard").find("thead").show()</script>
        <?php endif; ?>

    </table>

    <div id="total">
        <?php printf('%s | %d@%.4fs | auto %ds', date('H:i:s', time()), $SMD->getDisplayedItems(), microtime(true) - $time_start, $timeout); ?>
        |
        <?php printf('%d/%d %s %s', $SMD->getDisplayedItems(), $SMD->getTotalItems(), Language::t('avisos'), $showAll); ?>
    </div>

<?php if (count($SMD->getDowntimes()) > 0): ?>
    <div class="title"><?php echo Language::t('Apagados Programados'); ?></div>

    <table id="tblDowntime" border="0" align="center">
        <thead class="head">
        <tr>
            <th><?php echo Language::t('Servidor'); ?></th>
            <th><?php echo Language::t('Servicio'); ?></th>
            <th><?php echo Language::t('Estado'); ?></th>
            <th><?php echo Language::t('Inicio'), ' &#8594; ', Language::t('Fin'); ?></th>
            <th><?php echo Language::t('Autor'); ?></th>
            <th><?php echo Language::t('Comentarios'); ?></th>
            <?php if (Config::getConfig()->isColBackend()): ?>
                <th><?php echo Language::t('Backend'); ?></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($SMD->getDowntimes() as $downtime): ?>
            <?php /** @var $downtime \SMD\Backend\Event\DowntimeInterface */ ?>
            <?php $tiempoRestante = $downtime->getStartTime() - time(); ?>
            <tr>
                <td><?php echo (is_array($downtime->getHostName())) ? implode('<br>', $downtime->getHostName()) : $downtime->getHostName(); ?></td>
                <td><?php echo ($downtime->getServiceDisplayName()) ? $downtime->getServiceDisplayName() : $downtime->getHostName(); ?></td>
                <td><?php echo ($tiempoRestante > 0) ? sprintf(Language::t('Quedan %s'), Util::timeElapsed($tiempoRestante)) : Language::t('En parada'); ?></td>
                <td><?php echo date('d-m-Y H:i', $downtime->getStartTime()), ' &#8594; ', date('d-m-Y H:i', $downtime->getEndTime()); ?></td>
                <td><?php echo $downtime->getAuthor(); ?></td>
                <td><?php echo $downtime->getComment(); ?></td>
                <?php if (Config::getConfig()->isColBackend()): ?>
                    <td><?php echo $downtime->getBackendAlias(); ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (count($SMD->getErrors()) > 0): ?>
    <div class="title"><?php echo Language::t('Errores'); ?></div>
    <?php foreach ($SMD->getErrors() as $error): ?>
        <div id="nomessages" class="full error">
            <?php echo Language::t($error); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php ob_end_flush(); ?>

<?php if (Util::checkRefreshSession()): ?>
    <script>
        console.info('RELOAD');
        window.location.href = "index.php";
    </script>
<?php endif; ?>