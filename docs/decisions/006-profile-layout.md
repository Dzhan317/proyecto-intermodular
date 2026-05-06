# Layout de páginas autenticadas — Fase 3

## Estructura de layouts

A partir de la Fase 3 existen dos layouts distintos:

| Layout | Ruta | Uso |
|---|---|---|
| `auth.php` | `Views/layouts/auth.php` | Login, registro, 2FA, recuperar contraseña |
| `main.php` | `Views/layouts/main.php` | Todas las páginas autenticadas (perfil, tienda, pedidos...) |

Ambos comparten el mismo sistema de diseño (dark theme, Sora, variables CSS) pero tienen estructuras distintas.

---

## Partials reutilizables

El layout `main.php` usa tres partials ubicados en `Views/layouts/partials/`:

- `header.php` — cabecera con logo secundario, búsqueda y iconos de navegación
- `footer.php` — pie con logo, columnas de enlaces y redes sociales
- `profile-sidebar.php` — sidebar de navegación del perfil (Inicio, Seguridad, y placeholders de Pedidos y Soporte)

Separar header y footer en partials evita duplicación cuando en Fases 4-9 se añadan más páginas con el mismo layout.

---

## Decisiones de diseño del perfil

**Solo nombre y apellidos son editables.** El email actúa como identificador único de la cuenta. Cambiarlo requeriría re-verificación (enviar enlace al nuevo email, confirmar, etc.), lo cual añade complejidad desproporcionada al tiempo disponible.

**Modo toggle en lugar de página separada.** La edición de datos personales se activa con un botón "Editar" que muestra el formulario en la misma tarjeta. No se añade una ruta `/profile/edit`. Esto simplifica la navegación y evita una ruta extra.

**2FA siempre activo, sin toggle.** El 2FA es obligatorio en PrimeLux SmartShop. El mockup original mostraba botones de activar/desactivar, pero como el 2FA no puede desactivarse, se muestra como un badge "Activo" de solo lectura.

**Sesión actual simplificada.** Se muestra el dispositivo detectado por User-Agent (navegador y SO) y un botón de cierre de sesión. No requiere guardar datos en BD. Guardar historial completo de sesiones es una mejora futura documentada.

---

## Mejora futura — Sesiones activas completas

Implementar un historial real de sesiones requeriría:
- Nueva tabla `user_sessions` con token, IP, User-Agent, fecha de creación y última actividad
- Guardar el token en la sesión PHP al hacer login
- Mostrar todas las sesiones activas con opción de cerrar cada una individualmente
- Limpiar sesiones expiradas periódicamente

Se estima en 3-4 horas. Queda pendiente si hay tiempo tras completar las fases principales.
