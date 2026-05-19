-- ============================================================
-- Datos adicionales — Multicategoría (32 productos)
-- 8 categorías coherentes con el anteproyecto
-- Incluye brand, cost_price, variantes e imágenes
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Categorías multicategoría ─────────────────────────────
INSERT INTO categories (name, slug, description, parent_id, featured) VALUES
('Moda',         'fashion',     'Ropa, camisetas, pantalones y prendas para hombre y mujer',         NULL, FALSE),
('Calzado',      'footwear',    'Zapatillas, botas, sandalias y zapatos para todos los estilos',     NULL, FALSE),
('Accesorios',   'accessories', 'Bolsos, carteras, cinturones, relojes y complementos de moda',      NULL, FALSE),
('Perfumería',   'perfumery',   'Perfumes, colonias, cremas y productos de cuidado personal',        NULL, FALSE),
('Hogar',        'home',        'Decoración, textil para el hogar, iluminación y menaje de cocina',  NULL, FALSE),
('Alimentación', 'food',        'Productos gourmet, conservas, bebidas y artículos de despensa',     NULL, FALSE),
('Deporte',      'sports',      'Ropa deportiva, calzado de entrenamiento y equipamiento fitness',   NULL, FALSE),
('Juguetes',     'toys',        'Juguetes educativos, de construcción y entretenimiento para niños', NULL, FALSE);

-- ── Productos — Moda (4) ──────────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Camiseta Básica Algodón Blanca',   'Levi\'s',    'camiseta-basica-algodon-blanca',   'Camiseta de algodón 100% peinado, corte regular, cuello redondo reforzado. Disponible en talla S a XXL. Lavable a máquina.',                                    19.99,  11.99),
('Pantalón Vaquero Slim Fit',        'Levi\'s',    'pantalon-vaquero-slim-fit',        'Vaquero de corte slim con tejido denim elástico 98% algodón 2% elastano. Cinco bolsillos, cierre con botón y cremallera.',                                      59.99,  37.99),
('Vestido Floral Verano',            'Zara',       'vestido-floral-verano',            'Vestido midi de tirantes con estampado floral, escote en V y falda fluida. Tejido viscosa ligero, ideal para días cálidos.',                                    39.99,  23.99),
('Chaqueta Vaquera Clásica',         'Levi\'s',    'chaqueta-vaquera-clasica',         'Chaqueta denim de corte regular con botones metálicos, dos bolsillos en el pecho y dos laterales. Tejido 100% algodón lavado.',                                79.99,  51.99);

-- ── Productos — Calzado (4) ───────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Zapatillas Running Air Max',       'Nike',       'zapatillas-running-air-max',       'Zapatilla de running con amortiguación de aire en el talón, upper de malla transpirable y suela de goma de alto agarre. Tallas 36-46.',                         99.99,  69.99),
('Botas Chelsea Cuero Marrón',       'Zara',       'botas-chelsea-cuero-marron',       'Bota Chelsea de cuero genuino con elásticos laterales, puntera redondeada y suela de goma vulcanizada. Forro interior de tela suave.',                          89.99,  58.99),
('Sandalias Planas Verano',          'Mango',      'sandalias-planas-verano',          'Sandalia plana con tiras de cuero sintético trenzado, suela de corcho y plantilla acolchada. Cierre con hebilla ajustable.',                                    34.99,  21.99),
('Zapatos Oxford Hombre',            'Clarks',     'zapatos-oxford-hombre',            'Zapato Oxford de piel con cordones, puntera lisa y suela de cuero. Forro interior acolchado. Ideal para ocasiones formales.',                                  119.99,  83.99);

-- ── Productos — Accesorios (4) ────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Bolso Tote Canvas Mujer',          'Mango',      'bolso-tote-canvas-mujer',          'Bolso tote de lona resistente con asas de cuero, cierre de cremallera y bolsillo interior con cremallera. Capacidad 20L.',                                      49.99,  32.99),
('Cinturón Cuero Negro',             'Hugo Boss',  'cinturon-cuero-negro',             'Cinturón de cuero genuino con hebilla metálica plateada. Ancho 3.5 cm, disponible en tallas 80 a 110 cm. Acabado liso clásico.',                                39.99,  24.99),
('Gafas de Sol Polarizadas',         'Ray-Ban',    'gafas-sol-polarizadas',            'Gafas de sol con lentes polarizadas UV400, montura de acetato ligero y patillas reforzadas. Incluye funda rígida y paño de limpieza.',                         129.99,  90.99),
('Mochila Urbana 20L',               'Eastpak',    'mochila-urbana-20l',               'Mochila urbana de 20 litros con compartimento para portátil de 15.6", bolsillos laterales para botella y espalda acolchada ergonómica.',                        69.99,  48.99);

