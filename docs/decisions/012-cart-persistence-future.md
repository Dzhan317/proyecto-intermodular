# Persistencia del carrito en BD — mejora futura

## Estado actual

El carrito se almacena en `$_SESSION['cart']` con esta estructura:

```php
$_SESSION['cart'] = [
    'items' => [
        // clave = variant_id (string)
        '5' => [
            'product_id' => 5,
            'variant_id' => 5,
            'name'       => 'RAM Kingston Fury 16GB DDR4',
            'price'      => 49.99,
            'quantity'   => 2,
            'image_url'  => '/assets/img/products/componentes/ram-kingston-fury-ddr4.webp',
            'slug'       => 'ram-kingston-fury-16gb-ddr4',
        ],
    ]
];
```

Los datos se pierden al cerrar el navegador o cuando la sesión expira (8 horas).

---

## Tablas de BD preparadas para la migración

El schema ya incluye las tablas necesarias:

```sql
-- Un carrito por usuario (status: active | abandoned | converted)
CREATE TABLE carts (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    status     ENUM('active','abandoned','converted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Líneas del carrito (una por variante)
CREATE TABLE cart_items (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id    INT UNSIGNED NOT NULL,
    variant_id INT UNSIGNED NOT NULL,
    quantity   SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id)    REFERENCES carts(id)    ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
);
```

---

## Pasos para migrar de sesión a BD

1. Al hacer login, comprobar si el usuario tiene un carrito `active` en BD.
   Si lo tiene, cargarlo en `$_SESSION['cart']`.

2. Cada vez que se modifica la sesión (add/update/remove), sincronizar con BD:
   - `CartController::add()` → INSERT o UPDATE en `cart_items`
   - `CartController::update()` → UPDATE quantity en `cart_items`
   - `CartController::remove()` → DELETE de `cart_items`

3. Al completar el pedido (`/checkout/success`), marcar el carrito como
   `converted` y vaciar `$_SESSION['cart']`.

4. Al cerrar sesión, marcar el carrito como `abandoned` (no eliminarlo,
   sirve para análisis de carritos abandonados).

---

## Beneficios de la migración

- El carrito persiste entre sesiones y dispositivos
- Permite recuperar carritos abandonados (marketing)
- Facilita estadísticas de productos más añadidos al carrito
- Consistencia total entre BD y sesión

---

## Estimación de tiempo

3-4 horas. Requiere refactorizar `CartController` añadiendo
un nuevo `CartService` que gestione la sincronización BD/sesión.
