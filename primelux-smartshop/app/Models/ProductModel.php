<?php
declare(strict_types=1);

/*
 * Consultas sobre la tabla products y sus relaciones.
 * Fase 4: filtros de precio (rango), stock, marca y orden.
 * Buscador: search() y countSearch() buscan en name, description y brand.
 */

class ProductModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Productos destacados para la home. */
    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id
            WHERE p.status = "active"
            ORDER BY p.created_at DESC
            LIMIT ?
        ');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Búsqueda de productos por término.
     * Busca en name, description y brand con LIKE.
     * Devuelve paginado igual que getByCategory.
     */
    public function search(
        string $query,
        int    $page    = 1,
        int    $perPage = 12
    ): array {
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
            ORDER BY
                CASE WHEN p.name LIKE ? THEN 0 ELSE 1 END,
                p.name ASC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$term, $term, $term, $term, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta total de resultados para la paginación del buscador.
     */
    public function countSearch(string $query): int
    {
        $term = '%' . $query . '%';

        $stmt = $this->db->prepare('
            SELECT COUNT(DISTINCT p.id)
            FROM products p
            WHERE p.status = "active"
              AND (p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)
        ');
        $stmt->execute([$term, $term, $term]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Marcas disponibles en una categoría — para los checkboxes del filtro.
     */
    public function getBrandsByCategory(int $categoryId): array
    {
        $stmt = $this->db->prepare('
            SELECT DISTINCT p.brand
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            WHERE pc.category_id = ?
              AND p.status = "active"
              AND p.brand IS NOT NULL
              AND p.brand != ""
            ORDER BY p.brand ASC
        ');
        $stmt->execute([$categoryId]);
        return array_column($stmt->fetchAll(), 'brand');
    }

    /**
     * Rango de precios reales de una categoría para el slider.
     */
    public function getPriceRange(int $categoryId): array
    {
        $stmt = $this->db->prepare('
            SELECT MIN(p.base_price) AS min_price,
                   MAX(p.base_price) AS max_price
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

    /**
     * Productos de una categoría con todos los filtros activos.
     *
     * @param string[] $brands  Array de marcas seleccionadas (vacío = todas)
     */
    public function getByCategory(
        int    $categoryId,
        string $sort     = 'newest',
        int    $page     = 1,
        int    $perPage  = 12,
        float  $minPrice = 0,
        float  $maxPrice = 0,
        bool   $inStock  = false,
        array  $brands   = []
    ): array {
        $orderBy = match ($sort) {
            'price_asc'  => 'p.base_price ASC',
            'price_desc' => 'p.base_price DESC',
            default      => 'p.created_at DESC',
        };

        $offset = ($page - 1) * $perPage;
        [$where, $bindings] = $this->buildFilters(
            $categoryId, $minPrice, $maxPrice, $inStock, $brands
        );

        $stmt = $this->db->prepare("
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id
            WHERE {$where}
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([...$bindings, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    /** Cuenta con los mismos filtros activos para la paginación. */
    public function countByCategory(
        int   $categoryId,
        float $minPrice = 0,
        float $maxPrice = 0,
        bool  $inStock  = false,
        array $brands   = []
    ): int {
        [$where, $bindings] = $this->buildFilters(
            $categoryId, $minPrice, $maxPrice, $inStock, $brands
        );

        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT p.id)
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN variants v ON v.product_id = p.id
            WHERE {$where}
        ");
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn();
    }

    /** Producto por slug con imagen, categoría y stock. */
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
            LEFT JOIN variants v ON v.product_id = p.id
            WHERE p.slug = ? AND p.status = "active"
            LIMIT 1
        ');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    /** Variante por defecto del producto. */
    public function getDefaultVariant(int $productId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM variants WHERE product_id = ? ORDER BY id ASC LIMIT 1
        ');
        $stmt->execute([$productId]);
        return $stmt->fetch();
    }

    /** Productos relacionados de la misma categoría. */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare('
            SELECT p.*, pi.image_url, COALESCE(v.stock, 0) AS stock
            FROM products p
            INNER JOIN product_categories pc ON pc.product_id = p.id
            LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_main = 1
            LEFT JOIN variants v ON v.product_id = p.id
            WHERE pc.category_id = ? AND p.id != ? AND p.status = "active"
            ORDER BY RAND()
            LIMIT ?
        ');
        $stmt->execute([$categoryId, $productId, $limit]);
        return $stmt->fetchAll();
    }

    // ─── Helper privado ──────────────────────────────────────────────────────

    private function buildFilters(
        int   $categoryId,
        float $minPrice,
        float $maxPrice,
        bool  $inStock,
        array $brands = []
    ): array {
        $conditions = ["pc.category_id = ?", "p.status = 'active'"];
        $bindings   = [$categoryId];

        if ($minPrice > 0) {
            $conditions[] = 'p.base_price >= ?';
            $bindings[]   = $minPrice;
        }
        if ($maxPrice > 0) {
            $conditions[] = 'p.base_price <= ?';
            $bindings[]   = $maxPrice;
        }
        if ($inStock) {
            $conditions[] = 'COALESCE(v.stock, 0) > 0';
        }
        if (!empty($brands)) {
            $placeholders = implode(',', array_fill(0, count($brands), '?'));
            $conditions[] = "p.brand IN ({$placeholders})";
            $bindings     = array_merge($bindings, $brands);
        }

        return [implode(' AND ', $conditions), $bindings];
    }
}
