# Fix de sesiones en IONOS — gc_maxlifetime

## Problema

En producción (IONOS hosting compartido), la sesión de usuario se destruía al cerrar el navegador y volver a entrar, incluso con `SESSION_LIFETIME = 604800` (7 días) configurado correctamente en `config.php`.

---

## Causa

PHP almacena las sesiones como archivos temporales en el servidor. El parámetro `gc_maxlifetime` controla cuánto tiempo PHP conserva esos archivos antes de eliminarlos mediante el recolector de basura (`gc` = garbage collector).

En hostings compartidos como IONOS, el servidor puede tener `gc_maxlifetime` configurado a un valor muy bajo (a veces 24 minutos) a nivel global, ignorando el valor definido en la aplicación mediante `session_set_cookie_params()`.

El resultado: la cookie del navegador seguía viva (7 días), pero el archivo de sesión en el servidor ya había sido eliminado. Al presentar la cookie, PHP no encontraba la sesión y creaba una nueva vacía — el usuario aparecía como no autenticado.

---

## Solución

Forzar `gc_maxlifetime` mediante `ini_set()` antes de `session_start()` en `public/index.php`:

```php
ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
ini_set('session.cookie_lifetime', (string) SESSION_LIFETIME);
```

`ini_set()` permite sobreescribir la configuración del servidor a nivel de script, teniendo precedencia sobre el `php.ini` global del hosting.

---

## Por qué ini_set() y no php.ini o .htaccess

| Método | Disponibilidad en IONOS compartido |
|---|---|
| `php.ini` global | Sin acceso — gestionado por IONOS |
| `.htaccess` + `php_value` | No siempre disponible en hosting compartido |
| `ini_set()` en código | ✅ Siempre disponible a nivel de script |

---

## Ubicación del cambio

`public/index.php`, dentro del bloque `if (session_status() === PHP_SESSION_NONE)`,
antes de `session_name()` y `session_set_cookie_params()`.

---

## Relación con SESSION_LIFETIME

Los tres valores quedan sincronizados apuntando al mismo valor (7 días):

```
SESSION_LIFETIME        = 604800  ← definido en config.php
session.gc_maxlifetime  = 604800  ← forzado con ini_set()
session.cookie_lifetime = 604800  ← forzado con ini_set()
```

El control real de la duración de sesión lo sigue haciendo `Controller::checkInactivity()` — estos valores son el techo máximo absoluto.
