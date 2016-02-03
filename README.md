## sysMonDash - Systems Monitor Dashboard

---

**sysMonDash** es un panel de monitorización optimizado para entornos con un alto número de elementos a monitorizar mostrando aquellos eventos que requieran de atención.

Los backend soportados son Nagios, Icinga y Zabbix (experimental).

Es posible utilizar Nagios o Icinga mediante el plugin 'mk_livestatus' (recomendado) o el archivo 'status.dat'.

Las funcionalidades de **sysMonDash** son las siguientes:

* Filtrado de hosts a mostrar en vista principal
* Filtrado de servicios para NO mostrar en vista principal
* Selección de elementos críticos para mostrar siempre
* Detección de paradas programadas que se hayan establecido, así como su visualización en la vista principal

---

### Instalación

Es necesario disponer de un servidor web con PHP y el plugin MK livestatus o la API de Zabbix correctamente configurados en el sistema de monitorización.

Descargar la aplicación desde https://github.com/nuxsmin/sysMonDash y descomprimirla en la ruta deseada (publicada por el servidor web).

Acceder a http://tuservidor.com/sysMonDash/config.php y configurar según tu entorno.

---

**sysMonDash** is an optimized monitoring dashboard for large environments which have a large number of items to monitor by showing those events that requires an special attention.

The supported backends are Nagios, Icinga and Zabbix (experimental).

It's possible to use Nagios or Icinga through the 'mk_livestatus' plugin (recommended) or the 'status.dat' file.

The **sysMonDash** key features are:

* Hosts filtering to be shown in the main view
* Services filtering to NOT be shown in the main view
* Critical items selection to be always shown
* Scheduled downtimes detection and showing them in the main view

---

### Installation

You need to have a running PHP webserver and setup the MK livestatus plugin or Zabbix API in the monitoring server.

Download the application from https://github.com/nuxsmin/sysMonDash and unpack it in the public webserver root.

Point to http://yourserver.com/sysMonDash/config.php and set it according to your environment. 

---

**DEMO: http://sysmondash.cygnux.org**

https://github.com/nuxsmin/sysMonDash

http://cygnux.org


![sysMonDash Main View](http://cloud.cygnux.org/sysMonDash/assets/sysMonDash.png)



