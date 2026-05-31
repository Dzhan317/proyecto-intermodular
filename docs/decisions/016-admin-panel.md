# Panel de administración — Fase 7

## Alcance

El panel de administración proporciona a los usuarios con rol `admin` una
interfaz centralizada para gestionar el catálogo, los pedidos y los usuarios
de PrimeLux SmartShop.

---

## Layout propio

El admin usa un layout independiente (`layouts/admin.php`) separado del
`main.php` de la tienda. Motivos:

- El admin no necesita carrito, buscador ni navegación de categorías
- Requiere un sidebar de navegación lateral propio
- El contexto es completamente distinto — herramienta interna vs tienda pública
- Permite aplicar estilos y lógica específicos sin afectar a la tienda

---

## Control de acceso

Todos los métodos del `AdminController` llaman a `requireAdmin()` como primera
instrucción. Este método verifica:

1. Que el usuario está autenticado (`$_SESSION['user_id']` existe)
2. Que su rol es `admin` (`$_SESSION['user_role'] === 'admin'`)
3. Que su sesión no ha expirado por inactividad (límite: 2 horas)

Si cualquiera de las tres condiciones falla, el usuario es redirigido
inmediatamente. El código del método nunca llega a ejecutarse.

---

## Sesión de administrador

La sesión del admin tiene un límite de inactividad de **2 horas**, más
estricto que los 7 días del usuario normal. Es el estándar de plataformas
como PrestaShop y Magento.

El control se implementa en `Controller::checkInactivity()` mediante
`$_SESSION['last_activity']` — si el admin lleva más de 2 horas sin
interactuar con el panel, la sesión expira automáticamente.

---

## CRUD de productos

### Soft delete
Los productos no se eliminan físicamente — se marcan como `status = inactive`.
Motivo: `order_items` referencia las variantes de cada producto. Si el producto
se eliminase, los pedidos históricos perderían la referencia y la BD lanzaría
un error de integridad referencial.

Un producto inactivo desaparece de la tienda pero sigue siendo visible en el
panel admin y los pedidos históricos mantienen su integridad.

### Slug único
Al crear o editar un producto, el slug se genera automáticamente a partir del
nombre. `slugExists()` recibe el ID del producto actual como parámetro de
exclusión para evitar detectar el propio slug como duplicado al editar sin
cambiar el nombre.

### Transacciones
`create()` y `update()` usan transacciones PDO porque afectan a tres tablas:
`products`, `variants` y `product_categories`. Si cualquier INSERT/UPDATE
falla, el `rollBack()` garantiza que ningún cambio parcial queda en la BD.

---

## Precio de coste y márgenes

Se añade `cost_price` a la tabla `products` para calcular el margen bruto.
Solo visible en el panel admin — los clientes no lo ven en ninguna vista.

El dashboard muestra:
- **Ingresos totales** → `SUM(orders.total)` donde `status IN (paid, shipped, delivered)`
- **Coste total** → `SUM(order_items.quantity × products.cost_price)`
- **Margen bruto** → ingresos − coste
- **% margen** → (margen / ingresos) × 100

El listado de productos muestra el % de margen por producto con código de color:
- Verde → ≥ 20%
- Ámbar → 10-19%
- Rojo → < 10%

### Por qué `paid + shipped + delivered`
El estado logístico (`shipped`, `delivered`) no afecta al ingreso — el cobro
ya se realizó mediante Stripe en el momento del pago. Solo `pending` y
`cancelled` se excluyen porque no representan ingresos reales.

---

## CRUD de categorías

Las categorías sí se eliminan físicamente (`DELETE`), a diferencia de los
productos, porque no tienen referencias directas en `order_items`.

Al eliminar una categoría que tiene productos asociados, el sistema los
reasigna automáticamente a la categoría especial **"Sin categoría"** (id fijo
en `CategoryModel::UNCATEGORIZED_ID`) antes de borrar. Esto garantiza que
ningún producto queda huérfano y el admin puede reasignarlos manualmente
cuando quiera.

La categoría "Sin categoría" está **protegida** — `delete()` devuelve
`['deleted' => false, 'protected' => true]` si se intenta eliminar, y el
controlador muestra un mensaje de error informativo. Se mantiene siempre
inactiva para que no aparezca en la tienda pública.

`delete()` devuelve un array con tres claves: `deleted`, `protected` y
`reassigned` (número de productos reasignados). El controlador usa estos
valores para mostrar el mensaje adecuado en cada caso.

El slug de categoría también se genera automáticamente con el mismo mecanismo
de exclusión por ID que los productos.

> Ver `docs/decisions/026-uncategorized-category.md` para el detalle completo.

---

## Gestión de usuarios

Los usuarios nunca se eliminan — solo se bloquean (`status = blocked`).
Motivo: tienen referencias en `orders` — eliminarlos rompería el historial
de pedidos.

Protecciones implementadas:
- Un admin no puede modificar su propio estado
- Solo existen dos roles: `admin` y `customer`


## Dashboard — actualización

El contador de productos muestra ahora **activos / total** en lugar de solo los activos. Esto permite al administrador ver de un vistazo cuántos productos están activos frente al total incluyendo inactivos.

```
$totalProducts = COUNT(*) WHERE status = 'active'   // activos
$allProducts   = COUNT(*)                            // todos
```

La vista muestra: `X / Y` donde X = activos e Y = total.
El contador de clientes sigue el mismo patrón, añadiendo además el desglose de usuarios bloqueados:


```
$activeUsers  = COUNT(*) WHERE role = 'customer' AND status = 'active'
$totalUsers   = COUNT(*) WHERE role = 'customer'
$blockedUsers = $totalUsers - $activeUsers
```

La vista muestra: `X / Y` donde X = clientes activos e Y = total de clientes,
con un subtexto en rojo "N bloqueados" que solo aparece si hay alguno.
El administrador (rol `admin`) no se contabiliza en ninguno de los tres valores.