-- ── Productos — Perfumería (4) ────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Perfume Homme Intense 100ml',      'Dior',         'perfume-homme-intense-100ml',      'Eau de Parfum masculino con notas de bergamota y lavanda, corazón de cuero y fondo de madera de cedro. Duración hasta 8 horas.',                               89.99,  44.99),
('Colonia Fresca Citrus 75ml',       'Issey Miyake', 'colonia-fresca-citrus-75ml',       'Eau de Cologne unisex con notas cítricas de limón siciliano y pomelo, fondo de almizcle blanco. Fresca y ligera para el día a día.',                          59.99,  35.99),
('Crema Hidratante Facial SPF30',    'Neutrogena',   'crema-hidratante-facial-spf30',    'Crema hidratante de día con FPS 30, textura ligera no grasa, con ácido hialurónico y vitamina C. Para todo tipo de piel. 50ml.',                              24.99,  14.99),
('Set Regalo Perfume y Body Lotion', 'Lancôme',      'set-regalo-perfume-body-lotion',   'Set de regalo con Eau de Parfum 50ml y body lotion 200ml con la misma fragancia floral. Presentado en caja elegante de regalo.',                              79.99,  47.99);

-- ── Productos — Hogar (4) ─────────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Juego Sábanas Algodón 200 Hilos',  'Zara Home',  'juego-sabanas-algodon-200-hilos',  'Juego de sábanas de algodón 100% de 200 hilos. Incluye sábana bajera ajustable, encimera y 2 fundas de almohada. Cama 150.',                                  49.99,  32.99),
('Lámpara de Mesa LED Táctil',       'Ikea',       'lampara-mesa-led-tactil',          'Lámpara de mesa con base metálica, pantalla de tela y control táctil de 3 intensidades. Bombilla LED E27 incluida. Cable USB-C.',                              39.99,  25.99),
('Sartén Antiadherente 28cm',        'Tefal',      'sarten-antiadherente-28cm',        'Sartén de aluminio forjado con recubrimiento antiadherente de 5 capas, apta para todo tipo de cocinas incluida inducción. Apta lavavajillas.',                 44.99,  29.99),
('Cojín Decorativo 45x45cm',        'H&M Home',   'cojin-decorativo-45x45cm',         'Cojín decorativo con funda de terciopelo suave en color verde musgo, relleno de fibra hueca siliconada. Funda extraíble y lavable.',                           19.99,  11.99);

-- ── Productos — Alimentación (4) ──────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Aceite de Oliva Virgen Extra 500ml','Carbonell',  'aceite-oliva-virgen-extra-500ml',  'Aceite de oliva virgen extra de primera prensada en frío, acidez inferior a 0,3°. Sabor afrutado con notas de almendra y hierba fresca.',                     12.99,   7.79),
('Café Molido Arábica 250g',         'Nespresso',  'cafe-molido-arabica-250g',         'Café 100% arábica de origen Colombia, tueste medio. Molido para cafetera de filtro y prensa francesa. Notas de chocolate y frutos rojos.',                     14.99,   8.99),
('Pack Conservas Gourmet',           'Ortiz',      'pack-conservas-gourmet',           'Pack de 6 conservas artesanales: mejillones en escabeche, berberechos al natural, sardinillas en aceite de oliva y anchoas del Cantábrico.',                   29.99,  17.99),
('Miel de Abeja Cruda 500g',         'Muria',      'miel-abeja-cruda-500g',            'Miel cruda sin filtrar ni pasteurizar, de flores silvestres de la sierra. Textura cremosa, color ámbar y sabor intenso. Envase de cristal.',                   11.99,   6.99);

