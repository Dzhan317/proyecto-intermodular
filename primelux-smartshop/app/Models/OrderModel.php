<?php
declare(strict_types=1);

/*
 * Gestiona la creación de pedidos en la BD.
 * Se usa en CheckoutController::success() tras confirmar el pago con Stripe.
 *
 * Tablas involucradas:
 *   - orders       → pedido principal
 *   - order_items  → líneas del pedido (con product_name_snapshot)
 *   - payments     → registro del pago de Stripe
 *   - addresses    → dirección del usuario (guardada/actualizada en checkout)
 */

class OrderModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Crea el pedido completo en una transacción:
     * orders + order_items + payments
     *
     * @param array $checkoutSession  $_SESSION['checkout']
     * @param array $cartItems        $_SESSION['cart']['items']
     * @param float $total            Total con IVA
     * @param string $stripeSessionId ID de sesión de Stripe
     * @param int   $userId
     * @return int  ID del pedido creado
     */
    public function createFromCheckout(
        array  $checkoutSession,
        array  $cartItems,
        float  $total,
        string $stripeSessionId,
        int    $userId
    ): int {
        $this->db->beginTransaction();

        try {
            // 1. Crear la orden
            $stmt = $this->db->prepare('
                INSERT INTO orders (
                    user_id, status, shipping_type, shipping_cost, total,
                    street, city, province, postal_code, country, phone,
                    stripe_session_id
                ) VALUES (?, "paid", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $userId,
                $checkoutSession['shipping_type'] ?? 'standard',
                (float) ($checkoutSession['shipping_cost'] ?? 0),
                $total,
                $checkoutSession['street']      ?? '',
                $checkoutSession['city']        ?? '',
                $checkoutSession['province']    ?? '',
                $checkoutSession['postal_code'] ?? '',
                $checkoutSession['country']     ?? 'España',
                $checkoutSession['phone']       ?? '',
                $stripeSessionId,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // 2. Crear las líneas del pedido
            $stmtItem = $this->db->prepare('
                INSERT INTO order_items (
                    order_id, variant_id, product_name_snapshot,
                    quantity, unit_price, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?)
            ');

            foreach ($cartItems as $item) {
                $subtotal = round($item['price'] * $item['quantity'], 2);
                $stmtItem->execute([
                    $orderId,
                    $item['variant_id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $subtotal,
                ]);
            }

            // 3. Registrar el pago
            $this->db->prepare('
                INSERT INTO payments (
                    order_id, payment_provider, external_payment_id,
                    payment_method, payment_status, amount, currency
                ) VALUES (?, "stripe", ?, "card", "completed", ?, "EUR")
            ')->execute([$orderId, $stripeSessionId, $total]);

            $this->db->commit();
            return $orderId;

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Obtiene un pedido por ID con sus líneas. */
    public function findById(int $orderId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT o.*,
                   u.name AS user_name, u.email AS user_email
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            WHERE o.id = ?
            LIMIT 1
        ');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        if (!$order) return false;

        $stmt = $this->db->prepare('
            SELECT oi.*, v.name AS variant_name
            FROM order_items oi
            LEFT JOIN variants v ON v.id = oi.variant_id
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();

        return $order;
    }

    /**
     * Guarda o actualiza la dirección por defecto del usuario en addresses.
     * Coherente con la tabla addresses del schema.
     */
    public function saveAddress(int $userId, array $checkoutSession): void
    {
        // Desactiva las anteriores
        $this->db->prepare('
            UPDATE addresses SET is_default = 0 WHERE user_id = ?
        ')->execute([$userId]);

        // Comprueba si ya existe una dirección idéntica
        $stmt = $this->db->prepare('
            SELECT id FROM addresses
            WHERE user_id = ? AND street = ? AND postal_code = ?
            LIMIT 1
        ');
        $stmt->execute([
            $userId,
            $checkoutSession['street']      ?? '',
            $checkoutSession['postal_code'] ?? '',
        ]);
        $existing = $stmt->fetch();

        if ($existing) {
            $this->db->prepare('
                UPDATE addresses SET is_default = 1 WHERE id = ?
            ')->execute([$existing['id']]);
        } else {
            $this->db->prepare('
                INSERT INTO addresses (
                    user_id, street, city, province,
                    postal_code, country, phone, is_default
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 1)
            ')->execute([
                $userId,
                $checkoutSession['street']      ?? '',
                $checkoutSession['city']        ?? '',
                $checkoutSession['province']    ?? '',
                $checkoutSession['postal_code'] ?? '',
                $checkoutSession['country']     ?? 'España',
                $checkoutSession['phone']       ?? '',
            ]);
        }
    }

    /** Obtiene la dirección por defecto del usuario para precargar el checkout. */
    public function getDefaultAddress(int $userId): array|false
    {
        $stmt = $this->db->prepare('
            SELECT * FROM addresses
            WHERE user_id = ? AND is_default = 1
            LIMIT 1
        ');
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    /** Pedidos de un usuario para el historial (Fase 7). */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
