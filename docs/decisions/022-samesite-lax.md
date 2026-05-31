# Cambio de SameSite: Strict → Lax

## Contexto

La cookie de sesión `primelux_session` se configura en `public/index.php` y
en `Controller::checkInactivity()` mediante `session_set_cookie_params()`.
El atributo `SameSite` controla en qué peticiones el navegador incluye la
cookie automáticamente.

La configuración inicial usaba `SameSite=Strict`.

---

## Problemas detectados

### Problema 1 — Stripe destruía la sesión al volver del pago

Con `Strict`, el navegador no envía la cookie cuando la petición llega desde
un dominio externo, incluso en redirecciones GET. Al completar el pago en
Stripe y redirigir a `/checkout/success`, PHP arrancaba sin sesión:
`requireAuth()` no encontraba `user_id` y mandaba al usuario al login,
aunque el pago se hubiera completado correctamente.

### Problema 2 — El Prefetcher destruía la sesión en segundo plano

El `Prefetcher` de `app.js` precargaba todos los enlaces visibles al cargar
la página, incluyendo el enlace `/logout`. Al hacer prefetch de `/logout`,
el navegador ejecutaba `session_destroy()` en segundo plano, destruyendo la
sesión sin que el usuario pulsara nada.

---

## Solución aplicada

`SameSite` se cambió de `Strict` a `Lax` en `public/index.php` y en
`app/Core/Controller.php`. Adicionalmente, se añadió una blacklist en el
Prefetcher para excluir `/logout` y `/admin` del prefetch automático.

`Lax` permite redirecciones GET desde dominios externos (necesario para
Stripe) y sigue bloqueando peticiones POST externas, que son las que
representan el riesgo real de CSRF.

---

## Archivos modificados

- `public/index.php`
- `app/Core/Controller.php`
- `public/assets/js/app.js`
