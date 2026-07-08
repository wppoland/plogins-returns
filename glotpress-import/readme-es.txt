=== Plogins Returns - Returns and RMA for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, returns, rma, refund, return request
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Requiere complementos: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Permita que los clientes soliciten devoluciones/reembolsos desde su cuenta y administren RMA en el administrador.

== Description ==

Returns añade un flujo de devolución de autoservicio (RMA) simple a WooCommerce. Desde <strong>Mi cuenta → Pedidos</strong>, un cliente abre una solicitud de devolución en un pedido elegible:
seleccione los artículos, establezca una cantidad, elija un motivo y añade una nota opcional. el
La solicitud se guarda como un registro privado, se le envía por correo electrónico y se le asigna un estado que el
el cliente puede seguir desde su cuenta.

Usted revisa y administra cada solicitud en wp-admin en <strong>WooCommerce → Solicitudes de devolución</strong>, moviendo cada una entre solicitada, aprobada, rechazada o completada.
Cualquiera que sea el estado que establezca, es el estado que el cliente ve en su cuenta.

Este es un complemento de solicitud y estado: no mueve dinero. Procesar cualquier reembolso
en la pantalla de pedido normal de WooCommerce; el registro de devolución mantiene la solicitud y
su estado en un solo lugar.

El código fuente y los informes de errores están disponibles en https://github.com/wppoland/plogins-returns.

= Documentation and links =

* <strong>Documentación</strong> - https://plogins.com/es/plogins-returns/docs/
* <strong>Página de complementos</strong> - https://plogins.com/es/plogins-returns/
* <strong>Código fuente</strong> - https://github.com/wppoland/plogins-returns
* <strong>Informes de errores y solicitudes de funciones</strong> - https://github.com/wppoland/plogins-returns/issues


= Features =

* Acción "Solicitar una devolución" en pedidos elegibles en Mi cuenta (lista de pedidos y vista de pedido único).
* Selector de artículos con cantidad por artículo, un menú desplegable de motivos y una nota opcional.
* Propiedad verificada: solo el propietario de un pedido que haya iniciado sesión puede solicitar su devolución.
* Estados de pedidos elegibles configurables y una ventana de devolución (en días).
* Cada solicitud se guarda como un tipo de publicación personalizada privada y se envía por correo electrónico al administrador de la tienda.
* Pantalla de gestión de administración con un flujo de trabajo de estado: solicitado, aprobado, rechazado, completado.
* Lista de estado de atención al cliente en Mi cuenta para que los compradores puedan realizar un seguimiento de sus devoluciones.
* Marcado accesible con un diseño responsivo; Los estilos de escaparate heredan los colores de su tema, por lo que se ubican en temas claros u oscuros sin trabajo adicional.
* Traducción lista (POT incluida) y desinstalación limpia.
* Compatible con HPOS y bloques de carrito/pago.

== Installation ==

1. Cargue el complemento en `/wp-content/plugins/returns`, o instálelo a través de Complementos → Añadir nuevo.
2. Actívalo. WooCommerce debe estar instalado y activo.
3. Vaya a <strong>WooCommerce → Devoluciones<strong> para elegir los estados de pedido elegibles y la ventana de devolución. 4. Los clientes ahora pueden abrir una devolución desde </strong>Mi cuenta → Pedidos</strong> en cualquier pedido elegible.

== Frequently Asked Questions ==

= Does it require WooCommerce? =

Sí. WooCommerce debe estar instalado y activo.

= Which orders can be returned? =

Pedidos en los estados que elija en WooCommerce → Devoluciones (completadas y
Procesando por defecto), dentro de la ventana de devolución que establezcas. Establezca la ventana en 0 para
eliminar el límite de tiempo.

= Does it issue refunds automatically? =

No. Este MVP registra la solicitud y realiza un seguimiento de su estado. Procesar cualquier reembolso en el
pantalla de pedido normal de WooCommerce; el registro de devolución permanece sincronizado con el estado
tú estableces.

= Where do return requests go? =

Cada envío se envía por correo electrónico al administrador de la tienda y se guarda como un "Devolución" privado.
Solicitar" registro en el menú WooCommerce en wp-admin.

= Can a customer return the same order twice? =

No. Una vez que existe una solicitud de devolución para un pedido, la acción se oculta y aparece un aviso.
en su lugar se muestra.


= Does this plugin work on WordPress Multisite? =

Sí. Este complemento es compatible con WordPress Multisite. Activarlo en red o activarlo en sitios individuales; Cada sitio mantiene su propia configuración y datos.

== Screenshots ==

1. La acción "Solicitar una devolución" en un pedido en Mi Cuenta.
2. El formulario de solicitud de devolución: selector de artículo, motivo y nota.

== External Services ==

Las devoluciones no se conectan a ningún servicio externo. No envía datos fuera de tu sitio y no carga scripts, fuentes o API de terceros. Cada solicitud de devolución se almacena localmente en WordPress como un tipo de publicación personalizada privada `returns_rma` (con meta de publicación `_returns_*` para el pedido, cliente, artículos, motivo, nota y estado), y la configuración del complemento se encuentra en las opciones `returns_settings` y `returns_db_version`. El correo electrónico de notificación del administrador se envía a través del propio correo de WordPress de tu sitio (`wp_mail`), por lo que la entrega utiliza cualquier configuración de correo que su servidor o complemento SMTP ya proporcione.

== Changelog ==

= 1.0.1 =
* Primera versión estable.

= 0.1.3 =
* Renombrado a Plogins Returns para WooCommerce para obtener un nombre de complemento más distintivo.

= 0.1.2 =
* Ayudante `Devoluciones\Support\Refunds` con acción `devoluciones/order_refund` para la automatización de reembolsos PRO.

= 0.1.1 =
* `Returns\Support\Reasons` con filtros `returns/reasons` y `returns/reason_label` para análisis y extensiones PRO.

= 0.1.0 =
* Versión inicial: solicitudes de devolución de autoservicio desde Mi cuenta, selector de artículos con motivo y nota, comprobaciones de propiedad, elegibilidad y ventana configurables, correo electrónico del comerciante, un registro privado de solicitud de devolución y un flujo de trabajo de estado de administrador.
