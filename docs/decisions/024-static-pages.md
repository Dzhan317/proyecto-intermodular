# Páginas estáticas del footer

## Contexto

El footer de PrimeLux SmartShop tenía varios enlaces apuntando a `#` — sin
contenido real detrás. Concretamente: Preguntas frecuentes, Envíos y
devoluciones, Política de privacidad, Política de cookies y Términos y
condiciones. El enlace de Contacto apuntaba a la misma página.

---

## Decisión

Se implementaron páginas estáticas para todos los enlaces informativos del
footer. El contenido es coherente con los datos reales de la aplicación —
los plazos de envío coinciden con los definidos en
`CheckoutController::SHIPPING_OPTIONS`, y los datos de contacto coinciden
con los de `about/index.php`.

---

## Estructura implementada

| URL | Vista | Descripción |
|---|---|---|
| `/about` | `about/index.php` | Contacto — enlaza a la sección existente |
| `/faq` | `static/faq.php` | Preguntas frecuentes |
| `/shipping` | `static/envios.php` | Envíos y devoluciones con métodos reales |
| `/legal/privacy` | `static/privacidad.php` | Política de privacidad (RGPD) |
| `/legal/cookies` | `static/cookies.php` | Política de cookies |
| `/legal/terms` | `static/terminos.php` | Términos y condiciones |

Las vistas viven en `app/Views/static/`. Usan el layout `main.php` y siguen
el mismo patrón visual que `about/index.php`.

---

## Por qué no se creó un controlador separado

Las páginas son completamente estáticas — no necesitan datos de BD ni lógica
de negocio. Se añadieron como métodos simples al `HomeController` existente
en lugar de crear un `StaticController` nuevo, para no añadir complejidad
innecesaria.

---

## Coherencia con el checkout

Los plazos de envío en `/shipping` coinciden con los definidos en
`CheckoutController::SHIPPING_OPTIONS`:

| Método | Plazo | Precio |
|---|---|---|
| Estándar | 2-4 días laborables | Gratis |
| Express | 24 horas | 4,99 € |
| Recogida en tienda | 24 horas | Gratis |

---

## Devoluciones

La página de envíos menciona el plazo legal de 14 días naturales para
devoluciones, conforme a la Ley General para la Defensa de los Consumidores
y Usuarios. El proceso de devolución se gestiona a través del módulo de
soporte. Queda como mejora futura la automatización mediante un botón en el
historial de pedidos con reembolso directo via Stripe API.
