# Carrito de compra — Fase 5

## Decisión de almacenamiento: sesión PHP

El carrito se almacena en `$_SESSION['cart']` en lugar de en las tablas
`carts` y `cart_items` de la base de datos.

**Por qué sesión y no BD:**
- Implementación más simple y directa (menos código, sin queries adicionales)
- Suficiente para el alcance del proyecto y la demo

**Las tablas `carts` y `cart_items` quedan como base de escalabilidad.**
Una migración futura a carrito persistente permitiría:
- Recuperar el carrito al volver a iniciar sesión
- Guardar carritos abandonados para análisis
- Compartir carrito entre dispositivos

## Carrito sin variantes visibles

Cada producto tiene una única variante "Unidad" generada en el seed.
El carrito usa esa variante por defecto sin mostrar ningún selector al usuario.
El selector de variantes (talla, capacidad, color) se implementaría en Fase 5+ si
se añade la tabla `product_attributes` documentada en `009-advanced-filters-deferred.md`.

## IVA desglosado informativamente

Los precios en la base de datos ya incluyen IVA.
En el resumen del carrito se desglosa de forma informativa:
- Subtotal sin IVA = total / 1.21
- IVA (21%) = total - subtotal
- Total = precio real a pagar

Este es el comportamiento estándar en e-commerce español (IVA incluido en precio).

## Invitados redirigidos al login

Confirma los casos de uso del diagrama: "Gestionar carrito de compra"
es exclusivo del Usuario Registrado. Cualquier acción de carrito
llama a `$this->requireAuth()` que redirige a `/login` si no hay sesión.
