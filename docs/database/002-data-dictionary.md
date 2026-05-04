# Diccionario de datos — PrimeLux SmartShop

Describe cada tabla de la base de datos, sus columnas y el propósito de cada campo.

---

## users

Almacena los datos de todos los usuarios registrados.

| Columna    | Tipo                              | Descripción                              |
|------------|-----------------------------------|------------------------------------------|
| id         | INT UNSIGNED AUTO_INCREMENT PK    | Identificador único                      |
| name       | VARCHAR(100)                      | Nombre                                   |
| last_name  | VARCHAR(150)                      | Apellidos                                |
| email      | VARCHAR(150) UNIQUE               | Correo electrónico, usado como login     |
| password   | VARCHAR(255)                      | Contraseña hasheada con bcrypt           |
| role       | ENUM('admin','customer')          | Rol del usuario                          |
| status     | ENUM('active','inactive','blocked')| Estado de la cuenta                     |
| created_at | DATETIME                          | Fecha de registro                        |
| updated_at | DATETIME                          | Última modificación                      |

---

## login_attempts

Registra todos los intentos de inicio de sesión para prevenir ataques de fuerza bruta.

| Columna     | Tipo         | Descripción                                  |
|-------------|--------------|----------------------------------------------|
| id          | INT UNSIGNED | Identificador único                          |
| email       | VARCHAR(255) | Email con el que se intentó acceder          |
| ip_address  | VARCHAR(45)  | IP de origen                                 |
| success     | BOOLEAN      | Si el intento fue exitoso o no               |
| created_at  | DATETIME     | Fecha y hora del intento                     |

---

## password_resets

Tokens de recuperación de contraseña. Cada token expira en 1 hora y solo puede usarse una vez.

| Columna    | Tipo         | Descripción                                  |
|------------|--------------|----------------------------------------------|
| id         | INT UNSIGNED | Identificador único                          |
| email      | VARCHAR(150) | Correo al que pertenece el token             |
| token_hash | CHAR(64)     | SHA-256 del token (nunca se guarda en claro) |
| expires_at | DATETIME     | Fecha de expiración                          |
| used       | BOOLEAN      | Si ya fue utilizado                          |
| created_at | DATETIME     | Fecha de creación                            |

---

## two_factor_codes

Códigos de verificación 2FA enviados por email al iniciar sesión.

| Columna         | Tipo            | Descripción                                  |
|-----------------|-----------------|----------------------------------------------|
| id              | INT UNSIGNED    | Identificador único                          |
| user_id         | INT UNSIGNED FK | Usuario al que pertenece el código           |
| code_hash       | CHAR(64)        | SHA-256 del código (nunca en claro)          |
| expires_at      | DATETIME        | Expiración (15 minutos desde la creación)    |
| used_at         | DATETIME        | Cuándo fue verificado                        |
| failed_attempts | TINYINT         | Intentos fallidos de verificación            |
| blocked_until   | DATETIME        | Bloqueo temporal si se superan los intentos  |
| request_ip      | VARCHAR(45)     | IP desde la que se solicitó el código        |
| created_at      | DATETIME        | Fecha de creación                            |

---

## addresses

Direcciones de envío guardadas por el usuario.

| Columna     | Tipo         | Descripción                           |
|-------------|--------------|---------------------------------------|
| id          | INT UNSIGNED | Identificador único                   |
| user_id     | INT FK       | Usuario propietario                   |
| street      | VARCHAR(255) | Calle y número                        |
| city        | VARCHAR(100) | Ciudad                                |
| province    | VARCHAR(100) | Provincia                             |
| postal_code | VARCHAR(10)  | Código postal                         |
| country     | VARCHAR(100) | País (por defecto: Spain)             |
| phone       | VARCHAR(15)  | Teléfono de contacto para el envío    |
| is_default  | BOOLEAN      | Si es la dirección predeterminada     |

---

## categories

Categorías del catálogo. Soporta subcategorías mediante parent_id.

