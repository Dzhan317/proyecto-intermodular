-- ============================================================
-- Datos iniciales — Tecnología (24 productos)
-- Categorías con slugs en inglés
-- Sin brand, cost_price ni featured — se añaden en 005, 006 y 007
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Categorías de tecnología ──────────────────────────────
-- featured no existe todavía — se añade en 007-add-featured-categories.sql
INSERT INTO categories (name, slug, description, parent_id) VALUES
('Informática', 'computing',   'Portátiles, ordenadores de sobremesa y más',         NULL),
('Electrónica', 'electronics', 'Smartphones, tablets y dispositivos electrónicos',   NULL),
('Componentes', 'components',  'RAM, SSD, GPU y componentes para PC',                NULL),
('Periféricos', 'peripherals', 'Ratones, teclados, monitores y auriculares',         NULL),
('Software',    'software',    'Licencias de sistemas operativos y aplicaciones',    NULL),
('Redes',       'networking',  'Routers, switches y equipos de conectividad',        NULL);

-- ── Productos ─────────────────────────────────────────────
-- brand y cost_price no existen todavía — se añaden en 005 y 006
INSERT INTO products (name, slug, description, base_price) VALUES

-- Informática
('Laptop HP Pavilion 15',        'laptop-hp-pavilion-15',        'Portátil HP Pavilion 15 con procesador Intel Core i5-1235U, 16 GB RAM, SSD 512 GB y pantalla Full HD de 15.6". Ideal para trabajo y estudio.',                                    699.99),
('MacBook Air M2 13"',           'macbook-air-m2-13',            'MacBook Air con chip Apple M2, 8 GB de memoria unificada, SSD de 256 GB y pantalla Liquid Retina de 13.6". Ultrafino y sin ventiladores.',                                          1299.99),
('PC Gamer Ryzen 5',             'pc-gamer-ryzen-5',             'Ordenador gaming con AMD Ryzen 5 7600X, 16 GB DDR5, SSD NVMe 1 TB y chasis ATX con iluminación RGB. Listo para jugar al máximo rendimiento.',                                       849.99),
('Chromebook Lenovo Flex 5',     'chromebook-lenovo-flex-5',     'Chromebook táctil 2 en 1 con AMD Ryzen 3, 4 GB RAM, 128 GB eMMC y pantalla FHD de 13.3". Ligero, rápido y perfecto para el día a día.',                                             349.99),

-- Electrónica
('iPhone 15 Pro 256 GB',         'iphone-15-pro-256gb',          'iPhone 15 Pro con chip A17 Pro, cámara triple de 48 MP, pantalla Super Retina XDR de 6.1" y carcasa de titanio de grado aeroespacial.',                                            1199.99),
('Samsung Galaxy S24',           'samsung-galaxy-s24',           'Samsung Galaxy S24 con Exynos 2400, pantalla Dynamic AMOLED 2X de 6.2", cámara de 50 MP y batería de 4000 mAh con carga rápida.',                                                    999.99),
('iPad Air M2 11"',              'ipad-air-m2-11',               'iPad Air con chip M2, pantalla Liquid Retina de 11", compatible con Apple Pencil y Smart Keyboard Folio. Potencia profesional en formato portable.',                                   799.99),
('Apple Watch Series 9 GPS',     'apple-watch-series-9-gps',     'Apple Watch Series 9 con chip S9, pantalla Retina Always-On, detección de caídas y accidente de tráfico. Resistente al agua hasta 50 metros.',                                       449.99),

