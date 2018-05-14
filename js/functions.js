$.noConflict();

/**
 * Función para activar el parpadeo de los eventos recientes
 */
(function ($) {
    $.fn.highlight = function (opts) {
        var defaults = {delay: 1000};
        var options = $.extend(defaults, opts);

        return this.each(function () {
            var obj = $(this);

            $(obj).css("color", options.fgcolor_on);
            $(obj).css("background-color", options.bgcolor_on);
        });
    }
}(jQuery));

var smd = (function ($) {
    "use strict";

    var totalHeight;
    var newItemsCount = 0;
    var audioEventAttached = false;

    /**
     * Objeto que contiene las variables de configuración de PHP
     */
    var config = {
        timeout: 10000,
        scroll: 0,
        ajaxfile: "/ajax/getData.php",
        remoteServer: '',
        audio: false,
        LANG: []
    };

    /**
     * Objeto para manejar la configuración
     */
    var Config = {
        setTimeout: function (t) {
            config.timeout = t * 1000;
        },
        getTimeout: function () {
            return config.timeout;
        },
        setScroll: function (s) {
            config.scroll = s;
        },
        getScroll: function () {
            return config.scroll;
        },
        setAjaxFile: function (a) {
            config.ajaxfile = a;
        },
        getAjaxFile: function () {
            if (config.remoteServer === "") {
                return getRootPath() + config.ajaxfile;
            }

            return config.ajaxfile;
        },
        setLang: function (l) {
            config.LANG.push(l);
        },
        getLang: function (index) {
            return config.LANG[index];
        },
        setRemoteServer: function (m) {
            config.remoteServer = m;
        },
        getRemoteServer: function () {
            return config.remoteServer;
        },
        setAudioEnabled: function (bool) {
            config.audio = bool;
        },
        getAudioEnabled: function () {
            return config.audio;
        }
    };

    /**
     * Devuelve la URL a la raíz de la web
     */
    var getRootPath = function () {
        var path = window.location.pathname.split("/");
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
     * Convertir un color de hexadecimal a RGB
     *
     * @param hexStr
     * @returns {string}
     */
    var hex2rgb = function (hexStr) {
        // note: hexStr should be #rrggbb
        var hex = parseInt(hexStr.substring(1), 16);
        var r = (hex & 0xff0000) >> 16;
        var g = (hex & 0x00ff00) >> 8;
        var b = hex & 0x0000ff;
        return "rgb(" + r + ", " + g + ", " + b + ")";
    };

    /**
     * Obtiene mediante AJAX los eventos a mostrar
     */
    var updateNagiosData = function () {
        var placeHolder = $("#monitor-data");

        setTime();

        var url = Config.getRemoteServer() + Config.getAjaxFile();

        $.ajax({
            url: url,
            cache: false,
            timeout: Config.getTimeout(),
            dataType: "html",
            success: function (data) {
                placeHolder.html(data);

                if (Config.getScroll()) {
                    totalHeight = $(document).height();

                    if (totalHeight > window.innerHeight) {
                        setTimeout(function () {
                            pageScroll();
                        }, Config.getTimeout() / 2);
                    }
                }

                var newItems = $('.new');

                if (newItemsCount < newItems.length) {
                    newItemsCount = newItems.length;
                    playBeep();
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                placeHolder.html("<div id=\"nomessages\" class=\"error\">" + Config.getLang(1) + "<p>" + xhr.status + " " + xhr.statusText + "</p></div>");
            }
        });
    };

    /**
     * Actualizar el contador de refresco
     */
    var updateCountDown = function () {
        var countdown = $("#refreshing_countdown");
        var remaining = parseInt(countdown.text());

        if (remaining === 0) {
            updateNagiosData();
            countdown.text(Config.getTimeout());
        }
        else {
            countdown.text(remaining - 1);
        }
    };

    /**
     * Inserta la hora actual en la cabecera de la página
     */
    var setTime = function () {
        var d = new Date();

        var curr_date = ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
        var curr_hour = ("0" + d.getHours()).slice(-2);
        var curr_min = ("0" + d.getMinutes()).slice(-2);
        var curr_sec = ("0" + d.getSeconds()).slice(-2);

        $("#hora").find("h1").html(curr_date + "<br>" + curr_hour + ":" + curr_min + ":" + curr_sec);
    };

    /**
     * Realiza un scroll automático de la página
     */
    var pageScroll = function () {
        $("body").animate(
            {scrollTop: totalHeight},
            Config.getTimeout() / 2,
            function () {
                pageUnScroll();
            }
        ).on("mousemove",
            function () {
                $(this).stop(true);
            }
        );
    };

    /**
     * Devuelve el scroll a la posición inicial
     */
    var pageUnScroll = function () {
        $("body").scrollTop(0);
    };

    /**
     * Recargar la página
     */
    var reloadPage = function () {
        window.location.reload(false);
    };

    /**
     * Guardar la configuración
     *
     * @param obj
     */
    var saveConfig = function (obj) {
        $.ajax({
            url: getRootPath() + "/ajax/saveConfig.php",
            type: "post",
            dataType: "json",
            data: obj.serialize(),
            success: function (data) {
                alertify.logPosition("bottom right");

                if (data.status == 0) {
                    $('#warn-save').hide();
                    alertify.delay(5000).closeLogOnClick(true).success(data.description)
                } else {
                    alertify.error(data.description);
                    // target.addClass('error');
                }
            }
        });
    };

    /**
     * Comprobar actualizaciones
     */
    var getUpdates = function () {
        $("#updates").load(getRootPath() + "/ajax/getUpdates.php");
    };

    /**
     * Inicializar sysMonDash
     */
    var startSMD = function () {
        $.ajaxSetup({
            global: false,
            timeout: Config.getTimeout() / 2
        });

        updateNagiosData();
        setInterval(function () {
            updateNagiosData();
        }, Config.getTimeout());
    };

    /**
     * Generar un hash aleatorio
     *
     * @param length
     * @returns {string}
     */
    var makeHash = function (length) {
        var hash = "";
        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++) {
            hash += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        return hash;
    };

    /**
     * Avisar para guardar la configuración
     */
    var warnConfig = function () {
        alertify.logPosition("bottom right");
        alertify.warn(Config.getLang(5));

        var warnSave = $('#warn-save');

        if (warnSave.css("display") !== "block") {
            warnSave.show();
        }
    };

    /**
     * Establecer los eventos para la vista de configuración
     */
    var setConfigHooks = function () {
        var form = $("#frmConfig");

        form.on("submit", function (e) {
            e.preventDefault();
            saveConfig(form);
        }).find("select").each(function () {
            var sel = $(this);
            sel.val(sel.data("selected"));
        });

        form.on("change", "input", function (e) {
            var warnSave = $("#warn-save");

            if (warnSave.css("display") !== "block") {
                warnSave.show();
            }
        });

        $(".add-backend").on("click", function (e) {
            e.preventDefault();

            switch ($(this).data("type")) {
                case 'status':
                    backends.add.status();
                    break;
                case 'livestatus':
                    backends.add.livestatus();
                    break;
                case 'checkmk':
                    backends.add.checkmk();
                    break;
                case 'zabbix':
                    backends.add.zabbix();
                    break;
                case 'smd':
                    backends.add.smd();
                    break;
                case 'dummy':
                    backends.add.dummy();
                    break;
            }
        });

        $('#backends-config-container').on("click", ".backendDelete", function (e) {
            e.preventDefault();

            var res = window.confirm(Config.getLang(0));

            if (res === true) {
                var $parent = $(this).parent().parent(".backendConfig");

                $parent.slideUp('slow', function () {
                        $parent.remove();
                    }
                );

                warnConfig();
            }
        }).on('click', '.backendCheckSMD', function (e) {
            e.preventDefault();

            backends.check.smd($(this));
        }).on("click", ".backendCheckZabbix", function (e) {
            e.preventDefault();

            backends.check.zabbix($(this));
        });

        $(".btn-gen-token, .btn-gen-pass").click(function (e) {
            var hash = makeHash(32);

            document.getElementById(this.getAttribute("data-dst")).value = hash;
            $(this).next(".fa-eye").attr("title", hash);
        });

        $(".fa-eye").click(function () {
            var title = this.getAttribute("title");
            if (title !== "") {
                alertify.alert(title);
            }
        });

        $("#special_config_pass").on("keyup", function () {
            $(this).parent().find(".fa-eye").attr("title", this.value);
        });

        $("#btnBack").click(function () {
            location.href = getRootPath();
        });

        $(".container-state").on("click", function (e) {
            var container = document.getElementById(this.getAttribute("data-container"));
            var state = container.getAttribute("aria-expanded");

            if (state == "true") {
                container.setAttribute("aria-expanded", "false");
                $(this).removeClass("fa-caret-up").addClass("fa-caret-down");
                $(container).slideUp("slow");
            } else {
                container.setAttribute("aria-expanded", "true");
                $(this).removeClass("fa-caret-down").addClass("fa-caret-up");
                $(container).slideDown("slow");
            }
        });
    };

    /**
     *
     * @type {{add: {livestatus: livestatus, status: status, checkmk: checkmk, zabbix: zabbix, smd: smd, dummy: dummy}, get: {livestatus: livestatus, status: status, checkmk: checkmk, zabbix: zabbix, smd: smd, dummy: dummy}}}
     */
    var backends = {
        add: {
            livestatus: function () {
                var el = $('<div/>', {
                    class: "backendLivestatus backendConfig",
                    html: backends.get.livestatus()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            },
            status: function () {
                var el = $('<div/>', {
                    class: "backendStatus backendConfig",
                    html: backends.get.status()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            },
            checkmk: function () {
                var el = $('<div/>', {
                    class: "backendCheckMK backendConfig",
                    html: backends.get.checkmk()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            },
            zabbix: function () {
                var el = $('<div/>', {
                    class: "backendZabbix backendConfig",
                    html: backends.get.zabbix()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            },
            smd: function () {
                var el = $('<div/>', {
                    class: "backendSMD backendConfig",
                    html: backends.get.smd()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            },
            dummy: function () {
                var el = $('<div/>', {
                    class: "backendDummy backendConfig",
                    html: backends.get.dummy()
                }).hide().appendTo("#backends-config-container").slideDown("slow");

                window.scroll(0, el.offset().top);
            }
        },
        get: {
            livestatus: function () {
                var len = $(".backendLivestatus").length;
                var $html = $(".livestatusTemplate").clone();

                $html.find("[name='backend[livestatus][alias]']")[0].name = "backend[livestatus][" + len + "][alias]]";
                $html.find("[name='backend[livestatus][path]']")[0].name = "backend[livestatus][" + len + "][path]]";
                $html.find("[name='backend[livestatus][active]']")[0].name = "backend[livestatus][" + len + "][active]]";

                return $html.html();
            },
            status: function () {
                var len = $(".backendStatus").length;
                var $html = $(".statusTemplate").clone();

                $html.find("[name='backend[status][alias]']")[0].name = "backend[status][" + len + "][alias]]";
                $html.find("[name='backend[status][path]']")[0].name = "backend[status][" + len + "][path]]";
                $html.find("[name='backend[status][active]']")[0].name = "backend[status][" + len + "][active]]";

                return $html.html();
            },
            checkmk: function () {
                var len = $(".backendCheckMK").length;
                var $html = $(".checkmkTemplate").clone();

                $html.find("[name='backend[checkmk][alias]']")[0].name = "backend[checkmk][" + len + "][alias]]";
                $html.find("[name='backend[checkmk][path]']")[0].name = "backend[checkmk][" + len + "][path]]";
                $html.find("[name='backend[checkmk][active]']")[0].name = "backend[checkmk][" + len + "][active]]";

                return $html.html();
            },
            zabbix: function () {
                var len = $(".backendZabbix").length;
                var $html = $(".zabbixTemplate").clone();

                $html.find("[name='backend[zabbix][alias]']")[0].name = "backend[zabbix][" + len + "][alias]";
                $html.find("[name='backend[zabbix][url]']")[0].name = "backend[zabbix][" + len + "][url]";
                $html.find("[name='backend[zabbix][version]']")[0].name = "backend[zabbix][" + len + "][version]";
                $html.find("[name='backend[zabbix][user]']")[0].name = "backend[zabbix][" + len + "][user]";
                $html.find("[name='backend[zabbix][pass]']")[0].name = "backend[zabbix][" + len + "][pass]";
                $html.find("[name='backend[zabbix][level]']")[0].name = "backend[zabbix][" + len + "][level]";
                $html.find("[name='backend[zabbix][active]']")[0].name = "backend[zabbix][" + len + "][active]";

                return $html.html();
            },
            smd: function () {
                var len = $(".backendSMD").length;
                var $html = $(".SMDTemplate").clone();

                $html.find('[name=\'backend[smd][alias]\']')[0].name = "backend[smd][" + len + "][alias]";
                $html.find('[name=\'backend[smd][url]\']')[0].name = "backend[smd][" + len + "][url]";
                $html.find('[name=\'backend[smd][token]\']')[0].name = "backend[smd][" + len + "][token]";
                $html.find('[name=\'backend[smd][show_in_api]\']')[0].name = "backend[smd][" + len + "][show_in_api]";
                $html.find('[name=\'backend[smd][active]\']')[0].name = "backend[smd][" + len + "][active]";

                return $html.html();
            },
            dummy: function () {
                var len = $(".backendDummy").length;
                var $html = $(".dummyTemplate").clone();

                $html.find('[name=\'backend[dummy][alias]\']')[0].name = "backend[dummy][" + len + "][alias]";
                $html.find('[name=\'backend[dummy][path]\']')[0].name = "backend[dummy][" + len + "][path]";
                $html.find('[name=\'backend[dummy][image]\']')[0].name = "backend[dummy][" + len + "][image]";
                $html.find('[name=\'backend[dummy][active]\']')[0].name = "backend[dummy][" + len + "][active]";

                return $html.html();
            }
        },
        check: {
            zabbix: function ($obj) {
                var parent = $obj.parent().parent(".backendConfig");

                var version = parent.find(".backend_zabbix_version").val();
                var url = parent.find(".backend_zabbix_url").val();
                var user = parent.find(".backend_zabbix_user").val();
                var pass = parent.find(".backend_zabbix_pass").val();

                if (version === '' || url === '') {
                    alertify.alert(Config.getLang(4));
                    return;
                }

                var checkData = {url: url, version: version, user: user, pass: pass};
                var ajaxData = {action: "zabbixBackend", data: JSON.stringify(checkData)};

                checkConfig(ajaxData);
            },
            smd: function ($obj) {
                var parent = $obj.parent().parent(".backendConfig");

                var url = parent.find(".backend_smd_url").val();
                var token = parent.find(".backend_smd_token").val();

                if (url === "") {
                    alertify.alert(Config.getLang(4));
                    return;
                }

                var checkData = {url: url, action: 10, token: token};
                var ajaxData = {action: 'smdBackend', data: JSON.stringify(checkData)};

                checkConfig(ajaxData);
            }
        }
    };

    /**
     * Reproducir sonido
     */
    var playBeep = function playBeep() {
        //console.info('BEEP');
        if (Config.getAudioEnabled()) {
            var audio = document.getElementById("audio-alarm");
            var timeout = Config.getTimeout() / 1000;

            // Detectar si la duración del sonido es mayor a la duración del timeout de refresco
            if (audio.duration >= timeout && audioEventAttached === false) {
                audio.addEventListener("timeupdate", function () {
                    audioEventAttached = true;

                    if (audio.currentTime >= timeout - 2) {
                        audio.pause();
                        audio.currentTime = 0;
                    }
                });
            }

            audio.play();
        }
    };

    /**
     * Comprobar parámetros de configuración mediante AJAX
     *
     * @param ajaxData
     */
    var checkConfig = function (ajaxData) {
        $.ajax({
            url: getRootPath() + "/ajax/checkConfig.php",
            type: "post",
            dataType: "json",
            data: ajaxData,
            success: function (data) {
                var msg;

                if (data.status === undefined || data.status !== 0) {
                    msg = Config.getLang(3) + "<br>" + Config.getLang(2) + " " + data.description || "";
                    alertify.alert(msg);
                } else {
                    msg = Config.getLang(1) + "<br>" + Config.getLang(2) + ' ' + data.description;
                    alertify.alert(msg);
                }

            }
        });
    };

    return {
        startSMD: startSMD,
        getUpdates: getUpdates,
        setConfigHooks: setConfigHooks,
        Config: Config
    }
})(jQuery);