-- ── Productos — Deporte (4) ───────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('Camiseta Técnica Running',         'Nike',        'camiseta-tecnica-running',         'Camiseta técnica de running con tejido Dri-FIT de secado rápido, costuras planas sin rozaduras y reflectivos para visibilidad nocturna.',                       34.99,  21.99),
('Mancuernas Ajustables 20kg',       'Bowflex',     'mancuernas-ajustables-20kg',       'Par de mancuernas ajustables de 2 a 20 kg con sistema de ajuste rápido por selector. Base de plástico incluida. Ideal para entrenamiento en casa.',           149.99, 104.99),
('Esterilla Yoga Antideslizante',    'Decathlon',   'esterilla-yoga-antideslizante',    'Esterilla de yoga de 6mm de grosor con superficie antideslizante en ambas caras, correa de transporte incluida. Dimensiones 183x61cm.',                       24.99,  14.99),
('Botella Térmica Acero 750ml',      'Hydro Flask', 'botella-termica-acero-750ml',      'Botella de acero inoxidable de doble pared al vacío. Mantiene bebidas frías 24h y calientes 12h. Boca ancha, tapa antigoteo y libre de BPA.',                 39.99,  24.99);

-- ── Productos — Juguetes (4) ──────────────────────────────
INSERT INTO products (name, brand, slug, description, base_price, cost_price) VALUES
('LEGO Classic Ladrillos Creativos', 'LEGO',          'lego-classic-ladrillos-creativos', 'Set LEGO Classic con 790 piezas en 33 colores diferentes. Incluye ruedas, ojos y otros elementos especiales para construcciones libres.',                     39.99,  27.99),
('Puzzle 1000 Piezas Paisaje',       'Ravensburger',  'puzzle-1000-piezas-paisaje',       'Puzzle de 1000 piezas con imagen de paisaje montañoso al atardecer. Piezas de cartón grueso con corte de precisión. Medidas terminadas: 68x48cm.',            19.99,  12.99),
('Muñeca Articulada con Accesorios', 'Nancy',         'muneca-articulada-con-accesorios', 'Muñeca articulada de 30cm con 5 puntos de articulación, incluye ropa intercambiable, zapatos y accesorios. A partir de 3 años.',                             24.99,  15.99),
('Juego de Mesa Familiar',           'Hasbro',        'juego-de-mesa-familiar',           'Juego de mesa para 2-6 jugadores con tablero, dados, cartas y fichas. Apto para toda la familia desde 8 años. Duración aproximada 45 minutos.',               29.99,  18.99);

-- ── Relación producto → categoría ────────────────────────

-- Fashion
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'camiseta-basica-algodon-blanca' AND c.slug = 'fashion';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'pantalon-vaquero-slim-fit' AND c.slug = 'fashion';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'vestido-floral-verano' AND c.slug = 'fashion';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'chaqueta-vaquera-clasica' AND c.slug = 'fashion';

-- Footwear
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'zapatillas-running-air-max' AND c.slug = 'footwear';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'botas-chelsea-cuero-marron' AND c.slug = 'footwear';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'sandalias-planas-verano' AND c.slug = 'footwear';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'zapatos-oxford-hombre' AND c.slug = 'footwear';

-- Accessories
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'bolso-tote-canvas-mujer' AND c.slug = 'accessories';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'cinturon-cuero-negro' AND c.slug = 'accessories';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'gafas-sol-polarizadas' AND c.slug = 'accessories';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'mochila-urbana-20l' AND c.slug = 'accessories';

-- Perfumery
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'perfume-homme-intense-100ml' AND c.slug = 'perfumery';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'colonia-fresca-citrus-75ml' AND c.slug = 'perfumery';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'crema-hidratante-facial-spf30' AND c.slug = 'perfumery';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'set-regalo-perfume-body-lotion' AND c.slug = 'perfumery';

-- Home
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'juego-sabanas-algodon-200-hilos' AND c.slug = 'home';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'lampara-mesa-led-tactil' AND c.slug = 'home';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'sarten-antiadherente-28cm' AND c.slug = 'home';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'cojin-decorativo-45x45cm' AND c.slug = 'home';

-- Food
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'aceite-oliva-virgen-extra-500ml' AND c.slug = 'food';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'cafe-molido-arabica-250g' AND c.slug = 'food';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'pack-conservas-gourmet' AND c.slug = 'food';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'miel-abeja-cruda-500g' AND c.slug = 'food';

-- Sports
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'camiseta-tecnica-running' AND c.slug = 'sports';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'mancuernas-ajustables-20kg' AND c.slug = 'sports';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'esterilla-yoga-antideslizante' AND c.slug = 'sports';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'botella-termica-acero-750ml' AND c.slug = 'sports';

-- Toys
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'lego-classic-ladrillos-creativos' AND c.slug = 'toys';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'puzzle-1000-piezas-paisaje' AND c.slug = 'toys';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'muneca-articulada-con-accesorios' AND c.slug = 'toys';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'juego-de-mesa-familiar' AND c.slug = 'toys';

