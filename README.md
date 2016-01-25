## sysMonDash - Systems Monitor Dashboard

---

**sysMonDash** es un panel de monitorización optimizado para entornos con un alto número de elementos a monitorizar mostrando aquellos eventos que requieran de atención.

La capa de monitorización puede ser realizada por cualquier aplicación que utilice el núcleo de Nagios, ya que la interconexión con éste se realiza con MK livestatus http://mathias-kettner.com/checkmk_livestatus.html, que es un plugin que obtiene la información en tiempo real de Nagios y la canaliza a través de un socket UNIX.

Las funcionalidades de **sysMonDash** son las siguientes:

* Filtrado de hosts a mostrar en vista principal
* Filtrado de servicios para NO mostrar en vista principal
* Selección de elementos críticos para mostrar siempre
* Detección de paradas programadas que se hayan establecido, así como su visualización en la vista principal

---

### Instalación

Es necesario disponer de un servidor web con PHP y el plugin MK livestatus correctamente configurado en el sistema de monitorización.

Descargar la aplicación desde https://github.com/nuxsmin/sysMonDash y descomprimirla en la ruta deseada (publicada por el servidor web).

Editar el archivo de configuración y establecer las rutas y URLs según el entorno.

---

**sysMonDash** is an optimized monitoring dashboard for large environments which have large number of items to monitor by showing those events that requires an special attention.

The monitoring layer can be performed by any application that uses Nagios core, because the connection between the dashboard and the monitoring system is done by MK livestatus http://mathias-kettner.com/checkmk_livestatus.html, which it's a plugin that retrieves the real time data from Nagios and it's channeled through an UNIX socket.

The **sysMonDash** key features are:

* Hosts filtering to be shown in the main view
* Services filtering to NOT be shown in the main view
* Critical items selection to be always shown
* Scheduled downtimes detection and showing them in the main view

---

### Installation

You need to have a running PHP webserver and setup the MK livestatus plugin in the monitoring server.

Download the application from https://github.com/nuxsmin/sysMonDash and unpack it in the public webserver root.

Edit the config file and set the paths and URLs according to your environment.

---

**DEMO: http://cloud.cygnux.org/sysMonDash/index.php**

https://github.com/nuxsmin/sysMonDash

http://cygnux.org

