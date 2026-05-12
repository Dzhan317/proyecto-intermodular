# Checkout diferido — Fase 6

## Estado actual (Fase 5)

En Fase 5, el checkout no forma parte del alcance funcional.
El botón "Continuar" del carrito, queda preparado como punto de entrada hacia `/checkout/shipping`, pero esa ruta pertenece a la Fase 6. 

Para evitar un comportamiento controlado en produción, se implementó un mensaje informativo indicando de que el proceso de pago estará disponible en la siguiente fase.

Esto es esperado. El carrito de Fase 5 está completo en sí mismo:
    - añadir productos, modificar cantidades, eliminar y ver el resumen con IVA.
    - El paso de pago se implementa en Fase 6.

---

## Qué implementa la Fase 6

El flujo de checkout se divide en tres pasos:

**Paso 1 — Datos de envío (`/checkout/shipping`)**
El usuario introduce o confirma su dirección de entrega.
Los datos se guardan en `$_SESSION['checkout']` durante el flujo.

**Paso 2 — Método de envío (`/checkout/shipping` POST)**
El usuario selecciona el tipo de envío:
- Estándar (2-5 días) — gratuito a partir de cierto importe
- Express (24h) — coste adicional

**Paso 3 — Pago (`/checkout/payment`)**
Integración con Stripe Checkout. El usuario es redirigido a la
página de Stripe para introducir los datos de tarjeta.
Los datos de tarjeta nunca pasan por el servidor de la aplicación.

**Confirmación (`/checkout/success` y `/checkout/cancel`)**
Stripe redirige a estas URLs según el resultado del pago.
En `/checkout/success` se crea el pedido en la tabla `orders`
y se vacía el carrito de sesión.

---

## Rutas ya definidas en routes.php

```php
$router->get( '/checkout/shipping', 'CheckoutController@shipping');
$router->post('/checkout/shipping', 'CheckoutController@saveShipping');
$router->get( '/checkout/payment',  'CheckoutController@payment');
$router->get( '/checkout/success',  'CheckoutController@success');
$router->get( '/checkout/cancel',   'CheckoutController@cancel');
```

Las rutas ya existen — solo falta el controlador y las vistas.
