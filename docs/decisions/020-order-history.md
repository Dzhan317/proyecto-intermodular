# Historial de pedidos — Fase 8

## Alcance

La Fase 8 añade al usuario autenticado la posibilidad de consultar sus pedidos realizados y ver el detalle de cada uno. En el panel de administración se completa la vista de detalle de pedido individual, que ya estaba referenciada en `AdminController::showOrder()` pero cuyo archivo de vista no existía.

---

## Estructura implementada

### Lado usuario

| Elemento | Ruta |
|---|---|
| `OrderController` | `app/Controllers/OrderController.php` |
| Listado de pedidos | `GET /orders` → `app/Views/orders/index.php` |
| Detalle de pedido | `GET /orders/:id` → `app/Views/orders/show.php` |

### Lado admin

| Elemento | Ruta |
|---|---|
| Detalle de pedido | `GET /admin/orders/:id` → `app/Views/admin/order-detail.php` |
| Cambio de estado | `POST /admin/orders/:id` → ya existía en Fase 7 |

---

## Decisiones de diseño

### OrderController separado de ProfileController

Los pedidos tienen su propia ruta (`/orders`) independiente del perfil (`/profile`). Se crea un `OrderController` separado en lugar de añadir
métodos a `ProfileController` por dos motivos:

- Separación de responsabilidades — el perfil gestiona datos personales,
  los pedidos son una entidad de negocio distinta
- Coherencia con el resto de controladores del proyecto — cada dominio
  tiene su propio controlador

### Seguridad en show()

Antes de renderizar el detalle de un pedido, `OrderController::show()`
verifica que `$order['user_id']` coincide con `$_SESSION['user_id']`.
Si no coincide (un usuario intenta acceder al pedido de otro), se redirige a `/orders` sin mostrar ningún mensaje de error que revele la existencia del pedido.

### Subtotal en la vista de detalle

La vista `orders/show.php` calcula el subtotal de línea en PHP con`array_sum(array_column($order['items'], 'subtotal'))` en lugar de
hacer una consulta adicional a la BD. El dato ya está disponible en `$order['items']` gracias a `OrderModel::findById()`, que incluye
los items del pedido con su `subtotal` calculado.

### activeTab en las vistas

Las vistas de pedidos pasan `'activeTab' => 'orders'` al sidebar del
perfil, siguiendo el mismo patrón que las vistas de `profile/`,
`profile/security` y `profile/addresses`. El sidebar resalta la pestaña
activa visualmente con un borde izquierdo de color brand.

---

## Lo que NO se implementa en esta fase

- **Cancelación de pedidos por el usuario** — una vez pagado, el pedido no puede cancelarse desde la interfaz. Queda como posible ampliación futura.
- **Factura PDF descargable** — fuera del alcance del proyecto.
- **Paginación en el listado de pedidos del usuario** — el volumen de pedidos por usuario en esta fase no lo justifica.