| Columna     | Tipo         | Descripción                                   |
|-------------|--------------|-----------------------------------------------|
| id          | INT UNSIGNED | Identificador único                           |
| name        | VARCHAR(100) | Nombre visible                                |
| slug        | VARCHAR(100) | URL amigable (ej: electronica)                |
| description | TEXT         | Descripción opcional                          |
| parent_id   | INT FK       | Categoría padre (NULL si es de primer nivel)  |
| status      | ENUM         | active / inactive                             |
| created_at  | DATETIME     | Fecha de creación                             |

---

## products

Productos del catálogo.

| Columna     | Tipo           | Descripción                              |
|-------------|----------------|------------------------------------------|
| id          | INT UNSIGNED   | Identificador único                      |
| name        | VARCHAR(150)   | Nombre del producto                      |
| slug        | VARCHAR(150)   | URL amigable                             |
| description | TEXT           | Descripción larga                        |
| base_price  | DECIMAL(10,2)  | Precio base sin variantes                |
| status      | ENUM           | active / inactive                        |
| created_at  | DATETIME       | Fecha de alta                            |
| updated_at  | DATETIME       | Última modificación                      |

---

## product_categories

Relación muchos a muchos entre productos y categorías.

| Columna     | Tipo         | Descripción       |
|-------------|--------------|-------------------|
| product_id  | INT FK       | Producto          |
| category_id | INT FK       | Categoría         |

---

## variants

Variantes de un producto (talla, color, modelo…). El stock se gestiona por variante.

| Columna     | Tipo          | Descripción                              |
|-------------|---------------|------------------------------------------|
| id          | INT UNSIGNED  | Identificador único                      |
| product_id  | INT FK        | Producto al que pertenece                |
| name        | VARCHAR(100)  | Nombre de la variante (ej: "Talla M")    |
| extra_price | DECIMAL(10,2) | Precio adicional sobre el base           |
| stock       | INT UNSIGNED  | Unidades disponibles                     |

---

## product_images

Imágenes de un producto. Una de ellas es la principal.

| Columna    | Tipo         | Descripción                     |
|------------|--------------|---------------------------------|
| id         | INT UNSIGNED | Identificador único             |
| product_id | INT FK       | Producto al que pertenece       |
| image_url  | VARCHAR(255) | Ruta o URL de la imagen         |
| is_main    | BOOLEAN      | Si es la imagen principal       |

---

## carts

Carritos de compra activos por usuario.

| Columna    | Tipo            | Descripción                                       |
|------------|-----------------|---------------------------------------------------|
| id         | BIGINT UNSIGNED | Identificador único                               |
| user_id    | INT FK          | Usuario propietario                               |
| status     | ENUM            | active / abandoned / converted                    |
| created_at | DATETIME        | Fecha de creación                                 |
| updated_at | DATETIME        | Última modificación                               |

---

## cart_items

Líneas de un carrito.

| Columna    | Tipo            | Descripción                             |
|------------|-----------------|-----------------------------------------|
| id         | BIGINT UNSIGNED | Identificador único                     |
| cart_id    | BIGINT FK       | Carrito al que pertenece                |
| variant_id | INT FK          | Variante añadida                        |
| quantity   | INT UNSIGNED    | Cantidad                                |

---

## orders

Pedidos realizados. Los datos de envío se copian en el momento del checkout (snapshot).

| Columna           | Tipo          | Descripción                                          |
|-------------------|---------------|------------------------------------------------------|
| id                | INT UNSIGNED  | Identificador único                                  |
| user_id           | INT FK        | Usuario que realizó el pedido                        |
| status            | ENUM          | pending / paid / shipped / delivered / cancelled     |
| shipping_type     | ENUM          | standard / express / pickup_point                    |
| shipping_cost     | DECIMAL(10,2) | Coste del envío                                      |
| total             | DECIMAL(10,2) | Total del pedido (con envío)                         |
| street … country  | VARCHAR       | Dirección de envío en el momento del pedido          |
| stripe_session_id | VARCHAR(255)  | ID de sesión de Stripe para verificar el pago        |
| created_at        | DATETIME      | Fecha del pedido                                     |

---

## order_items

Líneas de un pedido. El nombre del producto se guarda como snapshot.

