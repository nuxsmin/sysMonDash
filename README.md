## sysMonDash - Systems Monitor Dashboard

---

**sysMonDash** es un panel de monitorización optimizado para entornos con un alto número de elementos a monitorizar mostrando aquellos eventos que requieran de atención.

La capa de monitorización puede ser realizada por cualquier aplicación que utilice el núcleo de Nagios, ya que la interconexión con éste se realiza con MK livestatus http://mathias-kettner.com/checkmk_livestatus.html, que es un plugin que obtiene la información en tiempo real de Nagios y la canaliza a través de un socte UNIX.

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

https://github.com/nuxsmin/sysMonDash

http://cygnux.org

