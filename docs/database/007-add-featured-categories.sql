-- Fase 7 — Categorías destacadas en la home
-- Añade columna featured a categories para controlar
-- qué categorías aparecen en la portada de la tienda.

ALTER TABLE categories
ADD COLUMN featured BOOLEAN DEFAULT FALSE AFTER status;

UPDATE categories SET featured = TRUE;
