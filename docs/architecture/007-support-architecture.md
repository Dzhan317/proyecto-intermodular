# Arquitectura del módulo de soporte

## Contexto

El módulo de soporte permite a los usuarios autenticados abrir conversaciones
con el equipo de administración. Cada conversación tiene un asunto y un hilo
de mensajes. El administrador responde desde el panel admin.
A partir de la Fase 9 el chat funciona en tiempo real mediante polling, sin
necesidad de recargar la página.

---

## Estructura de archivos

```
app/
├── Controllers/
│   ├── SupportController.php     — gestión del soporte para el usuario
│   └── AdminController.php       — métodos support* para el admin
├── Models/
│   └── SupportModel.php          — acceso a BD: conversations y messages
└── Views/
    ├── support/
    │   ├── index.php              — listado de conversaciones del usuario
    │   └── show.php               — chat de una conversación (con polling)
    └── admin/
        ├── support.php            — listado de todas las conversaciones
        └── support-detail.php     — chat desde el lado admin (con polling)

public/assets/js/
└── support-chat.js               — JS compartido: polling y badge
```

---

## Tablas involucradas

```
conversations
  id, user_id, subject, status (open/closed), created_at

messages
  id, conversation_id, user_id, message, is_read, created_at
```

### is_read

`messages.is_read` (TINYINT, default 0) indica si el mensaje ha sido leído
por el destinatario. Se marca como leído al abrir la conversación o al hacer
polling. Se usa para calcular el conteo de mensajes no leídos del badge.

---

## Flujo de una conversación

```
Usuario crea conversación → POST /support
  ├── SupportController::create() valida asunto
  ├── SupportModel::create() inserta en conversations
  └── Redirige a /support/:id

Usuario envía mensaje → POST /support/:id/message
  ├── SupportController::sendMessage() valida y añade mensaje
  ├── Si es AJAX → devuelve JSON con el mensaje creado
  └── Si es POST normal → redirige a /support/:id

Polling (cada 4s) → GET /support/:id/messages?since=N
  ├── SupportController::getMessages() devuelve mensajes nuevos en JSON
  └── Marca como leídos los mensajes del otro interlocutor
```

---

## Control de acceso

| Ruta | Guard |
|---|---|
| POST /support | requireAuth() + CSRF |
| GET /support | requireAuth() |
| GET /support/:id | requireAuth() + conversación propia |
| POST /support/:id/message | requireAuth() + CSRF |
| GET /support/:id/messages | requireAuth() + conversación propia |
| GET /support/unread | isLoggedIn() |
| GET /admin/support | requireAdmin() |
| GET /admin/support/:id | requireAdmin() |
| POST /admin/support/:id/message | requireAdmin() + CSRF |
| GET /admin/support/:id/messages | requireAdmin() |
| GET /admin/support/unread | requireAdmin() |
| POST /admin/support/:id/status | requireAdmin() + CSRF |

---

## Badge de mensajes no leídos

El header consulta el endpoint de no leídos cada 10 segundos.
Devuelve `{ "count": N }`. Si N > 0, muestra el badge rojo sobre el icono.

- **Usuario:** cuenta mensajes con `user_id != session.user_id` en sus conversaciones
- **Admin:** cuenta mensajes de clientes (`role = 'customer'`) con `is_read = 0`

El badge aparece tanto en el header de escritorio como dentro del menú
hamburguesa en móvil, garantizando visibilidad en todos los dispositivos.

---

## Estados de una conversación

| Estado | Significado |
|---|---|
| open | Activa — ambos pueden enviar mensajes |
| closed | Cerrada por el admin — el usuario no puede enviar más mensajes |

Solo el administrador puede cambiar el estado.

---

## Métodos principales de SupportModel

| Método | Descripción |
|---|---|
| `getMessagesSince(int $conversationId, int $lastId)` | Devuelve los mensajes con ID mayor al indicado — usado por el polling para obtener solo los mensajes nuevos |
| `markAsRead(int $conversationId, int $userId)` | Marca como leídos los mensajes del otro interlocutor en una conversación |
| `getUnreadCount(int $userId)` | Cuenta mensajes no leídos para un usuario cliente |
| `getUnreadCountAdmin()` | Cuenta mensajes no leídos enviados por clientes para el administrador |
| `addMessage(int $conversationId, int $userId, string $message)` | Inserta un nuevo mensaje en la conversación |

---

## JS compartido — support-chat.js

`initSupportChat(config)` e `initSupportBadge(config)` se reutilizan en ambas
vistas. El parámetro `isAdmin` controla el color y alineado de las burbujas:

| Contexto | Mensaje propio | Mensaje ajeno |
|---|---|---|
| Usuario | Azul brand, izquierda | Gris secondary, derecha |
| Admin | Rojo error-bg, derecha | Azul brand, izquierda |
