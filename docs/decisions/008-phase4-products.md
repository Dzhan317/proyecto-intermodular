# Catálogo de productos — Fase 4

## Alcance de la fase

Implementación del catálogo público de productos: página de inicio con categorías y productos destacados, listado por categoría con filtro de orden y paginación, y detalle de producto con stock e información completa.

---

## Categorías implementadas

Se crearon 6 categorías coherentes con el ciclo formativo DAW:

| Categoría | Slug | Descripción |
|---|---|---|
| Informática | `computing` | Portátiles, sobremesa |
| Electrónica | `electronics` | Smartphones, tablets |
| Componentes | `components` | RAM, SSD, GPU |
| Periféricos | `peripherals` | Ratones, teclados, monitores |
| Software | `software` | Licencias, suscripciones |
| Redes | `networking` | Routers, switches, cableado |

Datos de prueba disponibles en `docs/database/004-seed-data.sql` (24 productos, 6 categorías, variante por defecto por producto).

---

## Estructura de imágenes

Las imágenes de productos se organizan por categoría:

```
public/assets/img/products/
├── computing/    ← laptop-hp-pavilion-15.webp ...
├── electronics/  ← iphone-15-pro-256gb.webp ...
├── components/   ← ram-kingston-fury-16gb-ddr4.webp ...
├── peripherals/  ← raton-logitech-mx-master-3s.webp ...
├── software/     ← windows-11-pro-licencia.webp ...
└── networking/   ← router-asus-wifi6-ax6000.webp ...
```

El nombre del archivo debe coincidir exactamente con el slug del producto (ej: `laptop-hp-pavilion-15.jpg`). Sin imagen, la tarjeta muestra un placeholder SVG.

---

## Decisiones técnicas

**Sin variantes visibles en Fase 4** — cada producto tiene una variante "Unidad" generada por el seed. El selector de variantes se implementa en Fase 5 junto con el carrito. El botón "Añadir al carrito" existe en la vista pero está deshabilitado.

**Filtros simplificados** — el mockup original incluía filtros por precio, marca y RAM. Para Fase 4 solo se implementa el orden (más recientes, precio ascendente/descendente). Los filtros avanzados quedan como mejora futura documentada.

**Carruseles → grids** — los carruseles horizontales del mockup se reemplazaron por grids CSS responsivos (2 columnas en móvil, 4 en escritorio). Más sencillo de implementar, sin JavaScript extra y funciona igual de bien para una demo.

**Barra de categorías en el header** — la barra de navegación secundaria carga las categorías activas desde BD. Si la BD no está disponible el header funciona sin categorías (try/catch defensivo).

---

## Base preparada para Fase 5

- Selector de cantidad en el detalle de producto (`shop.js: initQuantitySelector`)
- Atributos `data-product-id` y `data-variant-id` en el botón de carrito
- Carpetas de imágenes creadas y estructuradas
- Modelo `ProductModel::getDefaultVariant()` listo para uso en CartController
