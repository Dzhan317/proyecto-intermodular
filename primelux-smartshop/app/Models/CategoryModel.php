<?php
declare(strict_types=1);

/*
 * Consultas sobre la tabla categories.
 * Sin lógica de negocio — solo acceso a datos.
 */

class CategoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Todas las categorías raíz activas ordenadas por nombre. */
    public function getAll(): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM categories
            WHERE status = "active" AND parent_id IS NULL
            ORDER BY name ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Categoría por slug. */
    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM categories
            WHERE slug = ? AND status = "active"
            LIMIT 1
        ');
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
}
