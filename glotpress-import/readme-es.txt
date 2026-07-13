=== Plogins Returns - Returns and RMA for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, returns, rma, refund, return request
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requires Plugins: woocommerce
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Permite que tus clientes soliciten devoluciones y reembolsos desde su cuenta y gestiona los RMA en la administración.

== Description ==

Returns añade a WooCommerce un flujo de devolución de autoservicio (RMA) sencillo. Desde <strong>Mi cuenta → Pedidos</strong>, un cliente abre una solicitud de devolución en un pedido válido: elige los artículos, indica una cantidad, selecciona un motivo y añade una nota opcional. La solicitud se guarda como un registro privado, se te envía por correo electrónico y recibe un estado que el cliente puede seguir desde su cuenta.

Revisas y gestionas cada solicitud en wp-admin, en <strong>WooCommerce → Solicitudes de devolución</strong>, moviendo cada una por los estados solicitada, aprobada, rechazada o completada. El estado que asignes es el que el cliente ve en su cuenta.

Es un plugin de solicitudes y estados: no mueve dinero. Procesa cualquier reembolso en la pantalla de pedido habitual de WooCommerce; el registro de devolución mantiene la solicitud y su estado en un solo lugar.

El código fuente y los informes de errores están en https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/plogins-returns/docs/
* <strong>Página del plugin</strong> - https://plogins.com/es/plogins-returns/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-returns
* <strong>Informes de errores y peticiones de funciones</strong> - https://github.com/wppoland/plogins-returns/issues


= Features =

* Acción «Solicitar una devolución» en los pedidos válidos de Mi cuenta (lista de pedidos y vista de pedido individual).
* Selector de artículos con cantidad por artículo, un desplegable de motivos y una nota opcional.
* Con comprobación de propiedad: solo el propietario del pedido que haya iniciado sesión puede solicitar su devolución.
* Estados de pedido válidos configurables y una ventana de devolución (en días).
* Cada solicitud se guarda como un tipo de contenido personalizado privado y se envía por correo electrónico al administrador de la tienda.
* Pantalla de gestión en la administración con un flujo de estados: solicitada, aprobada, rechazada, completada.
* Lista de estados visible para el cliente en Mi cuenta, para que los compradores puedan seguir sus devoluciones.
* Marcado accesible con un diseño adaptable; los estilos de la tienda heredan los colores de tu tema, así que encajan en temas claros u oscuros sin trabajo adicional.
* Listo para traducir (incluye POT) y desinstalación limpia.
* Compatible con HPOS y con los bloques de carrito/pago.

== Installation ==

1. Sube el plugin a `/wp-content/plugins/returns` o instálalo desde Plugins → Añadir nuevo.
2. Actívalo. WooCommerce debe estar instalado y activo.
3. Ve a <strong>WooCommerce → Devoluciones</strong> para elegir los estados de pedido válidos y la ventana de devolución.
4. Los clientes ya pueden abrir una devolución desde <strong>Mi cuenta → Pedidos</strong> en cualquier pedido válido.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Sí. WooCommerce debe estar instalado y activo.

= Which orders can be returned? =

Los pedidos en los estados que elijas en WooCommerce → Devoluciones (por defecto, Completado y Procesando), dentro de la ventana de devolución que definas. Pon la ventana en 0 para quitar el límite de tiempo.

= Does it issue refunds automatically? =

No. Este MVP registra la solicitud y hace un seguimiento de su estado. Procesa cualquier reembolso en la pantalla de pedido habitual de WooCommerce; el registro de devolución permanece sincronizado con el estado que asignes.

= Where do return requests go? =

Cada envío se manda por correo electrónico al administrador de la tienda y se guarda como un registro privado «Solicitud de devolución» en el menú de WooCommerce en wp-admin.

= Can a customer return the same order twice? =

No. Una vez que existe una solicitud de devolución para un pedido, la acción se oculta y en su lugar se muestra un aviso.


= Does this plugin work on WordPress Multisite? =

Sí. Este plugin es compatible con WordPress Multisite. Actívalo para toda la red o en sitios concretos; cada sitio conserva sus propios ajustes y datos.

== Screenshots ==

1. La acción «Solicitar una devolución» en un pedido de Mi cuenta.
2. El formulario de solicitud de devolución: selector de artículos, motivo y nota.

== External Services ==

Returns no se conecta a ningún servicio externo. No envía datos fuera de tu sitio y no carga scripts, fuentes ni API de terceros. Cada solicitud de devolución se almacena localmente en WordPress como un tipo de contenido personalizado privado `returns_rma` (con la metainformación de entrada `_returns_*` para el pedido, el cliente, los artículos, el motivo, la nota y el estado), y la configuración del plugin se guarda en las opciones `returns_settings` y `returns_db_version`. El correo de notificación al administrador se envía a través del propio correo de WordPress de tu sitio (`wp_mail`), así que la entrega usa la configuración de correo que ya proporcione tu servidor o tu plugin SMTP.

== Translations ==

Plogins Returns incluye traducciones al polaco, al alemán y al español para la interfaz del plugin. El dominio de texto es `plogins-returns`, por lo que los paquetes de idioma de WordPress.org también pueden sustituir o ampliar estas traducciones incluidas.

== Changelog ==

= 1.0.2 =
* Se han añadido traducciones incluidas al polaco, al alemán y al español para la interfaz del plugin.

= 1.0.1 =
* Primera versión estable.

= 0.1.3 =
* Renombrado a Plogins Returns for WooCommerce para tener un nombre de plugin más distintivo.

= 0.1.2 =
* Ayudante `Returns\Support\Refunds` con la acción `returns/order_refund` para la automatización de reembolsos PRO.

= 0.1.1 =
* `Returns\Support\Reasons` con los filtros `returns/reasons` y `returns/reason_label` para analíticas y extensiones PRO.

= 0.1.0 =
* Versión inicial: solicitudes de devolución de autoservicio desde Mi cuenta, selector de artículos con motivo y nota, comprobaciones de propiedad, elegibilidad y ventana configurables, correo al comerciante, un registro privado de solicitud de devolución y un flujo de estados en la administración.
