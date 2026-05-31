<?php
declare(strict_types=1);

/*
 * Todas las consultas relacionadas con la tabla users.
 * Ninguna lógica de negocio: solo lectura y escritura en BD.
 * Fase 9: añadidos getAllAdmin(), countAll() para el panel de administración.
 */

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (name, last_name, email, password, role, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['name'],
            $data['last_name'],
            $data['email'],
            $data['password'],
            $data['role']   ?? 'customer',
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?
        ');
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function updateProfile(int $userId, string $name, string $lastName): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users SET name = ?, last_name = ?, updated_at = NOW() WHERE id = ?
        ');
        return $stmt->execute([$name, $lastName, $userId]);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Listado paginado de usuarios para el admin.
     * Permite búsqueda por nombre, apellido o email.
     */
    public function getAllAdmin(int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;

        if ($search !== '') {
            $term = '%' . $search . '%';
            $stmt = $this->db->prepare('
                SELECT id, name, last_name, email, role, status, created_at
                FROM users
                WHERE name LIKE ? OR last_name LIKE ? OR email LIKE ?
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ');
            $stmt->execute([$term, $term, $term, $perPage, $offset]);
        } else {
            $stmt = $this->db->prepare('
                SELECT id, name, last_name, email, role, status, created_at
                FROM users
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ');
            $stmt->execute([$perPage, $offset]);
        }

        return $stmt->fetchAll();
    }

    /** Cuenta total de usuarios para la paginación del admin. */
    public function countAll(string $search = ''): int
    {
        if ($search !== '') {
            $term = '%' . $search . '%';
            $stmt = $this->db->prepare('
                SELECT COUNT(*) FROM users
                WHERE name LIKE ? OR last_name LIKE ? OR email LIKE ?
            ');
            $stmt->execute([$term, $term, $term]);
        } else {
            $stmt = $this->db->query('SELECT COUNT(*) FROM users');
        }

        return (int) $stmt->fetchColumn();
    }
}
