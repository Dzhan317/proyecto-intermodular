<?php
declare(strict_types=1);

/*
 * Panel de administración — Fases 7, 8 y 9.
 * Acceso restringido a usuarios con rol 'admin' mediante requireAdmin().
 *
 * Métodos públicos:
 *   dashboard()             → GET  /admin
 *   products()              → GET  /admin/products
 *   createProduct()         → GET  /admin/products/create
 *   storeProduct()          → POST /admin/products/create
 *   editProduct()           → GET  /admin/products/:id/edit
 *   updateProduct()         → POST /admin/products/:id/edit
 *   deleteProduct()         → POST /admin/products/:id/delete
 *   orders()                → GET  /admin/orders
 *   showOrder()             → GET  /admin/orders/:id
 *   updateOrderStatus()     → POST /admin/orders/:id
 *   users()                 → GET  /admin/users
 *   updateUserStatus()      → POST /admin/users/:id
 *   categories()            → GET  /admin/categories
 *   createCategory()        → GET  /admin/categories/create
 *   storeCategory()         → POST /admin/categories/create
 *   editCategory()          → GET  /admin/categories/:id/edit
 *   updateCategory()        → POST /admin/categories/:id/edit
 *   deleteCategory()        → POST /admin/categories/:id/delete
 *   support()               → GET  /admin/support
 *   showSupportTicket()     → GET  /admin/support/:id
 *   replySupport()          → POST /admin/support/:id/message
 *   updateSupportStatus()   → POST /admin/support/:id/status
 *
 * Helpers privados:
 *   getFlash()           → Lee y limpia admin_success/admin_error de sesión
 *   parseProductPost()   → Extrae y normaliza los campos POST de producto
 */

require_once APP_PATH . '/Models/ProductModel.php';
require_once APP_PATH . '/Models/OrderModel.php';
require_once APP_PATH . '/Models/UserModel.php';
require_once APP_PATH . '/Models/SupportModel.php';
require_once APP_PATH . '/Models/CategoryModel.php';

class AdminController extends Controller
{
    // ─── Helpers privados ────────────────────────────────────────────────────

