# Arquitectura del dominio de pedidos

## Contexto

El dominio de pedidos abarca todo el ciclo de vida de una compra: desde la confirmación del pago en Stripe hasta la consulta del historial por parte del usuario o el administrador.

---

## Estructura de archivos

```
app/
├── Controllers/
│   ├── CheckoutController.php   — crea el pedido tras confirmar el pago
│   ├── OrderController.php      — historial y detalle para el usuario
│   └── AdminController.php      — listado y gestión de pedidos en el admin
├── Models/
│   └── OrderModel.php           — acceso a BD: orders, order_items, payments
└── Views/
    ├── orders/
    │   ├── index.php             — listado de pedidos del usuario
    │   └── show.php              — detalle de un pedido del usuario
    └── admin/
        ├── orders.php            — listado de todos los pedidos (admin)
        └── order-detail.php      — detalle de un pedido con cambio de estado
```

---

## Tablas involucradas

```
orders
  id, user_id, status, shipping_type, shipping_cost, total
  street, city, province, postal_code, country, phone
  stripe_session_id, created_at

order_items
  id, order_id, variant_id, product_name_snapshot
  quantity, unit_price, subtotal

payments
  id, order_id, payment_provider, external_payment_id
  payment_method, payment_status, amount, currency
```

### product_name_snapshot

El nombre del producto se copia en `order_items.product_name_snapshot` en el momento de crear el pedido. Esto garantiza que el historial de pedidos siempre muestra el nombre correcto aunque el producto sea editado o eliminado posteriormente en el catálogo.

---

## Flujo de creación de un pedido

```
CheckoutController::success()
  │
  ├── Recupera la sesión de Stripe con Session::retrieve()
  ├── Verifica que payment_status === 'paid'
  ├── Llama a OrderModel::createFromCheckout()
  │     ├── INSERT orders         → pedido principal
  │     ├── INSERT order_items    → una fila por producto del carrito
  │     ├── UPDATE variants       → descuenta el stock de cada variante
  │     └── INSERT payments       → registro del pago de Stripe
  ├── Llama a OrderModel::saveAddress()  → guarda dirección en addresses
  ├── Vacía $_SESSION['cart'] y $_SESSION['checkout']
  └── Redirige a /checkout/success
```

`createFromCheckout()` ejecuta las cuatro operaciones dentro de una **transacción PDO**. Si cualquier operación falla, el `rollBack()` garantiza que no queda ningún registro parcial en la BD ni el stock descontado sin pedido creado.

El descuento de stock usa `GREATEST(0, stock - ?)` para evitar que el stock baje de cero en caso de concurrencia.

---

## Estados de un pedido

| Estado | Significado |
|---|---|
| `pending` | Pedido creado pero pago no confirmado |
| `paid` | Pago confirmado por Stripe — estado inicial habitual |
| `shipped` | Pedido enviado al cliente |
| `delivered` | Pedido entregado |
| `cancelled` | Pedido cancelado |

Los pedidos se crean directamente con estado `paid` porque la creación solo ocurre tras verificar `payment_status === 'paid'` con Stripe. El estado `pending` existe para posibles flujos futuros.

Solo el administrador puede cambiar el estado desde el panel admin. El usuario no puede modificar el estado de sus pedidos.

---

## Control de acceso

| Ruta | Controlador | Método de guardia |
|---|---|---|
| `GET /orders` | `OrderController::index()` | `requireAuth()` |
| `GET /orders/:id` | `OrderController::show()` | `requireAuth()` + verificación de `user_id` |
| `GET /admin/orders` | `AdminController::orders()` | `requireAdmin()` |
| `GET /admin/orders/:id` | `AdminController::showOrder()` | `requireAdmin()` |
| `POST /admin/orders/:id` | `AdminController::updateOrderStatus()` | `requireAdmin()` + CSRF |

`OrderController::show()` verifica adicionalmente que `$order['user_id']` coincide con `$_SESSION['user_id']` para evitar que un usuario acceda al pedido de otro. Si no coincide, redirige a `/orders` sin revelar si el pedido existe.

---

## Separación de responsabilidades

`CheckoutController` crea pedidos. `OrderController` los muestra.
`AdminController` los gestiona. 
`OrderModel` es el único punto de acceso a las tablas `orders`, `order_items` y `payments` — ningún controlador ejecuta SQL directamente sobre estas tablas.
