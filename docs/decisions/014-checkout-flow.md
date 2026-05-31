# Checkout y pagos — Fase 6

## Flujo implementado

```
Carrito → Envío/Dirección → Pago (Stripe Checkout) → Confirmación
```

Envío y dirección se unifican en un solo paso para simplificar el flujo
frente al mockup original que los separaba. Reduce fricción al usuario.

---

## Stripe Checkout

Se usa Stripe Checkout (página alojada en Stripe) en lugar de Stripe Elements
(formulario embebido). Razones:

- Los datos de tarjeta nunca pasan por el servidor — sin PCI DSS
- Sin mantenimiento de formulario de tarjeta
- Stripe gestiona 3D Secure automáticamente
- Más fácil de implementar y auditar

La librería se carga manualmente desde `public/vendor/stripe/init.php`
sin Composer, compatible con hosting compartido de IONOS.

---

## Métodos de pago deshabilitados

PayPal, Google Pay y Apple Pay aparecen visualmente en la vista de pago
pero con badge "Próximamente" y sin funcionalidad.
Solo Stripe (tarjeta) es funcional en esta fase.

---

## Dirección guardada en addresses

Cuando el usuario completa un pedido, su dirección se guarda en la tabla
`addresses` con `is_default = 1`. En el próximo checkout se precarga
automáticamente. La dirección también es visible en `/profile/addresses`.

Lógica en `OrderModel::saveAddress()`:
1. Desactiva todas las direcciones anteriores del usuario
2. Si ya existe una dirección idéntica (mismo street + postal_code), la marca como default
3. Si no existe, inserta una nueva con is_default = 1

---

## Email de confirmación

Al confirmar el pedido en `CheckoutController::success()` se envía
un email desde `no-reply@primeluxshop.es` usando `MailService`.
El email reutiliza la plantilla dark del 2FA con el resumen del pedido:
productos, total, dirección y método de envío.

No interrumpe el flujo si falla — el pedido se crea igualmente.

---

## Tablas de BD involucradas

```
orders       → pedido principal (user_id, status, shipping_*, total, stripe_session_id)
order_items  → líneas del pedido (product_name_snapshot, quantity, unit_price, subtotal)
payments     → registro del pago de Stripe (external_payment_id, payment_status)
addresses    → dirección del usuario (is_default=1 para la última usada)
```

---

## Opciones de envío

| Tipo          | Descripción         | Coste  |
|---------------|---------------------|--------|
| standard      | 2-4 días laborables | Gratis |
| express       | 24 horas            | 4,99 € |
| pickup_point  | Recogida en tienda  | Gratis |

---

## Sesión del checkout

```php
$_SESSION['checkout'] = [
    'street'             => string,
    'city'               => string,
    'province'           => string,
    'postal_code'        => string,
    'country'            => 'España',
    'phone'              => string,
    'shipping_type'      => 'standard' | 'express' | 'pickup_point',
    'shipping_cost'      => float,
    'stripe_session_id'  => string|null,
];
```

Se elimina en `success()` junto con el carrito tras crear el pedido.
