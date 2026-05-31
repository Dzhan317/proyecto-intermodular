# Checkout y pagos con Stripe — Fase 6

> El flujo completo y las tablas de BD están documentados en `014-checkout-flow.md`.
> Este archivo se centra en la integración específica con Stripe.

---

## Integración elegida: Stripe Checkout

Se usa **Stripe Checkout** (página alojada en Stripe) en lugar de Stripe Elements.
La decisión y sus motivos están documentados en `014-checkout-flow.md`.

---

## Instalación de la librería

La librería `stripe-php v15.10.0` se carga **manualmente** sin Composer, compatible con el hosting compartido de IONOS donde Composer no está disponible.

Estructura en el proyecto:

```
public/vendor/stripe/stripe-php/
  ├── data/
  ├── lib/
  └── init.php        ← punto de entrada; se incluye con require_once
```

Carga en `CheckoutController.php`:

```php
require_once APP_PATH . '/../public/vendor/stripe/stripe-php/init.php';
```

---

## Claves de API

Se definen en `config.php` (excluido de GitHub — contiene credenciales reales):

```php
define('STRIPE_PUBLIC_KEY',     'pk_test_...');   // o pk_live_... en producción
define('STRIPE_SECRET_KEY',     'sk_test_...');   // o sk_live_... en producción
define('STRIPE_WEBHOOK_SECRET', '');              // no se usa — ver sección Webhook
```

---

## Creación de la sesión de Stripe

En `CheckoutController::payment()`:

```php
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items'           => $lineItems,
    'mode'                 => 'payment',
    'success_url'          => APP_URL . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url'           => APP_URL . '/checkout/cancel',
    'customer_email'       => $user['email'],
]);

header('Location: ' . $session->url);
```

Los datos de tarjeta nunca pasan por el servidor de la aplicación.

---

## Confirmación del pago — sin webhook

La confirmación se realiza en `CheckoutController::success()` mediante
`Session::retrieve()`, **sin usar webhooks**:

```php
$session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);

if ($session->payment_status !== 'paid') {
    // redirigir con error
}
// crear pedido en BD, vaciar sesión, enviar email
```

### Por qué no se usa webhook

El webhook es el mecanismo recomendado por Stripe para entornos de producción de alto tráfico porque garantiza la recepción del evento incluso si el usuario cierra el navegador antes de que cargue `/checkout/success`.

Para este proyecto se descartó por las siguientes razones:

- El flujo con `Session::retrieve()` es suficiente para la demo y el MVP
- El hosting compartido de IONOS no garantiza la recepción fiable de webhooks
- Añadir webhook requería `STRIPE_WEBHOOK_SECRET`, endpoint dedicado y
  validación de firma — complejidad innecesaria para el alcance de este proyecto
- El riesgo de pedido creado sin pago real es inexistente: la URL de éxito incluye un `session_id` generado por Stripe que se verifica con
  `Session::retrieve()` antes de crear el pedido. Un `session_id` no puede fabricarse — si el pago no fue completado, Stripe no lo genera.
  
  El único riesgo menor es el inverso: que el usuario cierre el navegador justo después de pagar y antes de que cargue `/checkout/success`, lo que impediría crear el pedido en BD aunque el cobro ya se realizó. Para eso existe el webhook, descartado aquí por las razones anteriores.

`STRIPE_WEBHOOK_SECRET` se define vacío en `config.php` para mantener
la variable disponible como base para una futura integración.

---

## Modo test vs producción

| Entorno | Claves | Tarjeta de prueba |
|---------|--------|-------------------|
| Test    | `pk_test_...` / `sk_test_...` | `4242 4242 4242 4242`, cualquier fecha futura, cualquier CVC |
| Live    | `pk_live_...` / `sk_live_...` | Tarjeta real |

El cambio de test a live es únicamente sustituir las claves en `config.php`.
No requiere cambios en el código.

---

## Cancelación

Si el usuario pulsa "Volver" desde la página de Stripe, es redirigido a `/checkout/cancel`. El carrito se mantiene intacto y se muestra un mensaje informativo. No se crea ningún pedido ni pago en BD.
