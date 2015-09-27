<?php
/**
 * sysMonDash Config File
 */

// Título para la página
$pageTitle = 'sysMonDash - Cuadro de Mandos';

// Archivo con información de estado de Icinga
$statusFile = '/var/lib/icinga/status.dat';

// Habilitar el uso de livestatus
$use_livestatus = true;

// Socket livestatus
$livestatus_socket_path = '/var/lib/icinga/rw/live';

// URL del servidor de Icinga/Nagios
$monitorServerUrl = 'http://your-monitor-server.com';

// URL del CGI de Icinga/Nagios
$cgiURL = $monitorServerUrl . '/cgi-bin/icinga';

// Segundos para recarga de página
$refreshValue = 10;

// Mostrar la columna de último check
$colLastcheck = true;

// Mostrar la columna de host
$colHost = true;

// Mostrar la columna de información
$colStatusInfo = true;

// Mostrar la columna de servicio
$colService = true;

// Tiempo para resaltar un evento nuevo en segundos. Para eventos de recuperación, se utiliza
// la mitad de este valor
$newItemTime = 900;

// Número máximo de avisos a mostrar en pantalla
$maxDisplayItems = 200;

// Usar la información de la BD de NagiosQL
$useNagiosQLInfo = false;

// Servidor de BD de NagiosQL. Sólo es necesario si no se usa livestatus
$dbServer = 'localhost';

// Nombre BD de NagiosQL
$dbName = 'nagiosql_db';

// Usuario BD de NagiosQL
$dbUser = 'nagiosql_user';

// Clave BD de NagiosQL
$dbUserPass = 'nagiosql_pass';

// Expresión regular para mostrar hosts en página de inicio
$regexHostShow = '/.*/';

// Expresión regular para no mostrar servicios en página de inicio
$regexServiceNoShow = '/^SSH_.*/';

// Hosts o servicios críticos que se muestran siempre
$criticalItems = array('CRITICAL_SERVER_NAME', 'OTHER_CRITICAL_SERVER_NAME');