-- ── Variantes por defecto ─────────────────────────────────
INSERT INTO variants (product_id, name, extra_price, stock)
SELECT p.id, 'Unidad', 0.00, FLOOR(5 + RAND() * 46)
FROM products p
WHERE p.slug IN (
    'camiseta-basica-algodon-blanca', 'pantalon-vaquero-slim-fit', 'vestido-floral-verano', 'chaqueta-vaquera-clasica',
    'zapatillas-running-air-max', 'botas-chelsea-cuero-marron', 'sandalias-planas-verano', 'zapatos-oxford-hombre',
    'bolso-tote-canvas-mujer', 'cinturon-cuero-negro', 'gafas-sol-polarizadas', 'mochila-urbana-20l',
    'perfume-homme-intense-100ml', 'colonia-fresca-citrus-75ml', 'crema-hidratante-facial-spf30', 'set-regalo-perfume-body-lotion',
    'juego-sabanas-algodon-200-hilos', 'lampara-mesa-led-tactil', 'sarten-antiadherente-28cm', 'cojin-decorativo-45x45cm',
    'aceite-oliva-virgen-extra-500ml', 'cafe-molido-arabica-250g', 'pack-conservas-gourmet', 'miel-abeja-cruda-500g',
    'camiseta-tecnica-running', 'mancuernas-ajustables-20kg', 'esterilla-yoga-antideslizante', 'botella-termica-acero-750ml',
    'lego-classic-ladrillos-creativos', 'puzzle-1000-piezas-paisaje', 'muneca-articulada-con-accesorios', 'juego-de-mesa-familiar'
);

-- ── Imágenes principales ──────────────────────────────────
INSERT INTO product_images (product_id, image_url, is_main)
SELECT p.id, CONCAT('/assets/img/products/', c.slug, '/', p.slug, '.webp'), 1
FROM products p
INNER JOIN product_categories pc ON pc.product_id = p.id
INNER JOIN categories c ON c.id = pc.category_id
WHERE p.slug IN (
    'camiseta-basica-algodon-blanca', 'pantalon-vaquero-slim-fit', 'vestido-floral-verano', 'chaqueta-vaquera-clasica',
    'zapatillas-running-air-max', 'botas-chelsea-cuero-marron', 'sandalias-planas-verano', 'zapatos-oxford-hombre',
    'bolso-tote-canvas-mujer', 'cinturon-cuero-negro', 'gafas-sol-polarizadas', 'mochila-urbana-20l',
    'perfume-homme-intense-100ml', 'colonia-fresca-citrus-75ml', 'crema-hidratante-facial-spf30', 'set-regalo-perfume-body-lotion',
    'juego-sabanas-algodon-200-hilos', 'lampara-mesa-led-tactil', 'sarten-antiadherente-28cm', 'cojin-decorativo-45x45cm',
    'aceite-oliva-virgen-extra-500ml', 'cafe-molido-arabica-250g', 'pack-conservas-gourmet', 'miel-abeja-cruda-500g',
    'camiseta-tecnica-running', 'mancuernas-ajustables-20kg', 'esterilla-yoga-antideslizante', 'botella-termica-acero-750ml',
    'lego-classic-ladrillos-creativos', 'puzzle-1000-piezas-paisaje', 'muneca-articulada-con-accesorios', 'juego-de-mesa-familiar'
);

-- ── Imágenes adicionales — 3 productos con galería ───────
-- Zapatillas Running Air Max: superior + suela
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/footwear/zapatillas-running-air-max-top.webp', 0
FROM products WHERE slug = 'zapatillas-running-air-max';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/footwear/zapatillas-running-air-max-sole.webp', 0
FROM products WHERE slug = 'zapatillas-running-air-max';

-- Bolso Tote Canvas Mujer: interior + detalle asa
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/accessories/bolso-tote-canvas-mujer-interior.webp', 0
FROM products WHERE slug = 'bolso-tote-canvas-mujer';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/accessories/bolso-tote-canvas-mujer-handle.webp', 0
FROM products WHERE slug = 'bolso-tote-canvas-mujer';

-- Mochila Urbana 20L: lateral + interior
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/accessories/mochila-urbana-20l-side.webp', 0
FROM products WHERE slug = 'mochila-urbana-20l';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/accessories/mochila-urbana-20l-interior.webp', 0
FROM products WHERE slug = 'mochila-urbana-20l';

SET FOREIGN_KEY_CHECKS = 1;
