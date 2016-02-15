/**
 * Función para activar el parpadeo de los eventos recientes
 */
(function ($) {
    $.fn.blink = function (opts) {
        var defaults = {delay: 1000};
        var options = $.extend(defaults, opts);

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


function SMD() {
    jQuery.noConflict();

    var totalHeight;
    var Config;
    var self = this;
    var newItemsCount = 0;
    var audioEventAttached = false;

    /**
     * Objeto que contiene las variables de configuración de PHP
     */
    this.SMDConfig = function () {
        var timeout = 10000,
            scroll = 0,
            ajaxfile = '/ajax/getData.php',
            remoteServer = '',
            audio = false;
        var LANG = [];

        this.setTimeout = function (t) {
            timeout = t * 1000;
        };
        this.getTimeout = function () {
            return timeout;
        };
        this.setScroll = function (s) {
            scroll = s;
        };
        this.getScroll = function () {
            return scroll;
        };
        this.setAjaxFile = function (a) {
            ajaxfile = a;
        };
        this.getAjaxFile = function () {
            if (remoteServer === '') {
                return self.getRootPath() + ajaxfile;
            }

            return ajaxfile;
        };
        this.setLang = function (l) {
            LANG.push(l);
        };
        this.getLang = function (index) {
            return LANG[index];
        };
        this.setRemoteServer = function (m) {
            remoteServer = m;
        };
        this.getRemoteServer = function () {
            return remoteServer;
        };
        this.setAudioEnabled = function (bool) {
            audio = bool;
        };
        this.getAudioEnabled = function () {
            return audio;
        }
    };

    /**
     * Devuelve la URL a la raíz de la web
     */
    this.getRootPath = function () {
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


    this.hex2rgb = function (hexStr) {
        // note: hexStr should be #rrggbb
        var hex = parseInt(hexStr.substring(1), 16);
        var r = (hex & 0xff0000) >> 16;
        var g = (hex & 0x00ff00) >> 8;
        var b = hex & 0x0000ff;
        return 'rgb(' + r + ', ' + g + ', ' + b + ')';
    };

    /**
     * Obtiene mediante AJAX los eventos a mostrar
     */
    this.updateNagiosData = function () {
        var placeHolder = jQuery("#monitor-data");

        this.setTime();

        var url = Config.getRemoteServer() + Config.getAjaxFile();

        jQuery.ajax({
            url: url,
            cache: false,
            timeout: 5000,
            dataType: 'html',
            success: function (data) {
                placeHolder.html(data);

                if (Config.getScroll()) {
                    totalHeight = jQuery(document).height();

                    if (totalHeight > window.innerHeight) {
                        setTimeout(function () {
                            self.pageScroll();
                        }, Config.getTimeout() / 2);
                    }
                }

                var newItems = jQuery('.new');

                if (newItemsCount < newItems.length) {
                    newItemsCount = newItems.length;
                    playBeep();
                }

                newItems.blink({bgcolor_on: self.hex2rgb('#ffff00'), fgcolor_on: self.hex2rgb('#333333')});
            },
            error: function (xhr, textStatus, errorThrown) {
                placeHolder.html("<div id=\"nomessages\" class=\"error\">" + Config.getLang(1) + "<p>" + xhr.status + " " + xhr.statusText + "</p></div>");
            }
        });
    };

    /**
     * Actualizar el contador de refresco
     */
    this.updateCountDown = function () {
        var countdown = jQuery("#refreshing_countdown");
        var remaining = parseInt(countdown.text());
        if (remaining == 0) {
            self.updateNagiosData(placeHolder);
            countdown.text(Config.getTimeout());
        }
        else {
            countdown.text(remaining - 1);
        }
    };

    /**
     * Inserta la hora actual en la cabecera de la página
     */
    this.setTime = function () {
        var d = new Date();

        var curr_date = ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
        var curr_hour = ('0' + d.getHours()).slice(-2);
        var curr_min = ('0' + d.getMinutes()).slice(-2);
        var curr_sec = ('0' + d.getSeconds()).slice(-2);

        jQuery('#hora>h1').html(curr_date + '<br>' + curr_hour + ':' + curr_min + ':' + curr_sec);
    };

    /**
     * Realiza un scroll automático de la página
     */
    this.pageScroll = function () {
        jQuery('body,html').animate(
            {scrollTop: totalHeight},
            Config.getTimeout() / 2,
            function () {
                self.pageUnScroll();
            }
        ).on("mousemove",
            function () {
                jQuery(this).stop(true);
            }
        );
    };

    /**
     * Devuelve el scroll a la posición inicial
     */
    this.pageUnScroll = function () {
        jQuery('body,html').scrollTop(0);
    };

    /**
     * Recargar la página
     */
    this.reloadPage = function () {
        window.location.reload(false);
    };

    this.saveConfig = function (obj) {
        jQuery.ajax({
            url: self.getRootPath() + '/ajax/saveConfig.php',
            type: 'post',
            dataType: 'json',
            data: obj.serialize(),
            success: function (data) {
                var target = jQuery("#result");

                target.removeClass();

                if (data.status == 0) {
                    target.addClass('ok');
                } else {
                    target.addClass('error');
                }

                target.html(data.description);
            }
        });
    };

    this.getNewLivestatusBackend = function () {
        var len = jQuery('.backendLivestatus').length;
        var $html = jQuery('.livestatusTemplate').clone();

        $html.find('[name=\'backend[livestatus][alias]\']')[0].name = "backend[livestatus][" + len + "][alias]]";
        $html.find('[name=\'backend[livestatus][path]\']')[0].name = "backend[livestatus][" + len + "][path]]";
        $html.find('[name=\'backend[livestatus][active]\']')[0].name = "backend[livestatus][" + len + "][active]]";

        return $html.html();
    };

    this.getNewStatusBackend = function () {
        var len = jQuery('.backendStatus').length;
        var $html = jQuery('.statusTemplate').clone();

        $html.find('[name=\'backend[status][alias]\']')[0].name = "backend[status][" + len + "][alias]]";
        $html.find('[name=\'backend[status][path]\']')[0].name = "backend[status][" + len + "][path]]";
        $html.find('[name=\'backend[status][active]\']')[0].name = "backend[status][" + len + "][active]]";

        return $html.html();
    };

    this.getNewZabbixBackend = function () {
        var len = jQuery('.backendZabbix').length;
        var $html = jQuery('.zabbixTemplate').clone();

        $html.find('[name=\'backend[zabbix][alias]\']')[0].name = "backend[zabbix][" + len + "][alias]";
        $html.find('[name=\'backend[zabbix][url]\']')[0].name = "backend[zabbix][" + len + "][url]";
        $html.find('[name=\'backend[zabbix][version]\']')[0].name = "backend[zabbix][" + len + "][version]";
        $html.find('[name=\'backend[zabbix][user]\']')[0].name = "backend[zabbix][" + len + "][user]";
        $html.find('[name=\'backend[zabbix][pass]\']')[0].name = "backend[zabbix][" + len + "][pass]";
        $html.find('[name=\'backend[zabbix][active]\']')[0].name = "backend[zabbix][" + len + "][active]";

        return $html.html();
    };

    this.getNewSMDBackend = function () {
        var len = jQuery('.backendSMD').length;
        var $html = jQuery('.SMDTemplate').clone();

        $html.find('[name=\'backend[smd][alias]\']')[0].name = "backend[smd][" + len + "][alias]";
        $html.find('[name=\'backend[smd][url]\']')[0].name = "backend[smd][" + len + "][url]";
        $html.find('[name=\'backend[smd][token]\']')[0].name = "backend[smd][" + len + "][token]";
        $html.find('[name=\'backend[smd][active]\']')[0].name = "backend[smd][" + len + "][active]";

        return $html.html();
    };

    this.getUpdates = function () {
        jQuery('#updates').load(this.getRootPath() + '/ajax/getUpdates.php');
    };

    this.startSMD = function () {
        jQuery.ajaxSetup({
            global: false,
            timeout: Config.getTimeout() / 2
        });

        this.updateNagiosData();
        setInterval(function () {
            self.updateNagiosData();
        }, Config.getTimeout());
    };

    this.setConfig = function (c) {
        Config = c;
    };

    // Reproducir sonido
    var playBeep = function playBeep() {
        //console.info('BEEP');
        if (Config.getAudioEnabled()) {
            var audio = document.getElementById('audio-alarm');
            var timeout = Config.getTimeout() / 1000;

            // Detectar si la duración del sonido es mayor a la duración del timeout de refresco
            if (audio.duration >= timeout && audioEventAttached === false) {
                audio.addEventListener('timeupdate', function () {
                    audioEventAttached = true;

                    if (audio.currentTime >= timeout - 2) {
                        audio.pause();
                        audio.currentTime = 0;
                    }
                });
            }

            audio.play();
        }
    }
}

var smd = new SMD();
var config = new smd.SMDConfig();