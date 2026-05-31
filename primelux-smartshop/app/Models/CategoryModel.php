<?php
declare(strict_types=1);

/*
 * Consultas sobre la tabla categories.
 * Sin lógica de negocio — solo acceso a datos.
 * Fase 7: añadidos findById(), create(), update(), delete() para el admin.
 */

class CategoryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /** Categorías destacadas para la home — solo las marcadas con featured = true. */
    public function getFeatured(): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM categories
            WHERE status = "active" AND featured = 1 AND parent_id IS NULL
            ORDER BY name ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
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

    /** Todas las categorías para el admin (incluye inactivas). */
    public function getAllAdmin(): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM categories
            WHERE parent_id IS NULL
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

    /** Categoría por ID. */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM categories WHERE id = ? LIMIT 1
        ');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Crea una nueva categoría. Genera el slug a partir del nombre. */
    public function create(array $data): int
    {
        $slug   = $this->generateSlug($data['name']);
        $status = in_array($data['status'] ?? '', ['active', 'inactive']) ? $data['status'] : 'active';

        $stmt = $this->db->prepare('
            INSERT INTO categories (name, slug, description, status, featured)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stmt->execute([$data['name'], $slug, $data['description'] ?? '', $status, $data['featured'] ?? 0]);
        return (int) $this->db->lastInsertId();
    }

    /** Actualiza nombre, slug, status y featured de una categoría. */
    public function update(int $id, array $data): bool
    {
        $slug   = $this->generateSlug($data['name'], $id);
        $status = in_array($data['status'] ?? '', ['active', 'inactive']) ? $data['status'] : 'active';

        $stmt = $this->db->prepare('
            UPDATE categories
            SET name = ?, slug = ?, description = ?, status = ?, featured = ?
            WHERE id = ?
        ');
        return $stmt->execute([$data['name'], $slug, $data['description'] ?? '', $status, $data['featured'] ?? 0, $id]);
    }

    // ID de la categoría de sistema "Sin categoría" — nunca se puede eliminar
    private const UNCATEGORIZED_ID = 19;

    /**
     * Elimina una categoría por ID.
     * Si tiene productos asociados, los reasigna a "Sin categoría" (id=19) antes de borrar.
     * La categoría "Sin categoría" está protegida y nunca se puede eliminar.
     */
    public function delete(int $id): array
    {
        // Protección — la categoría "Sin categoría" no se puede eliminar
        if ($id === self::UNCATEGORIZED_ID) {
            return ['deleted' => false, 'protected' => true, 'reassigned' => 0];
        }

        // Cuenta los productos asociados
        $stmt = $this->db->prepare('
            SELECT COUNT(*) FROM product_categories WHERE category_id = ?
        ');
        $stmt->execute([$id]);
        $productCount = (int) $stmt->fetchColumn();

        // Si tiene productos, los reasigna a "Sin categoría"
        if ($productCount > 0) {
            $this->db->prepare('
                UPDATE product_categories SET category_id = ? WHERE category_id = ?
            ')->execute([self::UNCATEGORIZED_ID, $id]);
        }

        // Elimina la categoría
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);

        return ['deleted' => true, 'protected' => false, 'reassigned' => $productCount];
    }

    /**
     * Genera un slug único a partir de un nombre.
     * Si se pasa $excludeId, excluye esa categoría del check de unicidad
     * para evitar que editar sin cambiar el nombre genere slug-1, slug-2, etc.
     */
    /**
     * Comprueba si ya existe una categoría con ese nombre.
     * $excludeId permite excluir la propia categoría al editar.
     */
    public function nameExists(string $name, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE name = ? AND id != ?');
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE name = ?');
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

        $original = $slug;
        $counter  = 1;
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ?');
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM categories WHERE slug = ?');
            $stmt->execute([$slug]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }
}
