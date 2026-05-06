# Mejoras de sesión, CSRF y UX — Fase 3

## Tiempo de sesión — de 1 hora a 8 horas

`SESSION_LIFETIME` se cambió de 3600 a 28800 segundos.

Una hora es demasiado corto para una tienda online. Un usuario que añade productos al carrito, se distrae y vuelve 90 minutos después perdería su sesión. Los principales e-commerce (Amazon, El Corte Inglés, FNAC) usan entre 8 y 12 horas. 8 horas es el equilibrio estándar entre seguridad y usabilidad.

---

## Comportamiento del CSRF expirado — de exit() a redirección con aviso

`validateCsrf()` en `Controller.php` antes hacía `exit('Invalid CSRF token.')` al detectar un token inválido o sesión expirada. Esto causaba una pantalla en blanco sin explicación para el usuario.

El nuevo comportamiento:
1. Regenera el token CSRF para que el formulario vuelva a funcionar
2. Guarda el mensaje en `$_SESSION['csrf_error']`
3. Redirige de vuelta al formulario con `HTTP_REFERER`

Los layouts `auth.php` y `main.php` muestran el mensaje como una barra de aviso fija en la parte superior (fondo ámbar, icono de advertencia, botón de cierre). Es el patrón estándar de Gmail y GitHub para notificaciones no bloqueantes.

---

## Icono de email — de img externo a SVG inline

El icono del campo de email en `step-email.php` y `forgot-password.php` usaba `<img src="sobre.svg">` con `filter: invert()` en CSS. Este enfoque falla en algunos navegadores cuando el SVG no se carga correctamente o el contexto CSS no aplica el filtro.

Se reemplazó por un SVG inline con `stroke="currentColor"` que hereda el color del CSS sin filtros externos y funciona en todos los navegadores sin depender de una petición de red adicional.

---

## Toggle de contraseña en campo de confirmación

El botón de mostrar/ocultar contraseña se añadió también al campo "Confirmar contraseña" en `register.php`, `reset-password.php` y `profile/security.php`. No había razón técnica ni de seguridad para no tenerlo — su ausencia era un olvido de implementación.

---

## noindex en página de inicio

La página "Próximamente" tiene `<meta name="robots" content="noindex, nofollow">`. Sin esto Google la indexaría, mostrando en resultados de búsqueda una página vacía sin contenido. Se eliminará cuando la tienda esté operativa en Fase 4.

---

## index.php defensivo

El bloque de captura de errores en `index.php` ahora usa `defined('APP_PATH')` antes de intentar cargar la vista de error 500. Si el error ocurre antes de que las constantes estén definidas (por ejemplo, si `config.php` no existe), el fallback renderiza HTML inline sin depender de ninguna constante.