-- Componentes
('RAM Kingston Fury 16 GB DDR4', 'ram-kingston-fury-16gb-ddr4',  'Módulo de memoria Kingston Fury Beast 16 GB DDR4 3200 MHz CL16. Compatible con Intel y AMD. Ideal para gaming y multitarea.',                                                         49.99),
('SSD Samsung 970 EVO 1 TB',     'ssd-samsung-970-evo-1tb',      'Unidad SSD NVMe M.2 Samsung 970 EVO con velocidades de lectura hasta 3.500 MB/s. Interfaz PCIe Gen 3.0 x4. Garantía de 5 años.',                                                     89.99),
('NVIDIA RTX 4060 Ti 8 GB',      'nvidia-rtx-4060-ti-8gb',       'Tarjeta gráfica NVIDIA GeForce RTX 4060 Ti con 8 GB GDDR6, arquitectura Ada Lovelace y compatibilidad con DLSS 3. Ideal para gaming en 1080p y 1440p.',                              399.99),
('Intel Core i5-13400',          'intel-core-i5-13400',          'Procesador Intel Core i5-13400 de 10 núcleos (6P+4E), hasta 4.6 GHz en Turbo Boost, socket LGA1700. Compatibilidad con placas base Z690/Z790/B660/B760.',                            199.99),

-- Periféricos
('Ratón Logitech MX Master 3S',  'raton-logitech-mx-master-3s',  'Ratón inalámbrico Logitech MX Master 3S con sensor 8K DPI, rueda de desplazamiento electromagnética y hasta 70 días de batería. Compatible con Windows y macOS.',                    99.99),
('Teclado Keychron K2 V2',       'teclado-keychron-k2-v2',       'Teclado mecánico compacto 75% Keychron K2 V2 con switches Gateron G Pro, retroiluminación RGB y compatibilidad con Windows, macOS y Android.',                                       109.99),
('Monitor LG 27UL850 4K',        'monitor-lg-27ul850-4k',        'Monitor LG 27" 4K UHD IPS con puerto USB-C 60W, AMD FreeSync y cobertura de color 99% sRGB. Ideal para diseño gráfico, edición y productividad.',                                    449.99),
('Auriculares Sony WH-1000XM5',  'auriculares-sony-wh1000xm5',   'Auriculares over-ear con cancelación de ruido líder del sector, 30 horas de batería, micrófono integrado de alta definición y carga rápida vía USB-C.',                             329.99),

-- Software
('Windows 11 Pro — Licencia',    'windows-11-pro-licencia',      'Licencia digital Windows 11 Pro para 1 PC. Incluye actualizaciones de seguridad, BitLocker, escritorio remoto y acceso a Microsoft Store.',                                           199.99),
('Microsoft 365 Personal 1 año', 'microsoft-365-personal-1ano',  'Suscripción Microsoft 365 Personal por 1 año. Incluye Word, Excel, PowerPoint, Outlook, Teams y 1 TB de almacenamiento en OneDrive.',                                                  69.99),
('Adobe Creative Cloud 1 año',   'adobe-creative-cloud-1ano',    'Plan completo Adobe Creative Cloud con acceso a más de 20 aplicaciones: Photoshop, Illustrator, Premiere Pro, After Effects y mucho más.',                                           599.99),
('Kaspersky Plus 3 PCs 1 año',   'kaspersky-plus-3pc-1ano',      'Antivirus Kaspersky Plus para 3 dispositivos durante 1 año. Protección en tiempo real, VPN incluida, control parental y gestor de contraseñas.',                                      49.99),

-- Redes
('Router ASUS WiFi 6 AX6000',    'router-asus-wifi6-ax6000',     'Router ASUS RT-AX88U con WiFi 6 (802.11ax), velocidades de hasta 6000 Mbps, 8 puertos LAN Gigabit, USB 3.0 y compatibilidad con AiMesh.',                                           299.99),
('Switch TP-Link 8P Gigabit',    'switch-tp-link-8p-gigabit',    'Switch no administrable TP-Link TL-SG108 con 8 puertos Gigabit 10/100/1000 Mbps. Diseño metálico, silencioso y sin necesidad de configuración.',                                      29.99),
('Cable Ethernet Cat6 10 m',     'cable-ethernet-cat6-10m',      'Cable de red RJ45 categoría 6 de 10 metros. Velocidad hasta 1 Gbps, transmisión 250 MHz, trenzado UTP para minimizar interferencias.',                                               12.99),
('Access Point Ubiquiti U6 Lite','access-point-ubiquiti-u6-lite','Access Point UniFi U6 Lite con WiFi 6, cobertura hasta 300 m², soporte para más de 300 dispositivos y alimentación PoE. Gestión vía aplicación UniFi.',                            129.99);

