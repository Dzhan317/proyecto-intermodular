<?php
declare(strict_types=1);

require_once APP_PATH . '/Models/CategoryModel.php';
require_once APP_PATH . '/Models/ProductModel.php';

class CategoryController extends Controller
{
    private const PER_PAGE = 12;

    public function show(array $params): void
    {
        $slug = $params['slug'] ?? '';

        $categoryModel = new CategoryModel();
        $category      = $categoryModel->getBySlug($slug);

        if (!$category) {
            http_response_code(404);
            require_once APP_PATH . '/Views/errors/404.php';
            return;
        }

        $productModel   = new ProductModel();
        $priceRange     = $productModel->getPriceRange($category['id']);
        $availableBrands = $productModel->getBrandsByCategory($category['id']);

        // ── Parámetros GET saneados ───────────────────────────────────────
        $sort = in_array($_GET['sort'] ?? '', ['newest', 'price_asc', 'price_desc'])
                ? $_GET['sort'] : 'newest';

        $minPrice = max($priceRange['min'], (float) ($_GET['min_price'] ?? 0));
        $maxPrice = (float) ($_GET['max_price'] ?? 0);
        if ($maxPrice <= 0 || $maxPrice > $priceRange['max']) $maxPrice = $priceRange['max'];
        if ($minPrice > $maxPrice) $minPrice = $priceRange['min'];

        $inStock = isset($_GET['in_stock']) && $_GET['in_stock'] === '1';

        // Marcas seleccionadas — solo acepta valores que existen en BD
        $selectedBrands = array_filter(
            (array) ($_GET['brands'] ?? []),
            fn($b) => in_array($b, $availableBrands, true)
        );
        $selectedBrands = array_values($selectedBrands);

        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $total = $productModel->countByCategory(
            $category['id'], $minPrice, $maxPrice, $inStock, $selectedBrands
        );
        $pages    = max(1, (int) ceil($total / self::PER_PAGE));
        $page     = min($page, $pages);
        $products = $productModel->getByCategory(
            $category['id'], $sort, $page, self::PER_PAGE,
            $minPrice, $maxPrice, $inStock, $selectedBrands
        );

        $hasActiveFilters = $inStock
            || !empty($selectedBrands)
            || $minPrice > $priceRange['min']
            || $maxPrice < $priceRange['max'];

        $this->view('products.listing', [
            'pageTitle'       => htmlspecialchars($category['name']) . ' | PrimeLux SmartShop',
            'category'        => $category,
            'products'        => $products,
            'sort'            => $sort,
            'page'            => $page,
            'pages'           => $pages,
            'total'           => $total,
            'priceRange'      => $priceRange,
            'minPrice'        => $minPrice,
            'maxPrice'        => $maxPrice,
            'inStock'         => $inStock,
            'availableBrands' => $availableBrands,
            'selectedBrands'  => $selectedBrands,
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }
}
