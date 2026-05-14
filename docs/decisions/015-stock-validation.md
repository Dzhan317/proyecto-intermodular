# Validación de stock en el carrito — Fase 6

## Problema

El método `CartController::update()` no validaba el stock real del producto al modificar la cantidad en el carrito. Un usuario podía introducir cualquier cantidad en el selector y el sistema la aceptaba sin límite, lo que permitía crear pedidos con más unidades de las disponibles en BD.

El método `add()` sí aplicaba `min($quantity, $variant['stock'])` correctamente,
pero `update()` lo saltaba por completo.

---

## Solución implementada

### CartController::update()

Antes de guardar la nueva cantidad, se consulta el stock real desde BD:

```php
$variant  = $productModel->getDefaultVariant($item['product_id']);
$maxStock = $variant ? (int) $variant['stock'] : ($item['stock'] ?? 1);

$item['stock']    = $maxStock;           // refresca snapshot en sesión
$item['quantity'] = min($quantity, $maxStock);  // aplica límite
```

Si la cantidad solicitada supera el stock, se recorta y se muestra un mensaje
de error claro al usuario antes de redirigir al carrito.

### Snapshot de stock en sesión

El array de ítems del carrito almacena ahora el campo `stock` como snapshot
del stock en el momento de añadir o actualizar el producto:

```php
$items[$key] = [
    // ...campos existentes...
    'stock' => $maxStock,   // campo añadido en Fase 6
];
```

Este snapshot sirve para dos propósitos:

1. **Mostrar badges de stock** en la vista del carrito sin consultar BD
   en cada carga de página
2. **Fallback** en `update()` si la consulta a BD fallase

El snapshot se refresca desde BD cada vez que el usuario modifica
la cantidad, garantizando que siempre esté actualizado.

---

## Badges de stock

Se implementan tres niveles visuales coherentes en `product-card.php`
(partial reutilizable), `show.php` (detalle de producto) y `cart/index.php`:

| Condición | Badge | Color |
|-----------|-------|-------|
| `stock === 0` | Sin stock | Rojo sólido |
| `stock === 1` | ¡Última unidad! | Rojo translúcido |
| `stock <= 3` | Quedan X | Ámbar |
| `stock > 3` | En stock / sin badge en card | Verde |

Al usar un partial (`product-card.php`), el badge aparece automáticamente
en el listado de categorías, los relacionados del carrito y los relacionados
del detalle de producto — sin duplicar código.

### Dónde aparece cada badge

| Vista | Badge de stock | Motivo |
|-------|---------------|--------|
| `product-card.php` | ✅ Sin stock / Última unidad / Quedan X | Urgencia en navegación y decisión |
| `show.php` | ✅ Todos los niveles | Decisión final de compra |
| `cart/index.php` | ✅ Aviso si cantidad > stock | Prevenir error antes del checkout |
| `home.php` | ❌ | Escaparate, no decisión de compra |

---

## Por qué no se valida en el frontend

El selector `+`/`−` del carrito deshabilita visualmente el botón `+` cuando se alcanza el stock (`pointer-events-none`), pero esta restricción es solo orientativa — cualquier manipulación del DOM la saltaría.

La validación real siempre ocurre en el servidor (`update()`), siguiendo el principio de nunca confiar en datos del cliente.