| Columna                | Tipo          | Descripción                                    |
|------------------------|---------------|------------------------------------------------|
| id                     | BIGINT UNSIGNED | Identificador único                          |
| order_id               | INT FK        | Pedido al que pertenece                        |
| variant_id             | INT FK        | Variante comprada                              |
| product_name_snapshot  | VARCHAR(150)  | Nombre en el momento de la compra              |
| quantity               | INT UNSIGNED  | Cantidad                                       |
| unit_price             | DECIMAL(10,2) | Precio unitario en el momento de la compra     |
| subtotal               | DECIMAL(10,2) | quantity × unit_price                          |

---

## payments

Registro de pagos procesados a través de Stripe.

| Columna             | Tipo            | Descripción                                  |
|---------------------|-----------------|----------------------------------------------|
| id                  | BIGINT UNSIGNED | Identificador único                          |
| order_id            | INT FK          | Pedido asociado                              |
| payment_provider    | VARCHAR(50)     | Proveedor (stripe)                           |
| external_payment_id | VARCHAR(255)    | ID del pago en Stripe                        |
| payment_method      | ENUM            | card / paypal / google_pay / apple_pay       |
| payment_status      | ENUM            | pending / completed / failed                 |
| amount              | DECIMAL(10,2)   | Importe pagado                               |
| currency            | VARCHAR(10)     | Moneda (EUR)                                 |
| paid_at             | DATETIME        | Fecha del pago                               |

---

## interactions

Registra las acciones del usuario sobre productos para el motor de recomendación.

| Columna    | Tipo            | Descripción                          |
|------------|-----------------|--------------------------------------|
| id         | BIGINT UNSIGNED | Identificador único                  |
| user_id    | INT FK          | Usuario                              |
| product_id | INT FK          | Producto                             |
| type       | ENUM            | view / click / cart                  |
| created_at | DATETIME        | Fecha de la interacción              |

---

## view_history

Historial de productos vistos por el usuario.

| Columna    | Tipo            | Descripción       |
|------------|-----------------|-------------------|
| id         | BIGINT UNSIGNED | Identificador      |
| user_id    | INT FK          | Usuario            |
| product_id | INT FK          | Producto visto     |
| created_at | DATETIME        | Fecha              |

---

## user_interests

Puntuación de interés del usuario por cada categoría. Se actualiza automáticamente.

| Columna          | Tipo         | Descripción                                       |
|------------------|--------------|---------------------------------------------------|
| user_id          | INT FK       | Usuario                                           |
| category_id      | INT FK       | Categoría                                         |
| interest_score   | INT          | Puntuación acumulada                              |
| last_interaction | TIMESTAMP    | Última vez que el usuario interactuó con la cat.  |

---

## reviews

Reseñas de productos. La tabla está creada pero la pantalla de reseñas queda pendiente.
Ver `docs/decisions/001-reviews-deferred.md` para la justificación.

| Columna    | Tipo         | Descripción                          |
|------------|--------------|--------------------------------------|
| id         | INT UNSIGNED | Identificador único                  |
| user_id    | INT FK       | Usuario que escribió la reseña       |
| product_id | INT FK       | Producto reseñado                    |
| rating     | TINYINT      | Puntuación del 1 al 5                |
| comment    | TEXT         | Texto de la reseña                   |
| created_at | DATETIME     | Fecha                                |

---

## conversations

Conversaciones de soporte entre cliente y administración.

| Columna    | Tipo         | Descripción                  |
|------------|--------------|------------------------------|
| id         | INT UNSIGNED | Identificador único          |
| user_id    | INT FK       | Cliente que abrió el ticket  |
| subject    | VARCHAR(255) | Asunto                       |
| status     | ENUM         | open / closed                |
| created_at | DATETIME     | Fecha de apertura            |

---

## messages

Mensajes dentro de una conversación de soporte.

| Columna         | Tipo            | Descripción                          |
|-----------------|-----------------|--------------------------------------|
| id              | BIGINT UNSIGNED | Identificador único                  |
| conversation_id | INT FK          | Conversación a la que pertenece      |
| user_id         | INT FK          | Autor del mensaje (cliente o admin)  |
| message         | TEXT            | Contenido del mensaje                |
| is_read         | BOOLEAN         | Si fue leído por el destinatario     |
| created_at      | DATETIME        | Fecha de envío                       |
