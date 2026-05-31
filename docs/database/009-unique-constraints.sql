-- ============================================================
-- 009 — Constraints de unicidad en productos y categorías
--
-- Añade UNIQUE en name para evitar duplicados a nivel de BD,
-- como segunda capa de protección tras la validación del modelo.
--
-- Se ejecuta tras el seed completo (001–008).
-- ============================================================

SET NAMES utf8mb4;

ALTER TABLE products
    ADD CONSTRAINT uq_products_name UNIQUE (name);

ALTER TABLE categories
    ADD CONSTRAINT uq_categories_name UNIQUE (name);