    /** Lee y limpia los flash messages de sesión. Devuelve [success, error]. */
    private function getFlash(): array
    {
        $success = $_SESSION['admin_success'] ?? '';
        $error   = $_SESSION['admin_error']   ?? '';
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);
        return [$success, $error];
    }

    /** Extrae y normaliza los campos POST de un producto. */
    private function parseProductPost(): array
    {
        return [
            'name'        => trim($_POST['name']        ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'brand'       => trim($_POST['brand']       ?? ''),
            'base_price'  => (float) ($_POST['base_price']  ?? 0),
            'cost_price'  => (float) ($_POST['cost_price']  ?? 0),
            'stock'       => (int)   ($_POST['stock']       ?? 0),
            'category_id' => (int)   ($_POST['category_id'] ?? 0),
            'status'      => in_array($_POST['status'] ?? '', ['active', 'inactive'])
                                ? $_POST['status'] : 'active',
        ];
    }

    // ─── Dashboard ───────────────────────────────────────────────────────────

    // GET /admin
    public function dashboard(array $params): void
    {
        $this->requireAdmin();

        $db = Database::getInstance();

        $totalOrders   = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $activeUsers   = (int) $db->query('SELECT COUNT(*) FROM users WHERE role = "customer" AND status = "active"')->fetchColumn();
        $totalUsers    = (int) $db->query('SELECT COUNT(*) FROM users WHERE role = "customer"')->fetchColumn();
        $blockedUsers  = $totalUsers - $activeUsers;
        $totalProducts    = (int) $db->query('SELECT COUNT(*) FROM products WHERE status = "active"')->fetchColumn();
        $allProducts      = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();

        $totalRevenue = (float) $db->query('
            SELECT COALESCE(SUM(total), 0) FROM orders
            WHERE status IN ("paid", "shipped", "delivered")
        ')->fetchColumn();

        $totalCost = (float) $db->query('
            SELECT COALESCE(SUM(oi.quantity * p.cost_price), 0)
            FROM order_items oi
            INNER JOIN variants v ON v.id = oi.variant_id
            INNER JOIN products p ON p.id = v.product_id
            INNER JOIN orders o   ON o.id = oi.order_id
            WHERE o.status IN ("paid", "shipped", "delivered")
        ')->fetchColumn();

        $grossMargin = $totalRevenue - $totalCost;
        $marginPct   = $totalRevenue > 0 ? round(($grossMargin / $totalRevenue) * 100, 1) : 0;

        $recentOrders = $db->query('
            SELECT o.id, o.total, o.status, o.created_at,
                   u.name AS user_name, u.last_name AS user_last_name
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC
            LIMIT 5
        ')->fetchAll();

        $this->view('admin.dashboard', [
            'pageTitle'     => 'Dashboard — PrimeLux Admin',
            'totalOrders'   => $totalOrders,
            'totalRevenue'  => $totalRevenue,
            'activeUsers'   => $activeUsers,
            'totalUsers'    => $totalUsers,
            'blockedUsers'  => $blockedUsers,
            'totalProducts' => $totalProducts,
            'allProducts'   => $allProducts,
            'totalCost'     => $totalCost,
            'grossMargin'   => $grossMargin,
            'marginPct'     => $marginPct,
            'recentOrders'  => $recentOrders,
        ]);
    }

    // ─── Productos ───────────────────────────────────────────────────────────

    // GET /admin/products
    public function products(array $params): void
    {
        $this->requireAdmin();

        $productModel = new ProductModel();
        $page         = max(1, (int) ($_GET['page'] ?? 1));
        $search       = trim($_GET['q'] ?? '');
        $perPage      = 20;

        $products = $search
            ? $productModel->search($search, $page, $perPage)
            : $productModel->getAllAdmin($page, $perPage);

        $total = $search
            ? $productModel->countSearch($search)
            : $productModel->countAll();

        [$success, $error] = $this->getFlash();

        $this->view('admin.products', [
            'pageTitle' => 'Productos — PrimeLux Admin',
            'products'  => $products,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $perPage,
            'search'    => $search,
            'success'   => $success,
            'error'     => $error,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // GET /admin/products/create
    public function createProduct(array $params): void
    {
        $this->requireAdmin();

        $categoryModel = new CategoryModel();

        $this->view('admin.products-form', [
            'pageTitle'  => 'Añadir producto — PrimeLux Admin',
            'categories' => $categoryModel->getAllAdmin(),
            'csrfToken'  => $this->csrfToken(),
            'error'      => $_SESSION['admin_error'] ?? '',
        ]);

        unset($_SESSION['admin_error']);
    }

    // POST /admin/products/create
    public function storeProduct(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $data = $this->parseProductPost();

        if (!$data['name'] || $data['base_price'] <= 0 || !$data['category_id']) {
            $_SESSION['admin_error'] = 'Nombre, precio y categoría son obligatorios.';
            $this->redirect(APP_URL . '/admin/products/create');
        }

        $productModel = new ProductModel();

        // Validar duplicado — no permitir dos productos con el mismo nombre
        if ($productModel->nameExists($data['name'])) {
            $_SESSION['admin_error'] = 'Ya existe un producto con ese nombre.';
            $this->redirect(APP_URL . '/admin/products/create');
        }

        $productModel->create($data);

        $_SESSION['admin_success'] = 'Producto creado correctamente.';
        $this->redirect(APP_URL . '/admin/products');
    }

    // GET /admin/products/:id/edit
    public function editProduct(array $params): void
    {
        $this->requireAdmin();

        $productId    = (int) ($params['id'] ?? 0);
        $productModel = new ProductModel();
        $product      = $productModel->findById($productId);

        if (!$product) {
            $this->redirect(APP_URL . '/admin/products');
        }

        $categoryModel = new CategoryModel();

        $this->view('admin.products-form', [
            'pageTitle'  => 'Editar producto — PrimeLux Admin',
            'product'    => $product,
            'categories' => $categoryModel->getAllAdmin(),
            'csrfToken'  => $this->csrfToken(),
            'error'      => $_SESSION['admin_error'] ?? '',
        ]);

        unset($_SESSION['admin_error']);
    }

    // POST /admin/products/:id/edit
    public function updateProduct(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $productId = (int) ($params['id'] ?? 0);
        $data      = $this->parseProductPost();

        if (!$data['name'] || $data['base_price'] <= 0 || !$data['category_id']) {
            $_SESSION['admin_error'] = 'Nombre, precio y categoría son obligatorios.';
            $this->redirect(APP_URL . '/admin/products/' . $productId . '/edit');
        }

        $productModel = new ProductModel();

        // Validar duplicado — excluir el propio producto del check
        if ($productModel->nameExists($data['name'], $productId)) {
            $_SESSION['admin_error'] = 'Ya existe otro producto con ese nombre.';
            $this->redirect(APP_URL . '/admin/products/' . $productId . '/edit');
        }

        $productModel->update($productId, $data);

        $_SESSION['admin_success'] = 'Producto actualizado correctamente.';
        $this->redirect(APP_URL . '/admin/products');
    }

    // POST /admin/products/:id/delete
    public function deleteProduct(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $productId    = (int) ($params['id'] ?? 0);
        $productModel = new ProductModel();
        $productModel->delete($productId);

        $_SESSION['admin_success'] = 'Producto desactivado correctamente.';
        $this->redirect(APP_URL . '/admin/products');
    }

    // ─── Pedidos ─────────────────────────────────────────────────────────────

    // GET /admin/orders
    public function orders(array $params): void
    {
        $this->requireAdmin();

        $db      = Database::getInstance();
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $stmt = $db->prepare('
            SELECT o.*, u.name AS user_name, u.last_name AS user_last_name, u.email AS user_email
            FROM orders o
            INNER JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ');
        $stmt->execute([$perPage, $offset]);
        $orders = $stmt->fetchAll();

        $total = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();

        [$success, $error] = $this->getFlash();

        $this->view('admin.orders', [
            'pageTitle' => 'Pedidos — PrimeLux Admin',
            'orders'    => $orders,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $perPage,
            'success'   => $success,
            'error'     => $error,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // GET /admin/orders/:id
    public function showOrder(array $params): void
    {
        $this->requireAdmin();

        $orderId     = (int) ($params['id'] ?? 0);
        $orderModel  = new OrderModel();
        $order       = $orderModel->findById($orderId);

        if (!$order) {
            $this->redirect(APP_URL . '/admin/orders');
        }

        $this->view('admin.order-detail', [
            'pageTitle' => 'Pedido #' . $orderId . ' — PrimeLux Admin',
            'order'     => $order,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // POST /admin/orders/:id
    public function updateOrderStatus(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $orderId = (int) ($params['id'] ?? 0);
        $status  = $_POST['status'] ?? '';

        $allowed = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $this->redirect(APP_URL . '/admin/orders');
        }

        Database::getInstance()
            ->prepare('UPDATE orders SET status = ? WHERE id = ?')
            ->execute([$status, $orderId]);

        $_SESSION['admin_success'] = 'Estado del pedido actualizado.';
        $this->redirect(APP_URL . '/admin/orders');
    }

    // ─── Usuarios ────────────────────────────────────────────────────────────

    // GET /admin/users
    public function users(array $params): void
    {
        $this->requireAdmin();

        $userModel = new UserModel();
        $page      = max(1, (int) ($_GET['page'] ?? 1));
        $search    = trim($_GET['q'] ?? '');
        $perPage   = 20;

        $users = $userModel->getAllAdmin($page, $perPage, $search);
        $total = $userModel->countAll($search);

        [$success, $error] = $this->getFlash();

        $this->view('admin.users', [
            'pageTitle' => 'Usuarios — PrimeLux Admin',
            'users'     => $users,
            'total'     => $total,
            'page'      => $page,
            'perPage'   => $perPage,
            'search'    => $search,
            'success'   => $success,
            'error'     => $error,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    // POST /admin/users/:id
    public function updateUserStatus(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $userId = (int) ($params['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        $allowed = ['active', 'blocked'];
        if (!in_array($status, $allowed)) {
            $this->redirect(APP_URL . '/admin/users');
        }

        if ($userId === (int) $_SESSION['user_id']) {
            $_SESSION['admin_error'] = 'No puedes modificar tu propio estado.';
            $this->redirect(APP_URL . '/admin/users');
        }

        Database::getInstance()
            ->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?')
            ->execute([$status, $userId]);

        $_SESSION['admin_success'] = 'Estado del usuario actualizado.';
        $this->redirect(APP_URL . '/admin/users');
    }


    // ─── Categorías ──────────────────────────────────────────────────────────

    // GET /admin/categories
    public function categories(array $params): void
    {
        $this->requireAdmin();

        $categoryModel = new CategoryModel();
        $search        = trim($_GET['q'] ?? '');
        $page          = max(1, (int) ($_GET['page'] ?? 1));
        $perPage       = 5;
        $allCategories = $categoryModel->getAllAdmin();

        // Filtrar por búsqueda si se ha introducido un término
        if ($search !== '') {
            $allCategories = array_filter(
                $allCategories,
                fn($c) => str_contains(strtolower($c['name']), strtolower($search))
            );
            $allCategories = array_values($allCategories);
        }

        $total      = count($allCategories);
        $categories = array_slice($allCategories, ($page - 1) * $perPage, $perPage);

        [$success, $error] = $this->getFlash();

        $this->view('admin.categories', [
            'pageTitle'  => 'Categorías — PrimeLux Admin',
            'categories' => $categories,
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'search'     => $search,
            'success'    => $success,
            'error'      => $error,
            'csrfToken'  => $this->csrfToken(),
        ]);
    }

    // GET /admin/categories/create
    public function createCategory(array $params): void
    {
        $this->requireAdmin();

        $this->view('admin.categories-form', [
            'pageTitle' => 'Añadir categoría — PrimeLux Admin',
            'csrfToken' => $this->csrfToken(),
            'error'     => $_SESSION['admin_error'] ?? '',
        ]);

        unset($_SESSION['admin_error']);
    }

    // POST /admin/categories/create
    public function storeCategory(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $name        = trim($_POST['name']        ?? '');
        $description = trim($_POST['description'] ?? '');
        $featured    = isset($_POST['featured']) ? 1 : 0;
        $status      = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

        if (!$name) {
            $_SESSION['admin_error'] = 'El nombre de la categoría es obligatorio.';
            $this->redirect(APP_URL . '/admin/categories/create');
        }

        $categoryModel = new CategoryModel();

        // Validar duplicado — no permitir dos categorías con el mismo nombre
        if ($categoryModel->nameExists($name)) {
            $_SESSION['admin_error'] = 'Ya existe una categoría con ese nombre.';
            $this->redirect(APP_URL . '/admin/categories/create');
        }

        $categoryModel->create(['name' => $name, 'description' => $description, 'featured' => $featured, 'status' => $status]);

        $_SESSION['admin_success'] = 'Categoría creada correctamente.';
        $this->redirect(APP_URL . '/admin/categories');
    }

    // GET /admin/categories/:id/edit
    public function editCategory(array $params): void
    {
        $this->requireAdmin();

        $categoryId    = (int) ($params['id'] ?? 0);
        $categoryModel = new CategoryModel();
        $category      = $categoryModel->findById($categoryId);

        if (!$category) {
            $this->redirect(APP_URL . '/admin/categories');
        }

        $this->view('admin.categories-form', [
            'pageTitle' => 'Editar categoría — PrimeLux Admin',
            'category'  => $category,
            'csrfToken' => $this->csrfToken(),
            'error'     => $_SESSION['admin_error'] ?? '',
        ]);

        unset($_SESSION['admin_error']);
    }

    // POST /admin/categories/:id/edit
    public function updateCategory(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $categoryId  = (int) ($params['id'] ?? 0);
        $name        = trim($_POST['name']        ?? '');
        $description = trim($_POST['description'] ?? '');
        $featured    = isset($_POST['featured']) ? 1 : 0;
        $status      = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

        if (!$name) {
            $_SESSION['admin_error'] = 'El nombre de la categoría es obligatorio.';
            $this->redirect(APP_URL . '/admin/categories/' . $categoryId . '/edit');
        }

        $categoryModel = new CategoryModel();

        // Validar duplicado — excluir la propia categoría del check
        if ($categoryModel->nameExists($name, $categoryId)) {
            $_SESSION['admin_error'] = 'Ya existe otra categoría con ese nombre.';
            $this->redirect(APP_URL . '/admin/categories/' . $categoryId . '/edit');
        }

        $categoryModel->update($categoryId, ['name' => $name, 'description' => $description, 'featured' => $featured, 'status' => $status]);

        $_SESSION['admin_success'] = 'Categoría actualizada correctamente.';
        $this->redirect(APP_URL . '/admin/categories');
    }

    // POST /admin/categories/:id/delete
    public function deleteCategory(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $categoryId    = (int) ($params['id'] ?? 0);
        $categoryModel = new CategoryModel();
        $result        = $categoryModel->delete($categoryId);

        if ($result['protected']) {
            $_SESSION['admin_error'] = 'La categoría "Sin categoría" es una categoría de sistema y no puede eliminarse.';
        } elseif ($result['deleted']) {
            if ($result['reassigned'] > 0) {
                $_SESSION['admin_success'] = 'Categoría eliminada. ' . $result['reassigned'] . ' producto(s) reasignado(s) a "Sin categoría".';
            } else {
                $_SESSION['admin_success'] = 'Categoría eliminada correctamente.';
            }
        } else {
            $_SESSION['admin_error'] = 'No se pudo eliminar la categoría.';
        }

        $this->redirect(APP_URL . '/admin/categories');
    }

    // ─── Soporte ─────────────────────────────────────────────────────────────

    // GET /admin/support
    public function support(array $params): void
    {
        $this->requireAdmin();

        $supportModel = new SupportModel();
        $page         = max(1, (int) ($_GET['page'] ?? 1));
        $perPage      = 20;

        $conversations = $supportModel->getAll($page, $perPage);
        $total         = $supportModel->countAll();

        $this->view('admin.support', [
            'pageTitle'     => 'Soporte — PrimeLux Admin',
            'conversations' => $conversations,
            'total'         => $total,
            'page'          => $page,
            'perPage'       => $perPage,
            'csrfToken'     => $this->csrfToken(),
            ...$this->getFlash(),
        ]);
    }

    // GET /admin/support/:id
    public function showSupportTicket(array $params): void
    {
        $this->requireAdmin();

        $id           = (int) ($params['id'] ?? 0);
        $supportModel = new SupportModel();
        $conversation = $supportModel->findById($id);

        if (!$conversation) {
            $this->redirect(APP_URL . '/admin/support');
        }

        // Marca como leídos los mensajes del cliente al abrir la conversación
        $supportModel->markAsRead($id, (int) $_SESSION['user_id']);

        $this->view('admin.support-detail', [
            'pageTitle'    => 'Conversación #' . $id . ' — PrimeLux Admin',
            'conversation' => $conversation,
            'csrfToken'    => $this->csrfToken(),
            ...$this->getFlash(),
        ]);
    }

    // GET /admin/support/:id/messages?since=:lastId — polling JSON para el admin
    public function getSupportMessages(array $params): void
    {
        $this->requireAdmin();

        $id           = (int) ($params['id'] ?? 0);
        $supportModel = new SupportModel();
        $conversation = $supportModel->findById($id);

        if (!$conversation) {
            http_response_code(404);
            echo json_encode(['error' => 'No encontrado']);
            exit;
        }

        // Marca como leídos los mensajes del cliente al hacer polling
        $supportModel->markAsRead($id, (int) $_SESSION['user_id']);

        $lastId   = (int) ($_GET['since'] ?? 0);
        $messages = $supportModel->getMessagesSince($id, $lastId);
        $adminId  = (int) $_SESSION['user_id'];

        $result = array_map(function ($msg) use ($adminId) {
            return [
                'id'          => (int) $msg['id'],
                'message'     => htmlspecialchars($msg['message'], ENT_QUOTES),
                'sender_name' => htmlspecialchars($msg['sender_name'], ENT_QUOTES),
                'sender_role' => $msg['sender_role'],
                'is_own'      => ($msg['sender_role'] === 'admin'),
                'time'        => date('H:i', strtotime($msg['created_at'])),
            ];
        }, $messages);

        header('Content-Type: application/json');
        echo json_encode(['messages' => $result]);
        exit;
    }

    // GET /admin/support/unread — cuenta mensajes no leídos para el badge del admin
    public function getSupportUnread(array $params): void
    {
        if (!$this->isLoggedIn() || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['count' => 0]);
            exit;
        }

        $supportModel = new SupportModel();
        $count        = $supportModel->getUnreadCountAdmin();

        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    // POST /admin/support/:id/message
    public function replySupport(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id           = (int) ($params['id'] ?? 0);
        $supportModel = new SupportModel();
        $conversation = $supportModel->findById($id);

        if (!$conversation) {
            $this->redirect(APP_URL . '/admin/support');
        }

        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            $_SESSION['admin_error'] = 'El mensaje no puede estar vacío.';
            $this->redirect(APP_URL . '/admin/support/' . $id);
        }

        $supportModel->addMessage($id, (int) $_SESSION['user_id'], $message);

        // Si es una petición AJAX devuelve JSON con el mensaje recién creado
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $messages = $supportModel->getMessagesSince($id, 0);
            $last     = end($messages);
            header('Content-Type: application/json');
            echo json_encode([
                'message' => [
                    'id'          => (int) $last['id'],
                    'message'     => htmlspecialchars($last['message'], ENT_QUOTES),
                    'sender_name' => htmlspecialchars($last['sender_name'], ENT_QUOTES),
                    'sender_role' => $last['sender_role'],
                    'is_own'      => true,
                    'time'        => date('H:i', strtotime($last['created_at'])),
                ],
            ]);
            exit;
        }

        $this->redirect(APP_URL . '/admin/support/' . $id);
    }

    // POST /admin/support/:id/status
    public function updateSupportStatus(array $params): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id      = (int) ($params['id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        $allowed = ['open', 'closed'];

        if (!in_array($status, $allowed)) {
            $this->redirect(APP_URL . '/admin/support');
        }

        $supportModel = new SupportModel();
        $supportModel->updateStatus($id, $status);

        $_SESSION['admin_success'] = 'Estado actualizado correctamente.';
        $this->redirect(APP_URL . '/admin/support/' . $id);
    }

}
