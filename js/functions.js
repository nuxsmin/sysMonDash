function SMD() {
    jQuery.noConflict();

    var Config = new SMDConfig();

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
    function SMDConfig() {
        var timeout = 10000,
            scroll = 0,
            ajaxfile = '/ajax/getData.php',
            remoteServer = '';
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
                return getRootPath() + ajaxfile;
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
    }

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

    jQuery().ready(function () {
        updateNagiosData();
        setInterval(function () {
            updateNagiosData();
        }, Config.getTimeout());
    });

    /**
     * Obtiene mediante AJAX los eventos a mostrar
     */
    function updateNagiosData() {
        var placeHolder = jQuery("#monitor-data");

        setTime();

        jQuery('#icinga_header').attr('src', jQuery('#icinga_header').attr('src'));

        var url = Config.getRemoteServer() + Config.getAjaxFile();
        placeHolder.load(url, function (response, status, xhr) {
            jQuery(this).empty();

            if (status == "error") {
                jQuery(this).html("<div id=\"nomessages\" class=\"error\">" + Config.getLang(1) + "<p>" + xhr.status + " " + xhr.statusText + "</p></div>");
                return;
            }

            jQuery(this).html(response);

            if (Config.getScroll()) {
                totalHeight = jQuery(document).height();

                if (totalHeight > window.innerHeight) {
                    setTimeout(function(){
                        pageScroll();
                    }, Config.getTimeout() / 2);
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
            countdown.text(Config.getTimeout());
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
            Config.getTimeout() / 2,
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
    function reloadPage() {
        window.location.reload(false);
    }

    var saveConfig = function(obj) {
        jQuery.ajax({
            url: getRootPath() + '/ajax/saveConfig.php',
            type: 'post',
            dataType: 'json',
            data: obj.serialize(),
            success: function(data) {
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

    return {
        getSMDConfig: Config,
        saveSMDConfig: saveConfig,
        getRootPath: getRootPath
    }
}