<?php
declare(strict_types=1);

/*
 * Detalle de producto.
 * Muestra información completa, stock (variante por defecto) y productos relacionados.
 */

require_once APP_PATH . '/Models/ProductModel.php';
require_once APP_PATH . '/Models/CategoryModel.php';

class ProductController extends Controller
{
    public function index(array $params): void
    {
        $this->redirect(APP_URL . '/');
    }

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

        // Obtiene el ID de categoría desde el slug que sí viene del JOIN
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
        ]);
    }
}
