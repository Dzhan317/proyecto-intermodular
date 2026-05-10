# Catálogo de productos — Fase 4

## Alcance de la fase

Implementación del catálogo público de productos: página de inicio con categorías y productos destacados, listado por categoría con filtro de orden y paginación, y detalle de producto con stock e información completa.

---

## Categorías implementadas

Se crearon 6 categorías coherentes con el ciclo formativo DAW:

| Categoría | Slug | Descripción |
|---|---|---|
| Informática | `informatica` | Portátiles, sobremesa |
| Electrónica | `electronica` | Smartphones, tablets |
| Componentes | `componentes` | RAM, SSD, GPU |
| Periféricos | `perifericos` | Ratones, teclados, monitores |
| Software | `software` | Licencias, suscripciones |
| Redes | `redes` | Routers, switches, cableado |

Datos de prueba disponibles en `docs/database/004-seed-data.sql` (24 productos, 6 categorías, variante por defecto por producto).

---

## Estructura de imágenes

Las imágenes de productos se organizan por categoría:

```
public/assets/img/products/
├── informatica/    ← laptop-hp-pavilion-15.jpg ...
├── electronica/    ← iphone-15-pro-256gb.jpg ...
├── componentes/    ← ram-kingston-fury-16gb-ddr4.jpg ...
├── perifericos/    ← raton-logitech-mx-master-3s.jpg ...
├── software/       ← windows-11-pro-licencia.jpg ...
└── redes/          ← router-asus-wifi6-ax6000.jpg ...
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
