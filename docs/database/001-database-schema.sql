-- Esquema completo de la base de datos.
-- Organizado en 4 bloques: usuarios y seguridad, catálogo, carrito y pedidos,
-- recomendaciones y soporte.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- BLOQUE 1: USUARIOS Y SEGURIDAD
-- ------------------------------------------------------------

CREATE TABLE users (
    id           INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100)  NOT NULL,
    last_name    VARCHAR(150)  NOT NULL,
    email        VARCHAR(150)  UNIQUE NOT NULL,
    password     VARCHAR(255)  NOT NULL,
    role         ENUM('admin','customer')              DEFAULT 'customer',
    status       ENUM('active','inactive','blocked')   DEFAULT 'active',
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE login_attempts (
    id           INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(255)  NOT NULL,
    ip_address   VARCHAR(45)   NOT NULL,
    success      BOOLEAN       NOT NULL,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email      (email),
    INDEX idx_ip_address (ip_address),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE password_resets (
    id           INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(150)  NOT NULL,
    token_hash   CHAR(64)      NOT NULL,
    expires_at   DATETIME      NOT NULL,
    used         BOOLEAN       DEFAULT FALSE,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token_hash (token_hash),
    INDEX idx_email      (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE two_factor_codes (
    id              INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED     NOT NULL,
    code_hash       CHAR(64)         NOT NULL,
    expires_at      DATETIME         NOT NULL,
    created_at      DATETIME         DEFAULT CURRENT_TIMESTAMP,
    used_at         DATETIME         NULL,
    failed_attempts TINYINT UNSIGNED DEFAULT 0,
    blocked_until   DATETIME         NULL,
    request_ip      VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id    (user_id),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE addresses (
    id           INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id      INT UNSIGNED  NOT NULL,
    street       VARCHAR(255)  NOT NULL,
    city         VARCHAR(100)  NOT NULL,
    province     VARCHAR(100)  NOT NULL,
    postal_code  VARCHAR(10)   NOT NULL,
    country      VARCHAR(100)  DEFAULT 'Spain',
    phone        VARCHAR(15),
    is_default   BOOLEAN       DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_addresses_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- BLOQUE 2: CATÁLOGO
-- ------------------------------------------------------------

CREATE TABLE categories (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    slug        VARCHAR(100)  UNIQUE NOT NULL,
    description TEXT,
    parent_id   INT UNSIGNED  NULL,
    status      ENUM('active','inactive') DEFAULT 'active',
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_categories_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id           INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(150)   NOT NULL,
    slug         VARCHAR(150)   UNIQUE NOT NULL,
    description  TEXT,
    base_price   DECIMAL(10,2)  UNSIGNED NOT NULL DEFAULT 0.00,
    status       ENUM('active','inactive') DEFAULT 'active',
    created_at   DATETIME       DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_products_name   (name),
    INDEX idx_products_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_categories (
    product_id  INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id)  REFERENCES products(id)   ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE variants (
    id           INT UNSIGNED   AUTO_INCREMENT PRIMARY KEY,
    product_id   INT UNSIGNED   NOT NULL,
    name         VARCHAR(100)   NOT NULL,
    extra_price  DECIMAL(10,2)  UNSIGNED DEFAULT 0.00,
    stock        INT UNSIGNED   DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_variants_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_images (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED  NOT NULL,
    image_url   VARCHAR(255)  NOT NULL,
    is_main     BOOLEAN       DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- BLOQUE 3: CARRITO Y PEDIDOS
-- ------------------------------------------------------------

CREATE TABLE carts (
    id          BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED     NOT NULL,
    status      ENUM('active','abandoned','converted') DEFAULT 'active',
    created_at  DATETIME         DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME         DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_carts_user (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE cart_items (
    id          BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    cart_id     BIGINT UNSIGNED  NOT NULL,
    variant_id  INT UNSIGNED     NOT NULL,
    quantity    INT UNSIGNED     NOT NULL DEFAULT 1,
    FOREIGN KEY (cart_id)    REFERENCES carts(id)    ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    id                INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED    NOT NULL,
    status            ENUM('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
    shipping_type     ENUM('standard','express','pickup_point') DEFAULT 'standard',
    shipping_cost     DECIMAL(10,2)   UNSIGNED DEFAULT 0.00,
    total             DECIMAL(10,2)   UNSIGNED NOT NULL,
    street            VARCHAR(255),
    city              VARCHAR(100),
    province          VARCHAR(100),
    postal_code       VARCHAR(10),
    country           VARCHAR(100),
    phone             VARCHAR(15),
    stripe_session_id VARCHAR(255)    NULL,
    created_at        DATETIME        DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_orders_user   (user_id, created_at),
    INDEX idx_orders_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
    id                    BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_id              INT UNSIGNED     NOT NULL,
    variant_id            INT UNSIGNED     NOT NULL,
    product_name_snapshot VARCHAR(150),
    quantity              INT UNSIGNED     NOT NULL,
    unit_price            DECIMAL(10,2)    UNSIGNED NOT NULL,
    subtotal              DECIMAL(10,2)    UNSIGNED NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
    id                  BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    order_id            INT UNSIGNED     NOT NULL,
    payment_provider    VARCHAR(50),
    external_payment_id VARCHAR(255),
    payment_method      ENUM('card','paypal','google_pay','apple_pay') NOT NULL,
    payment_status      ENUM('pending','completed','failed') DEFAULT 'pending',
    amount              DECIMAL(10,2)    UNSIGNED NOT NULL,
    currency            VARCHAR(10)      DEFAULT 'EUR',
    paid_at             DATETIME         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_payments_order    (order_id),
    INDEX idx_payments_external (external_payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- BLOQUE 4: RECOMENDACIONES Y SOPORTE
-- ------------------------------------------------------------

CREATE TABLE interactions (
    id          BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED     NOT NULL,
    product_id  INT UNSIGNED     NOT NULL,
    type        ENUM('view','click','cart') NOT NULL,
    created_at  DATETIME         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_interactions_analysis (user_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE view_history (
    id          BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED     NOT NULL,
    product_id  INT UNSIGNED     NOT NULL,
    created_at  DATETIME         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_view_history_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE user_interests (
    user_id           INT UNSIGNED  NOT NULL,
    category_id       INT UNSIGNED  NOT NULL,
    interest_score    INT           DEFAULT 0,
    last_interaction  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, category_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla creada. La pantalla de reseñas se ha dejado para una fase posterior.
CREATE TABLE reviews (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    product_id  INT UNSIGNED  NOT NULL,
    rating      TINYINT UNSIGNED CHECK (rating BETWEEN 1 AND 5),
    comment     TEXT,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, product_id),
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE conversations (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    subject     VARCHAR(255),
    status      ENUM('open','closed') DEFAULT 'open',
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE messages (
    id               BIGINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    conversation_id  INT UNSIGNED     NOT NULL,
    user_id          INT UNSIGNED     NOT NULL,
    message          TEXT             NOT NULL,
    is_read          BOOLEAN          DEFAULT FALSE,
    created_at       DATETIME         DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)         REFERENCES users(id)         ON DELETE CASCADE,
    INDEX idx_messages_conversation (conversation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
