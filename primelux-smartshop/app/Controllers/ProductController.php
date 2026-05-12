<?php
declare(strict_types=1);

/*
 * Controlador de productos.
 * index() gestiona el buscador (GET /products?q=).
 * show()  muestra el detalle de un producto.
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

        // Sin término de búsqueda → vuelve a la home
        if ($query === '') {
            $this->redirect(APP_URL . '/');
        }

        // Limita la longitud del término para evitar consultas abusivas
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

        $categoryId = 0;
        if (!empty($product['category_slug'])) {
            $cat = (new CategoryModel())->getBySlug($product['category_slug']);
            if ($cat) $categoryId = (int) $cat['id'];
        }

        $related = $productModel->getRelated($product['id'], $categoryId, 4);

        $this->view('products.show', [
            'pageTitle' => htmlspecialchars($product['name']) . ' | PrimeLux SmartShop',
            'product'   => $product,
            'variant'   => $variant,
            'related'   => $related,
            'csrfToken' => $this->csrfToken(),
        ]);
    }
}
