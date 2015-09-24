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

define('APP_ROOT', '.');

require 'inc' . DIRECTORY_SEPARATOR . 'config.php';
require 'inc' . DIRECTORY_SEPARATOR . 'constants.php';

$type = (isset($_GET['t']) && !empty($_GET['t'])) ? intval($_GET['t']) : VIEW_FRONTLINE;
$timeout = (isset($_GET['to']) && !empty($_GET['to'])) ? intval($_GET['to']) : $refreshValue;
$scroll = (isset($_GET['scroll']) && !empty($_GET['scroll'])) ? intval($_GET['scroll']) : 0;

$ajaxFile = '/ajax/get_monitor_data.php?t=' . $type . '&to=' . $timeout;

// Forzar el scroll si el tipo de vista es FRONTLINE
$scroll = ($type === VIEW_FRONTLINE || $type === VIEW_DISPLAY) ? 1 : 0;

session_start();

$_SESSION['CSS_HASH'] = hash_file('md5', APP_ROOT . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'styles.css');
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="icon" type="image/png" href="imgs/logo_small.png">
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css?v=<?php echo $_SESSION['CSS_HASH']; ?>">
</head>
<body>
<div id="logo">
    <img src="imgs/logo.png"/>

    <div id="hora"><h1></h1></div>
    <div id="titulo">
        <h1>Panel Monitorización</h1>

        <h2>Sistemas</h2>
    </div>
</div>

<div id="monitor-data"></div>

<footer>
    <div id="project">
        <a href="http://cygnux.org" target="_blank" title="Un proyecto de cygnux.org">cygnux.org</a>
    </div>
</footer>

<script type="text/javascript">
    (function () {
        jQuery.noConflict();

        /**
         * Devuelve la URL a la raíz de la web
         */
        var getRootPath = function () {
            var path = window.location.pathname.split('/');
            var rootPath = function () {
                var fullPath = '';

                for (var i = 1; i <= path.length - 2; i++) {
                    fullPath += "/" + path[i];
                }

                return fullPath;
            };

            return window.location.protocol + "//" + window.location.host + rootPath();
        };

        /**
         * Objeto que contiene las variables de configuración de PHP
         */
        var config = {
            timeout: <?php echo $timeout * 1000; ?>,
            scroll: <?php echo ($scroll) ? 'true' : 'false'; ?>,
            ajaxfile: getRootPath() + '<?php echo $ajaxFile; ?>'
        };

        var totalHeight;

        function hex2rgb(hexStr) {
            // note: hexStr should be #rrggbb
            var hex = parseInt(hexStr.substring(1), 16);
            var r = (hex & 0xff0000) >> 16;
            var g = (hex & 0x00ff00) >> 8;
            var b = hex & 0x0000ff;
            return 'rgb(' + r + ', ' + g + ', ' + b + ')';
        }

        /**
         * Función para activar el parpadeo de los eventos recientes
         */
        (function ($) {
            $.fn.blink = function (options) {
                var defaults = {delay: 1000};
                var options = $.extend(defaults, options);

                return this.each(function () {
                    var obj = $(this);
                    var bgcolor = $(this).css('background-color');
                    var fgcolor = $(this).css('color');
                    var on = 0;

                    setInterval(function () {
                        if (on === 0) {
                            $(obj).css('color', options.fgcolor_on);
                            $(obj).css('background-color', options.bgcolor_on);
                            on = 1;
                        } else {
                            $(obj).css('color', fgcolor);
                            $(obj).css('background-color', bgcolor);
                            on = 0;
                        }
                    }, options.delay);
                });
            }
        }(jQuery));

        jQuery().ready(function () {
            updateNagiosData();
            setInterval(function () {
                updateNagiosData();
            }, config.timeout);
        });

        /**
         * Obtiene mediante AJAX los eventos a mostrar
         */
        function updateNagiosData() {
            var placeHolder = jQuery("#monitor-data");

            setTime();

            jQuery('#icinga_header').attr('src', jQuery('#icinga_header').attr('src'));

            placeHolder.load(config.ajaxfile, function (response, status, xhr) {
                jQuery(this).empty();

                if (status == "error") {
                    var msg = "Error al obtener los eventos de monitorización: ";
                    jQuery(this).html("<div id=\"nomessages\" class=\"error\">" + msg + "<p>" + xhr.status + " " + xhr.statusText + "</p></div>");
                    return;
                }

                jQuery(this).html(response);

                if (config.scroll) {
                    totalHeight = jQuery(document).height();

                    if (totalHeight > window.innerHeight) {
                        setTimeout('pageScroll()', config.timeout / 2);
                    }
                }

                jQuery('.new').blink({bgcolor_on: hex2rgb('#F7FE2E'), fgcolor_on: hex2rgb('#333333')});
            });
        }

        /**
         * Actualizar el contador de refresco
         */
        function updateCountDown() {
            var countdown = jQuery("#refreshing_countdown");
            var remaining = parseInt(countdown.text());
            if (remaining == 0) {
                updateNagiosData(placeHolder);
                countdown.text(config.timeout);
            }
            else {
                countdown.text(remaining - 1);
            }
        }

        /**
         * Inserta la hora actual en la cabecera de la página
         */
        function setTime() {
            var d = new Date();

            var curr_date = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
            var curr_hour = ('0' + d.getHours()).slice(-2);
            var curr_min = ('0' + d.getMinutes()).slice(-2);
            var curr_sec = ('0' + d.getSeconds()).slice(-2);

            jQuery('#hora>h1').html(curr_date + '<br>' + curr_hour + ':' + curr_min + ':' + curr_sec);
        }

        /**
         * Realiza un scroll automático de la página
         */
        function pageScroll() {
            jQuery('body,html').animate(
                {scrollTop: totalHeight},
                config.timeout / 2,
                function () {
                    pageUnScroll();
                }
            ).on("mousemove",
                function () {
                    jQuery(this).stop(true);
                }
            );
        }

        /**
         * Devuelve el scroll a la posición inicial
         */
        function pageUnScroll() {
            jQuery('body,html').scrollTop(0);
        }

        /**
         * Recargar la página
         */
        function reloadPsage() {
            window.location.reload(false);
        }
    }());
</script>
</body>
</html>
