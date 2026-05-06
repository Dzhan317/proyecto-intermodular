<?php
declare(strict_types=1);

/*
 * Todas las consultas relacionadas con la tabla users.
 * Ninguna lógica de negocio: solo lectura y escritura en BD.
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
}
