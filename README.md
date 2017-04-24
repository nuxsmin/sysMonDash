## sysMonDash - Systems Monitor Dashboard

---

**sysMonDash** es un panel de monitorización optimizado para entornos con un alto número de elementos a monitorizar mostrando aquellos eventos que requieran de atención.

Los backend soportados son Nagios, Icinga, Zabbix y Check_MK.

Es posible utilizar Nagios o Icinga mediante el plugin 'mk_livestatus' (recomendado) o el archivo 'status.dat'.

Las funcionalidades de **sysMonDash** son las siguientes:

* Selección de múltiples backends.
* Filtrado de hosts a mostrar en vista principal
* Filtrado de servicios para NO mostrar en vista principal
* Selección de elementos críticos para mostrar siempre
* Detección de paradas programadas que se hayan establecido, así como su visualización en la vista principal
* Enlace con backends remotos mediante API JSON

---

### Instalación

Es necesario disponer de un servidor web con PHP y el plugin MK livestatus o la API de Zabbix correctamente configurados en el sistema de monitorización.

Descargar la aplicación desde https://github.com/nuxsmin/sysMonDash y descomprimirla en la ruta deseada (publicada por el servidor web).

Acceder a http://tuservidor.com/sysMonDash/config.php y configurar según tu entorno.

---

**sysMonDash** is an optimized monitoring dashboard for large environments which have a large number of items to monitor by showing those events that requires an special attention.

The supported backends are Nagios, Icinga, Zabbix and Check_MK.

It's possible to use Nagios or Icinga through the 'mk_livestatus' plugin (recommended) or the 'status.dat' file.

The **sysMonDash** key features are:

* Multiple backends selection.
* Hosts filtering to be shown in the main view
* Services filtering to NOT be shown in the main view
* Critical items selection to be always shown
* Scheduled downtimes detection and showing them in the main view
* Link to remote backends through JSON API

---

### Installation

You need to have a running PHP webserver and setup the MK livestatus plugin or Zabbix API in the monitoring server.

Download the application from https://github.com/nuxsmin/sysMonDash and unpack it in the public webserver root.

Point to http://yourserver.com/sysMonDash/config.php and set it according to your environment. 

---

**DEMO: http://sysmondash.cygnux.org** (No disponible/Not availablet)

https://github.com/nuxsmin/sysMonDash

http://cygnux.org


![Main View](http://cygnux.org/wp-content/uploads/2016/02/sysMonDash_v1-624x338.png)



