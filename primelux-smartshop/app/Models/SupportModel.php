<?php
declare(strict_types=1);

/*
 * Gestiona el acceso a las tablas conversations y messages.
 * Usado por SupportController (usuario) y AdminController (admin).
 */

class SupportModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─── Conversaciones ───────────────────────────────────────────────────────

    /** Obtiene todas las conversaciones de un usuario con el último mensaje. */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT c.*,
                   (SELECT m.message
                    FROM messages m
                    WHERE m.conversation_id = c.id
                    ORDER BY m.created_at DESC
                    LIMIT 1) AS last_message,
                   (SELECT m.created_at
                    FROM messages m
                    WHERE m.conversation_id = c.id
                    ORDER BY m.created_at DESC
                    LIMIT 1) AS last_message_at
            FROM conversations c
            WHERE c.user_id = ?
            ORDER BY last_message_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Obtiene todas las conversaciones para el panel admin con datos del usuario. */
    public function getAll(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt   = $this->db->prepare('
            SELECT c.*,
                   u.name AS user_name, u.last_name AS user_last_name,
                   u.email AS user_email,
                   (SELECT m.message
                    FROM messages m
                    WHERE m.conversation_id = c.id
                    ORDER BY m.created_at DESC
                    LIMIT 1) AS last_message
            FROM conversations c
            INNER JOIN users u ON u.id = c.user_id
            ORDER BY c.created_at DESC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    /** Cuenta el total de conversaciones para la paginación. */
    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM conversations')->fetchColumn();
    }

    /** Obtiene una conversación por ID con sus mensajes. */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('
            SELECT c.*, u.name AS user_name, u.last_name AS user_last_name,
                   u.email AS user_email
            FROM conversations c
            INNER JOIN users u ON u.id = c.user_id
            WHERE c.id = ?
            LIMIT 1
        ');
        $stmt->execute([$id]);
        $conversation = $stmt->fetch();

        if (!$conversation) return false;

        $stmt = $this->db->prepare('
            SELECT m.*, u.name AS sender_name, u.role AS sender_role
            FROM messages m
            INNER JOIN users u ON u.id = m.user_id
            WHERE m.conversation_id = ?
            ORDER BY m.created_at ASC
        ');
        $stmt->execute([$id]);
        $conversation['messages'] = $stmt->fetchAll();

        return $conversation;
    }

    /** Crea una nueva conversación y devuelve su ID. */
    public function create(int $userId, string $subject): int
    {
        $this->db->prepare('
            INSERT INTO conversations (user_id, subject, status)
            VALUES (?, ?, "open")
        ')->execute([$userId, $subject]);

        return (int) $this->db->lastInsertId();
    }

    /** Actualiza el estado de una conversación (open/closed). */
    public function updateStatus(int $id, string $status): void
    {
        $this->db->prepare('
            UPDATE conversations SET status = ? WHERE id = ?
        ')->execute([$status, $id]);
    }

    // ─── Mensajes ─────────────────────────────────────────────────────────────

    /** Añade un mensaje a una conversación. */
    public function addMessage(int $conversationId, int $userId, string $message): void
    {
        $this->db->prepare('
            INSERT INTO messages (conversation_id, user_id, message)
            VALUES (?, ?, ?)
        ')->execute([$conversationId, $userId, $message]);
    }

    /**
     * Devuelve los mensajes de una conversación posteriores a un ID dado.
     * Usado por el polling para obtener solo los mensajes nuevos.
     */
    public function getMessagesSince(int $conversationId, int $lastId): array
    {
        $stmt = $this->db->prepare('
            SELECT m.*, u.name AS sender_name, u.role AS sender_role
            FROM messages m
            INNER JOIN users u ON u.id = m.user_id
            WHERE m.conversation_id = ? AND m.id > ?
            ORDER BY m.created_at ASC
        ');
        $stmt->execute([$conversationId, $lastId]);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta los mensajes no leídos para un usuario.
     * Solo cuenta mensajes enviados por otros usuarios (no los propios).
     */
    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*)
            FROM messages m
            INNER JOIN conversations c ON c.id = m.conversation_id
            WHERE c.user_id = ?
              AND m.user_id != ?
              AND m.is_read = 0
        ');
        $stmt->execute([$userId, $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Cuenta los mensajes no leídos para el admin.
     * Solo cuenta mensajes enviados por clientes.
     */
    public function getUnreadCountAdmin(): int
    {
        $stmt = $this->db->prepare('
            SELECT COUNT(*)
            FROM messages m
            INNER JOIN users u ON u.id = m.user_id
            WHERE u.role = "customer"
              AND m.is_read = 0
        ');
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Marca como leídos todos los mensajes de una conversación
     * que NO fueron enviados por el usuario dado.
     */
    public function markAsRead(int $conversationId, int $userId): void
    {
        $this->db->prepare('
            UPDATE messages
            SET is_read = 1
            WHERE conversation_id = ? AND user_id != ?
        ')->execute([$conversationId, $userId]);
    }
}
