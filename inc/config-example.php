<?php
/**
 * sysMonDash Config File
 */

// Idioma por defecto
// Default language
//$language = 'es_ES';
//$language = 'en_US';

// Título para la página
// Page Title
$pageTitle = 'sysMonDash - Cuadro de Mandos';

// Archivo con información de estado de Icinga
// Icinga/Nagios status information file
$statusFile = '/var/lib/icinga/status.dat';

// Habilitar el uso de livestatus
// Enable livestatus
// http://mathias-kettner.de/checkmk_livestatus.html
$use_livestatus = true;

// Ruta al Socket livestatus
// Livestatus' socket path
$livestatus_socket_path = '/var/lib/icinga/rw/live';
//$livestatus_socket_path = '/usr/lib/nagios/mk-livestatus/live';

// URL del cliente para realizar peticiones remotas (CORS)
// Client URL to perform remote requests (CORS)
// ej|ie : http://myclient.cygnux.org
$clientURL = '';

// URL del servidor remoto para las peticiones AJAX
// Remote server URL for AJAX requests
// ej|ie: http://cloud.cygnux.org/sysMonDash
$remoteServer = '';

// URL del servidor de Icinga/Nagios para enlaces
// Icinga/Nagios server URL for links
// ej|ie http://cloud.cygnux.org
$monitorServerUrl = '';

// URL del CGI de Icinga/Nagios
// Icinga/Nagios CGI URL
$cgiURL = $monitorServerUrl . '/cgi-bin/icinga';

// Segundos para recarga de eventos
// Seconds to refresh notices
$refreshValue = 10;

// Mostrar la columna de último check
// Show last check column
$colLastcheck = true;

// Mostrar la columna de host
// Show host column
$colHost = true;

// Mostrar la columna de información
// Show information column
$colStatusInfo = true;

// Mostrar la columna de servicio
// Show service column
$colService = true;

// Tiempo para resaltar un evento nuevo en segundos. Para eventos de recuperación, se utiliza
// la mitad de este valor
// Time in seconds to highlight a new notice. For recovering notices, it uses a half from this value
$newItemTime = 900;

// Número máximo de avisos a mostrar en pantalla
// Maximun number of notices to show
$maxDisplayItems = 200;

// Usar la información de la BD de NagiosQL. Sólo nombres de hosts
// Use NagiosQL DB information. Only hostnames
$useNagiosQLInfo = false;

// Servidor de BD de NagiosQL. Sólo es necesario si no se usa livestatus
// NagiosQL DB server. Only needed if livestatus is not enabled
$dbServer = 'localhost';

// Nombre BD de NagiosQL
// NagiosQL DB
$dbName = 'nagiosql_db';

// Usuario BD de NagiosQL
// NagiosQL DB user
$dbUser = 'nagiosql_user';

// Clave BD de NagiosQL
// NagiosQL DB pass
$dbUserPass = 'nagiosql_pass';

// Expresión regular para mostrar hosts en página de inicio
// Regular expression to show hosts at home page
$regexHostShow = '/.*/';

// Expresión regular para no mostrar servicios en página de inicio
// Regular expression to NOT show services at home page
$regexServiceNoShow = '/^SSH_.*/';

// Hosts o servicios críticos que se muestran siempre
// Regular expression to always show hosts or services at home page
$criticalItems = array('CRITICAL_SERVER_NAME', 'OTHER_CRITICAL_SERVER_NAME');