-- ── Relación producto → categoría ────────────────────────
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'laptop-hp-pavilion-15'       AND c.slug = 'computing';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'macbook-air-m2-13'           AND c.slug = 'computing';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'pc-gamer-ryzen-5'            AND c.slug = 'computing';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'chromebook-lenovo-flex-5'    AND c.slug = 'computing';

INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'iphone-15-pro-256gb'         AND c.slug = 'electronics';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'samsung-galaxy-s24'          AND c.slug = 'electronics';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'ipad-air-m2-11'              AND c.slug = 'electronics';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'apple-watch-series-9-gps'    AND c.slug = 'electronics';

INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'ram-kingston-fury-16gb-ddr4' AND c.slug = 'components';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'ssd-samsung-970-evo-1tb'     AND c.slug = 'components';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'nvidia-rtx-4060-ti-8gb'      AND c.slug = 'components';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'intel-core-i5-13400'         AND c.slug = 'components';

INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'raton-logitech-mx-master-3s' AND c.slug = 'peripherals';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'teclado-keychron-k2-v2'      AND c.slug = 'peripherals';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'monitor-lg-27ul850-4k'       AND c.slug = 'peripherals';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'auriculares-sony-wh1000xm5'  AND c.slug = 'peripherals';

INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'windows-11-pro-licencia'     AND c.slug = 'software';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'microsoft-365-personal-1ano' AND c.slug = 'software';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'adobe-creative-cloud-1ano'   AND c.slug = 'software';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'kaspersky-plus-3pc-1ano'     AND c.slug = 'software';

INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'router-asus-wifi6-ax6000'    AND c.slug = 'networking';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'switch-tp-link-8p-gigabit'   AND c.slug = 'networking';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'cable-ethernet-cat6-10m'     AND c.slug = 'networking';
INSERT INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p, categories c WHERE p.slug = 'access-point-ubiquiti-u6-lite' AND c.slug = 'networking';

-- ── Variantes por defecto ─────────────────────────────────
INSERT INTO variants (product_id, name, extra_price, stock)
SELECT id, 'Unidad', 0.00, FLOOR(10 + RAND() * 40)
FROM products;

-- ── Imágenes principales ──────────────────────────────────
INSERT INTO product_images (product_id, image_url, is_main)
SELECT p.id, CONCAT('/assets/img/products/', c.slug, '/', p.slug, '.webp'), 1
FROM products p
INNER JOIN product_categories pc ON pc.product_id = p.id
INNER JOIN categories c ON c.id = pc.category_id;

-- ── Imágenes adicionales — 3 productos con galería ───────

-- Laptop HP Pavilion 15: cerrado + detalle teclado
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/computing/laptop-hp-pavilion-15-closed.webp', 0
FROM products WHERE slug = 'laptop-hp-pavilion-15';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/computing/laptop-hp-pavilion-15-keyboard.webp', 0
FROM products WHERE slug = 'laptop-hp-pavilion-15';

-- iPhone 15 Pro: trasero + lateral
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/electronics/iphone-15-pro-256gb-back.webp', 0
FROM products WHERE slug = 'iphone-15-pro-256gb';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/electronics/iphone-15-pro-256gb-side.webp', 0
FROM products WHERE slug = 'iphone-15-pro-256gb';

-- Samsung Galaxy S24: trasero + detalle cámara
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/electronics/samsung-galaxy-s24-back.webp', 0
FROM products WHERE slug = 'samsung-galaxy-s24';
INSERT INTO product_images (product_id, image_url, is_main)
SELECT id, '/assets/img/products/electronics/samsung-galaxy-s24-camera.webp', 0
FROM products WHERE slug = 'samsung-galaxy-s24';

SET FOREIGN_KEY_CHECKS = 1;
