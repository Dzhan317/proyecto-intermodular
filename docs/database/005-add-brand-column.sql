-- Añadir columna brand a products
-- Se añade de forma incremental tras la inserción inicial de datos (004)
-- Permite filtrar y buscar productos por marca en la tienda y en el admin

ALTER TABLE products ADD COLUMN brand VARCHAR(100) NULL AFTER name;

-- ── Actualizar brand para los 24 productos de tecnología ─

-- Informática
UPDATE products SET brand = 'HP'       WHERE slug = 'laptop-hp-pavilion-15';
UPDATE products SET brand = 'Apple'    WHERE slug = 'macbook-air-m2-13';
UPDATE products SET brand = 'AMD'      WHERE slug = 'pc-gamer-ryzen-5';
UPDATE products SET brand = 'Lenovo'   WHERE slug = 'chromebook-lenovo-flex-5';

-- Electrónica
UPDATE products SET brand = 'Apple'    WHERE slug = 'iphone-15-pro-256gb';
UPDATE products SET brand = 'Samsung'  WHERE slug = 'samsung-galaxy-s24';
UPDATE products SET brand = 'Apple'    WHERE slug = 'ipad-air-m2-11';
UPDATE products SET brand = 'Apple'    WHERE slug = 'apple-watch-series-9-gps';

-- Componentes
UPDATE products SET brand = 'Kingston' WHERE slug = 'ram-kingston-fury-16gb-ddr4';
UPDATE products SET brand = 'Samsung'  WHERE slug = 'ssd-samsung-970-evo-1tb';
UPDATE products SET brand = 'NVIDIA'   WHERE slug = 'nvidia-rtx-4060-ti-8gb';
UPDATE products SET brand = 'Intel'    WHERE slug = 'intel-core-i5-13400';

-- Periféricos
UPDATE products SET brand = 'Logitech' WHERE slug = 'raton-logitech-mx-master-3s';
UPDATE products SET brand = 'Keychron' WHERE slug = 'teclado-keychron-k2-v2';
UPDATE products SET brand = 'LG'       WHERE slug = 'monitor-lg-27ul850-4k';
UPDATE products SET brand = 'Sony'     WHERE slug = 'auriculares-sony-wh1000xm5';

-- Software
UPDATE products SET brand = 'Microsoft' WHERE slug = 'windows-11-pro-licencia';
UPDATE products SET brand = 'Microsoft' WHERE slug = 'microsoft-365-personal-1ano';
UPDATE products SET brand = 'Adobe'     WHERE slug = 'adobe-creative-cloud-1ano';
UPDATE products SET brand = 'Kaspersky' WHERE slug = 'kaspersky-plus-3pc-1ano';

-- Redes
UPDATE products SET brand = 'ASUS'     WHERE slug = 'router-asus-wifi6-ax6000';
UPDATE products SET brand = 'TP-Link'  WHERE slug = 'switch-tp-link-8p-gigabit';
UPDATE products SET brand = 'TP-Link'  WHERE slug = 'cable-ethernet-cat6-10m';
UPDATE products SET brand = 'Ubiquiti' WHERE slug = 'access-point-ubiquiti-u6-lite';
