<?php
declare(strict_types=1);

/*
 * Controlador de productos.
 * index() gestiona el buscador (GET /products?q=).
 * show()  muestra el detalle de un producto.
 *
 * Motor de recomendaciones:
 * - Registra interacción tipo 'view' al visitar un producto
 * - Actualiza user_interests con peso WEIGHT_VIEW (1 punto)
 * - Usa getSmartRelated() con userId para aplicar anti-repetición (Mejora 5)
 * - Fallback a getRelated() para usuarios no autenticados
 */

require_once APP_PATH . '/Models/ProductModel.php';
require_once APP_PATH . '/Models/CategoryModel.php';

class ProductController extends Controller
{
    private const PER_PAGE = 12;

    // GET /products?q=término
    public function index(array $params): void
    {
        $query = trim($_GET['q'] ?? '');

        if ($query === '') {
            $this->redirect(APP_URL . '/');
        }

        $query = mb_substr($query, 0, 100);

        $productModel = new ProductModel();
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $total    = $productModel->countSearch($query);
        $pages    = max(1, (int) ceil($total / self::PER_PAGE));
        $page     = min($page, $pages);
        $products = $productModel->search($query, $page, self::PER_PAGE);

        $this->view('products.search', [
            'pageTitle' => 'Resultados para "' . htmlspecialchars($query) . '" — PrimeLux SmartShop',
            'query'     => $query,
            'products'  => $products,
            'total'     => $total,
            'page'      => $page,
            'pages'     => $pages,
        ]);
    }

    // GET /products/:slug
    public function show(array $params): void
    {
        $slug         = $params['slug'] ?? '';
        $productModel = new ProductModel();
        $product      = $productModel->getBySlug($slug);

        if (!$product) {
            http_response_code(404);
            require_once APP_PATH . '/Views/errors/404.php';
            return;
        }

        $variant = $productModel->getDefaultVariant($product['id']);

        // Galería de imágenes — principal + secundarias
        $images = $productModel->getImages($product['id']);

        // Categoría
        $categoryId = 0;
        if (!empty($product['category_slug'])) {
            $cat = (new CategoryModel())->getBySlug($product['category_slug']);
            if ($cat) $categoryId = (int) $cat['id'];
        }

        // Motor de recomendaciones — solo si el usuario está autenticado
        if ($this->isLoggedIn()) {
            $userId = (int) $_SESSION['user_id'];

            // Registra la visita (Mejora 2 — tipo 'view', evita duplicados diarios)
            $productModel->recordInteraction($userId, (int) $product['id'], 'view');

            // Actualiza interés por categoría con peso VIEW (1 punto)
            if ($categoryId > 0) {
                $productModel->updateUserInterest($userId, $categoryId, 'view');
            }

            // Productos relacionados inteligentes con anti-repetición (Mejoras 1,4,5)
            $related = $productModel->getSmartRelated(
                (int) $product['id'],
                $categoryId,
                4,
                $userId
            );
        } else {
            // Usuario no autenticado — relacionados aleatorios sin personalización
            $related = $productModel->getRelated((int) $product['id'], $categoryId, 4);
        }

        $this->view('products.show', [
            'pageTitle' => htmlspecialchars($product['name']) . ' | PrimeLux SmartShop',
            'product'   => $product,
            'variant'   => $variant,
            'images'    => $images,
            'related'   => $related,
            'csrfToken' => $this->csrfToken(),
        ]);
    }
}
