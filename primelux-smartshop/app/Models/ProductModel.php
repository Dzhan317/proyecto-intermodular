<?php
declare(strict_types=1);

/*
 * Consultas sobre la tabla products y sus relaciones.
 * Fase 4: filtros de precio (rango), stock, marca y orden.
 * Buscador: search() y countSearch() buscan en name, description y brand.
 * Fase 7: getAllAdmin(), countAll(), findById(), create(), update(), delete().
 * Motor de recomendaciones (mejoras 1-5):
 *   - Mejora 1: brand como criterio adicional en getSmartRelated()
 *   - Mejora 2: tipo 'cart' registrado desde CartController
 *   - Mejora 3: getRecommended() usa user_interests para home personalizada
 *   - Mejora 4: puntuación ponderada (view=1, cart=3, order=5)
 *   - Mejora 5: anti-repetición — no recomienda productos ya comprados
 */

class ProductModel
{
    private PDO $db;

    // Pesos de las interacciones para el algoritmo de relevancia
    private const WEIGHT_VIEW  = 1;
    private const WEIGHT_CART  = 3;
    private const WEIGHT_ORDER = 5;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE p.status = "active"
            ORDER BY p.created_at DESC
            LIMIT ?
        ');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $term   = '%' . $query . '%';
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock,
                   c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v        ON v.product_id  = p.id
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c      ON c.id = pc.category_id
            WHERE p.status = "active"
              AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)
            ORDER BY CASE WHEN p.name LIKE ? THEN 0 ELSE 1 END, p.name ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$term, $term, $term, $term, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function countSearch(string $query): int
    {
        $term = '%' . $query . '%';
        $stmt = $this->db->prepare('
            SELECT COUNT(DISTINCT p.id) FROM products p
            WHERE p.status = "active"
              AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)
        ');
        $stmt->execute([$term, $term, $term]);
        return (int) $stmt->fetchColumn();
    }

    public function getBrandsByCategory(int $categoryId): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT p.brand FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            WHERE pc.category_id = ? AND p.status = "active"
              AND p.brand IS NOT NULL AND p.brand != ""
            ORDER BY p.brand ASC
        ');
        $stmt->execute([$categoryId]);
        return array_column($stmt->fetchAll(), 'brand');
    }

    public function getPriceRange(int $categoryId): array
    {
        $stmt = $this->db->prepare('
            SELECT MIN(p.base_price) AS min_price, MAX(p.base_price) AS max_price
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            WHERE pc.category_id = ? AND p.status = "active"
        ');
        $stmt->execute([$categoryId]);
        $row = $stmt->fetch();
        return [
            'min' => (float) ($row['min_price'] ?? 0),
            'max' => (float) ($row['max_price'] ?? 9999),
        ];
    }

    public function getByCategory(
        int $categoryId, string $sort = 'newest', int $page = 1, int $perPage = 12,
        float $minPrice = 0, float $maxPrice = 0, bool $inStock = false, array $brands = []
    ): array {
        $orderBy = match ($sort) {
            'price_asc'  => 'p.base_price ASC',
            'price_desc' => 'p.base_price DESC',
            default      => 'p.created_at DESC',
        };
        $offset = ($page - 1) * $perPage;
        [$where, $bindings] = $this->buildFilters($categoryId, $minPrice, $maxPrice, $inStock, $brands);
        $stmt = $this->db->prepare("
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$bindings, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function countByCategory(int $categoryId, float $minPrice = 0, float $maxPrice = 0, bool $inStock = false, array $brands = []): int
    {
        [$where, $bindings] = $this->buildFilters($categoryId, $minPrice, $maxPrice, $inStock, $brands);
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT p.id) FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE {$where}
        ");
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }

    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url,
                   c.name AS category_name, c.slug AS category_slug,
                   COALESCE(v.stock, 0) AS stock
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE p.slug = ? AND p.status = "active" LIMIT 1
        ');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function getDefaultVariant(int $productId): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM variants WHERE product_id = ? ORDER BY id ASC LIMIT 1');
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE pc.category_id = ? AND p.id != ? AND p.status = "active"
            ORDER BY RAND() LIMIT ?
        ');
        $stmt->execute([$categoryId, $productId, $limit]);
        return $stmt->fetchAll();
    }

    // ─── Galería de imágenes ──────────────────────────────────────────────────

    public function getImages(int $productId): array
    {
        $stmt = $this->db->prepare('
            SELECT id, image_url, is_main FROM product_images
            WHERE product_id = ?
            ORDER BY is_main DESC, id ASC
        ');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    // ─── Motor de recomendaciones ─────────────────────────────────────────────

    /**
     * Registra una interacción del usuario con un producto.
     * Mejora 2: soporta tipo 'cart' además de 'view'.
     * Solo registra 1 view por producto por día para evitar spam.
     */
    public function recordInteraction(int $userId, int $productId, string $type): void
    {
        try {
            if ($type === 'view') {
                $exists = $this->db->prepare('
                    SELECT COUNT(*) FROM interactions
                    WHERE user_id = ? AND product_id = ? AND type = "view"
                      AND created_at >= DATE(NOW())
                ');
                $exists->execute([$userId, $productId]);
                if ((int) $exists->fetchColumn() > 0) return;
            }

            $this->db->prepare('
                INSERT INTO interactions (user_id, product_id, type) VALUES (?, ?, ?)
            ')->execute([$userId, $productId, $type]);

            if ($type === 'view') {
                $this->db->prepare('
                    INSERT INTO view_history (user_id, product_id) VALUES (?, ?)
                ')->execute([$userId, $productId]);

                // Conserva solo los 10 más recientes
                $this->db->prepare('
                    DELETE FROM view_history
                    WHERE user_id = ?
                      AND id NOT IN (
                          SELECT id FROM (
                              SELECT id FROM view_history
                              WHERE user_id = ?
                              ORDER BY created_at DESC LIMIT 10
                          ) AS recent
                      )
                ')->execute([$userId, $userId]);
            }
        } catch (\Throwable $e) {
            error_log('[RecommendationEngine] recordInteraction: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza el índice de interés del usuario por categoría.
     * Mejora 4: el peso depende del tipo de interacción.
     */
    public function updateUserInterest(int $userId, int $categoryId, string $type = 'view'): void
    {
        try {
            $score = match ($type) {
                'cart'  => self::WEIGHT_CART,
                'order' => self::WEIGHT_ORDER,
                default => self::WEIGHT_VIEW,
            };

            $this->db->prepare('
                INSERT INTO user_interests (user_id, category_id, interest_score)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    interest_score = interest_score + VALUES(interest_score),
                    last_interaction = NOW()
            ')->execute([$userId, $categoryId, $score]);
        } catch (\Throwable $e) {
            error_log('[RecommendationEngine] updateUserInterest: ' . $e->getMessage());
        }
    }

    /**
     * Productos relacionados inteligentes.
     * Mejora 1: incluye brand como criterio adicional.
     * Mejora 4: pondera por tipo de interacción (view=1, cart=3, order=5).
     * Mejora 5: excluye productos ya comprados por el usuario.
     *
     * Algoritmo:
     * 1. Encuentra usuarios que interactuaron con el producto actual
     * 2. Mira qué otros productos vieron/compraron esos usuarios
     * 3. Pondera por tipo de interacción
     * 4. Añade bonus si la marca coincide con el producto actual
     * 5. Excluye productos ya comprados por el usuario actual
     * 6. Fallback a productos de la misma categoría si no hay datos suficientes
     */
    public function getSmartRelated(int $productId, int $categoryId, int $limit = 4, int $userId = 0): array
    {
        // Obtiene la marca del producto actual para el bonus de brand
        $brandStmt = $this->db->prepare('SELECT brand FROM products WHERE id = ?');
        $brandStmt->execute([$productId]);
        $currentBrand = $brandStmt->fetchColumn() ?: '';

        // Mejora 5: productos ya comprados por este usuario
        $purchasedIds = [];
        if ($userId > 0) {
            $purchasedStmt = $this->db->prepare('
                SELECT DISTINCT v.product_id
                FROM order_items oi
                INNER JOIN variants v ON v.id = oi.variant_id
                INNER JOIN orders o ON o.id = oi.order_id
                WHERE o.user_id = ? AND o.status IN ("paid","shipped","delivered")
            ');
            $purchasedStmt->execute([$userId]);
            $purchasedIds = $purchasedStmt->fetchAll(\PDO::FETCH_COLUMN);
        }

        // Mejora 4: score ponderado por tipo de interacción
        // Mejora 1: bonus de +2 si la marca coincide
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock,
                   SUM(
                       CASE i2.type
                           WHEN "view"  THEN ' . self::WEIGHT_VIEW . '
                           WHEN "cart"  THEN ' . self::WEIGHT_CART . '
                           ELSE         ' . self::WEIGHT_ORDER . '
                       END
                   ) + IF(p.brand = ? AND p.brand != "", 2, 0) AS relevance_score
            FROM interactions i1
            INNER JOIN interactions i2
                ON i2.user_id = i1.user_id AND i2.product_id != i1.product_id
            INNER JOIN products p ON p.id = i2.product_id AND p.status = "active"
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE i1.product_id = ? AND i2.product_id != ?
            GROUP BY p.id
            ORDER BY relevance_score DESC
            LIMIT ?
        ');
        $stmt->execute([$currentBrand, $productId, $productId, $limit * 2]);
        $smart = $stmt->fetchAll();

        // Aplica filtro anti-repetición (Mejora 5)
        if (!empty($purchasedIds)) {
            $smart = array_filter($smart, fn($p) => !in_array($p['id'], $purchasedIds));
        }

        $smart = array_slice(array_values($smart), 0, $limit);

        // Fallback si no hay suficientes datos
        if (count($smart) < $limit) {
            $existing = array_column($smart, 'id');
            $existing[] = $productId;
            if (!empty($purchasedIds)) {
                $existing = array_merge($existing, $purchasedIds);
            }
            $existing   = array_unique($existing);
            $placeholders = implode(',', array_fill(0, count($existing), '?'));

            $stmt = $this->db->prepare("
                SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
                FROM products p
                INNER JOIN product_categories pc ON pc.product_id = p.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
                LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
                WHERE pc.category_id = ?
                  AND p.id NOT IN ({$placeholders})
                  AND p.status = 'active'
                ORDER BY RAND()
                LIMIT ?
            ");
            $stmt->execute([$categoryId, ...$existing, $limit - count($smart)]);
            $smart = array_merge($smart, $stmt->fetchAll());
        }

        return $smart;
    }

    /**
     * Productos recomendados para la home.
     * Mejora 3: usa user_interests para personalizar según historial del usuario.
     * Mejora 5: excluye productos ya comprados.
     */
    public function getRecommended(int $userId, int $limit = 8): array
    {
        try {
            // Productos ya comprados — no se recomiendan
            $purchasedStmt = $this->db->prepare('
                SELECT DISTINCT v.product_id
                FROM order_items oi
                INNER JOIN variants v ON v.id = oi.variant_id
                INNER JOIN orders o ON o.id = oi.order_id
                WHERE o.user_id = ? AND o.status IN ("paid","shipped","delivered")
            ');
            $purchasedStmt->execute([$userId]);
            $purchasedIds = $purchasedStmt->fetchAll(\PDO::FETCH_COLUMN);

            // Productos ya vistos — se muestran con menor prioridad
            $viewedStmt = $this->db->prepare('
                SELECT product_id FROM view_history WHERE user_id = ?
            ');
            $viewedStmt->execute([$userId]);
            $viewedIds = $viewedStmt->fetchAll(\PDO::FETCH_COLUMN);

            // Excluye comprados + vistos de la selección principal
            $excludeIds = array_unique(array_merge($purchasedIds, $viewedIds));
            $excludeIds = !empty($excludeIds) ? $excludeIds : [0];
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

            // Selecciona productos de las categorías con más interés
            $stmt = $this->db->prepare("
                SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock,
                       ui.interest_score
                FROM user_interests ui
                INNER JOIN product_categories pc ON pc.category_id = ui.category_id
                INNER JOIN products p ON p.id = pc.product_id AND p.status = 'active'
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
                LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
                WHERE ui.user_id = ?
                  AND p.id NOT IN ({$placeholders})
                ORDER BY ui.interest_score DESC, RAND()
                LIMIT ?
            ");
            $stmt->execute([$userId, ...$excludeIds, $limit]);
            $recommended = $stmt->fetchAll();

            // Fallback si no hay suficientes datos personalizados
            if (count($recommended) < $limit) {
                return $this->getFeatured($limit);
            }

            return $recommended;

        } catch (\Throwable $e) {
            error_log('[RecommendationEngine] getRecommended: ' . $e->getMessage());
            return $this->getFeatured($limit);
        }
    }

    // ─── Métodos admin (Fase 7) ───────────────────────────────────────────────

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url,
                   c.id AS category_id, c.name AS category_name,
                   COALESCE(v.stock, 0) AS stock, v.id AS variant_id
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            WHERE p.id = ? LIMIT 1
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAllAdmin(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, c.name AS category_name, COALESCE(v.stock, 0) AS stock
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN categories c ON c.id = pc.category_id
            LEFT JOIN variants v ON v.product_id = p.id AND v.id = (SELECT MIN(id) FROM variants WHERE product_id = p.id)
            ORDER BY p.created_at DESC LIMIT ? OFFSET ?
        ');
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $slug = $this->generateSlug($data['name']);
            $stmt = $this->db->prepare('
                INSERT INTO products (name, slug, description, brand, base_price, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $data['name'], $slug,
                $data['description'] ?? '',
                $data['brand']       ?? '',
                $data['base_price'],
                $data['status']      ?? 'active',
            ]);
            $productId = (int) $this->db->lastInsertId();
            $this->db->prepare('INSERT INTO variants (product_id, name, extra_price, stock) VALUES (?, "Unidad", 0, ?)')
                ->execute([$productId, $data['stock'] ?? 0]);
            $this->db->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)')
                ->execute([$productId, $data['category_id']]);
            $this->db->commit();
            return $productId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare('
                UPDATE products
                SET name = ?, description = ?, brand = ?,
                    base_price = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ')->execute([
                $data['name'], $data['description'] ?? '',
                $data['brand'] ?? '', $data['base_price'],
                $data['status'] ?? 'active', $id,
            ]);

            $variantStmt = $this->db->prepare('SELECT id FROM variants WHERE product_id = ? ORDER BY id ASC LIMIT 1');
            $variantStmt->execute([$id]);
            $variantRow = $variantStmt->fetch();
            if ($variantRow) {
                $this->db->prepare('UPDATE variants SET stock = ? WHERE id = ?')
                    ->execute([$data['stock'] ?? 0, $variantRow['id']]);
            } else {
                // Si el producto no tiene variante, la crea para no perder el stock silenciosamente
                $this->db->prepare('INSERT INTO variants (product_id, name, extra_price, stock) VALUES (?, "Unidad", 0, ?)')
                    ->execute([$id, $data['stock'] ?? 0]);
            }

            $existing = $this->db->prepare('SELECT product_id FROM product_categories WHERE product_id = ? LIMIT 1');
            $existing->execute([$id]);
            if ($existing->fetch()) {
                $this->db->prepare('UPDATE product_categories SET category_id = ? WHERE product_id = ?')
                    ->execute([$data['category_id'], $id]);
            } else {
                $this->db->prepare('INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)')
                    ->execute([$id, $data['category_id']]);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('UPDATE products SET status = "inactive", updated_at = NOW() WHERE id = ?')
            ->execute([$id]);
    }

    // ─── Helpers privados ────────────────────────────────────────────────────

    private function buildFilters(int $categoryId, float $minPrice, float $maxPrice, bool $inStock, array $brands = []): array
    {
        $conditions = ["pc.category_id = ?", "p.status = 'active'"];
        $bindings   = [$categoryId];
        if ($minPrice > 0) { $conditions[] = 'p.base_price >= ?'; $bindings[] = $minPrice; }
        if ($maxPrice > 0) { $conditions[] = 'p.base_price <= ?'; $bindings[] = $maxPrice; }
        if ($inStock)      { $conditions[] = 'COALESCE(v.stock, 0) > 0'; }
        if (!empty($brands)) {
            $placeholders = implode(',', array_fill(0, count($brands), '?'));
            $conditions[] = "p.brand IN ({$placeholders})";
            $bindings     = array_merge($bindings, $brands);
        }
        return [implode(' AND ', $conditions), $bindings];
    }

    /**
     * Comprueba si ya existe un producto con ese nombre.
     * $excludeId permite excluir el propio producto al editar.
     */
    public function nameExists(string $name, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE name = ? AND id != ?');
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE name = ?');
            $stmt->execute([$name]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    private function generateSlug(string $name, int $excludeId = 0): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[áàäâ]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöô]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/[ñ]/u',    'n', $slug);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        $original = $slug; $counter = 1;
        while ($this->slugExists($slug, $excludeId)) { $slug = $original . '-' . $counter++; }
        return $slug;
    }

    private function slugExists(string $slug, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE slug = ? AND id != ?');
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE slug = ?');
            $stmt->execute([$slug]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
