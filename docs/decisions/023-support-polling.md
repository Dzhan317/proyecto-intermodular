# Chat de soporte en tiempo real — Fase 9

## Problema

El chat de soporte funcionaba con recarga completa de página tras cada
mensaje. Para recibir respuestas del otro interlocutor, era necesario
recargar manualmente la vista. Además, no existía ningún indicador visual
de mensajes no leídos.

---

## Solución implementada

### Polling de mensajes nuevos

El navegador comprueba cada 4 segundos si hay mensajes nuevos mediante una
petición GET al endpoint `/support/:id/messages?since=:lastId`. El servidor
devuelve únicamente los mensajes con ID mayor al último recibido. Los mensajes
nuevos se añaden al DOM sin recargar la página.

### Envío por AJAX

El formulario de envío intercepta el `submit` y lo procesa mediante `fetch()`
en lugar de un POST normal. El servidor detecta la cabecera `X-Requested-With: XMLHttpRequest` mediante
`$_SERVER['HTTP_X_REQUESTED_WITH']` y devuelve el mensaje recién creado en
JSON en lugar de redirigir. El mensaje aparece inmediatamente en el chat sin recarga.

### Badge de mensajes no leídos

La tabla `messages` incorpora la columna `is_read` (TINYINT, default 0).
Al abrir una conversación o al hacer polling, el servidor marca como leídos
los mensajes del otro interlocutor. El header consulta cada 10 segundos el
endpoint `/support/unread` y actualiza el badge rojo sobre el icono de
soporte. En móvil, el badge aparece dentro del menú hamburguesa.

---

## Por qué polling y no WebSockets

IONOS hosting compartido no permite conexiones persistentes, por lo que
WebSockets no es viable. El polling cada 4 segundos es suficiente para
la experiencia de soporte — no es mensajería instantánea sino un canal
de atención al cliente. El coste de cada petición es mínimo.

---

## Archivos involucrados

| Archivo | Cambio |
|---|---|
| `app/Models/SupportModel.php` | `getMessagesSince()`, `getUnreadCount()`, `getUnreadCountAdmin()`, `markAsRead()` |
| `app/Controllers/SupportController.php` | `getMessages()`, `unreadCount()`, respuesta JSON en `sendMessage()` |
| `app/Controllers/AdminController.php` | `getSupportMessages()`, `getSupportUnread()`, respuesta JSON en `replySupport()` |
| `app/routes.php` | 4 rutas GET nuevas |
| `app/Views/support/show.php` | Polling y envío AJAX |
| `app/Views/admin/support-detail.php` | Polling y envío AJAX |
| `app/Views/layouts/partials/header.php` | Badge desktop y móvil |
| `public/assets/js/support-chat.js` | `initSupportChat()` e `initSupportBadge()` |
| `docs/database/001-database-schema.sql` | Columna `is_read` incluida en el schema inicial |
