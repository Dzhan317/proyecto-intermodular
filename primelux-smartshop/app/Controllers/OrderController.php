<?php
declare(strict_types=1);

/*
 * Gestiona las páginas de pedidos del usuario cliente.
 *
 * Rutas:
 *   index() → GET /orders        — listado de pedidos del usuario
 *   show()  → GET /orders/:id    — detalle de un pedido concreto
 */

require_once APP_PATH . '/Models/OrderModel.php';

class OrderController extends Controller
{
    private OrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    // GET /orders — listado de pedidos del usuario autenticado
    public function index(array $params): void
    {
        $this->requireAuth();

        $orders = $this->orderModel->getByUser((int) $_SESSION['user_id']);

        $this->view('orders.index', [
            'pageTitle' => 'Mis pedidos | PrimeLux SmartShop',
            'orders'    => $orders,
            'activeTab' => 'orders',
        ]);
    }

    // GET /orders/:id — detalle de un pedido concreto
    public function show(array $params): void
    {
        $this->requireAuth();

        $orderId = (int) ($params['id'] ?? 0);
        $order   = $this->orderModel->findById($orderId);

        // Seguridad: el pedido debe existir y pertenecer al usuario autenticado
        if (!$order || (int) $order['user_id'] !== (int) $_SESSION['user_id']) {
            $this->redirect(APP_URL . '/orders');
        }

        $this->view('orders.show', [
            'pageTitle' => 'Pedido #' . $orderId . ' | PrimeLux SmartShop',
            'order'     => $order,
            'activeTab' => 'orders',
        ]);
    }
}
