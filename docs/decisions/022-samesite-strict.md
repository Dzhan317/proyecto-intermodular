# Cambio de SameSite: Lax → Strict

## Contexto

La cookie de sesión `primelux_session` se configura en `public/index.php` mediante `session_set_cookie_params()`. El atributo `SameSite` controla en qué peticiones el navegador incluye la cookie automáticamente.

---

## Diferencia entre Lax y Strict

| Atributo | Comportamiento |
|---|---|
| `Lax` | La cookie se envía en navegación de primer nivel (clic en enlace externo) y en peticiones del mismo sitio |
| `Strict` | La cookie solo se envía en peticiones originadas desde el propio dominio. Nunca desde dominios externos |

### Ejemplo práctico

Un usuario recibe un email con un enlace a `primeluxshop.es/orders`:

- Con `Lax` → el navegador envía la cookie al seguir el enlace, el usuario llega autenticado directamente
- Con `Strict` → el navegador no envía la cookie al llegar desde el email, el usuario ve el login. En el siguiente clic dentro de la tienda, la cookie ya se envía y la sesión se recupera

---

## Motivo del cambio

`Strict` elimina completamente el vector de ataque CSRF mediante navegación cruzada. Con `Lax`, peticiones GET iniciadas desde dominios externos incluyen la cookie — aunque `Lax` ya bloquea peticiones POST cruzadas, `Strict` cierra también el vector GET.

Para una tienda con pagos reales procesados por Stripe, el nivel de seguridad adicional de `Strict` está justificado. El coste (el usuario llega sin sesión desde enlaces externos) es mínimo — la sesión se recupera en el primer clic interno sin necesidad de volver a hacer login.

---

## Cambio aplicado

`public/index.php`, dentro de `session_set_cookie_params()`:

```php
// Antes
'samesite' => 'Lax',

// Después
'samesite' => 'Strict',
```

---

## Relación con otras protecciones CSRF del proyecto

PrimeLux implementa protección CSRF por doble vía:

1. **Token CSRF** en todos los formularios POST — validado en `Controller::validateCsrf()`
2. **SameSite Strict** en la cookie de sesión — bloquea peticiones cruzadas a nivel de navegador antes de que lleguen al servidor

Ambas capas son complementarias. El token CSRF protege incluso si `SameSite` fallase (navegadores antiguos). `SameSite Strict` añade una barrera previa independiente del código de la aplicación.