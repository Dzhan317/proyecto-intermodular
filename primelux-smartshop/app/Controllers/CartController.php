<?php
declare(strict_types=1);

/*
 * Gestiona el carrito de compra basado en sesión PHP.
 * Motor de recomendaciones — Mejora 2:
 *   Al añadir un producto al carrito se registra una interacción tipo 'cart'
 *   y se actualiza user_interests con peso WEIGHT_CART (3 puntos).
 *   Esto pondera más el interés del usuario que una simple vista.
 */

require_once APP_PATH . '/Models/ProductModel.php';

class CartController extends Controller
{
    // GET /cart
    public function index(array $params): void
    {
        $this->requireAuth();

        $items   = $this->getItems();
        $totals  = $this->calculateTotals($items);
        $related = $this->getRelatedProducts($items);

        $success = $_SESSION['cart_success'] ?? '';
        $error   = $_SESSION['cart_error']   ?? '';
        unset($_SESSION['cart_success'], $_SESSION['cart_error']);

        $this->view('cart.index', [
            'pageTitle' => 'Carrito — PrimeLux SmartShop',
            'items'     => $items,
            'totals'    => $totals,
            'related'   => $related,
            'csrfToken' => $this->csrfToken(),
            'success'   => $success,
            'error'     => $error,
        ]);
    }

    // POST /cart/add
    public function add(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $variantId = (int) ($_POST['variant_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));
        $slug      = trim($_POST['slug'] ?? '');

        if (!$variantId || !$slug) {
            $this->redirect(APP_URL . '/cart');
        }

        $productModel = new ProductModel();
        $product      = $productModel->getBySlug($slug);

        if (!$product) {
            $this->redirect(APP_URL . '/');
        }

        $variant = $productModel->getDefaultVariant($product['id']);

        if (!$variant || (int) $variant['stock'] < 1) {
            $_SESSION['cart_error'] = 'Este producto no tiene stock disponible.';
            $this->redirect(APP_URL . '/products/' . $product['slug']);
        }

        $this->initCart();

        $key      = (string) $variantId;
        $items    = &$_SESSION['cart']['items'];
        $maxStock = (int) $variant['stock'];

        $unitPrice = (float) $product['base_price'] + (float) ($variant['extra_price'] ?? 0);

        if (isset($items[$key])) {
            $newQty              = $items[$key]['quantity'] + $quantity;
            $items[$key]['quantity'] = min($newQty, $maxStock);
            $items[$key]['stock']    = $maxStock;
        } else {
            $items[$key] = [
                'product_id'   => (int) $product['id'],
                'variant_id'   => $variantId,
                'variant_name' => $variant['name'] ?? 'Unidad',
                'name'         => $product['name'],
                'price'        => $unitPrice,
                'quantity'     => min($quantity, $maxStock),
                'image_url'    => $product['image_url'] ?? null,
                'slug'         => $product['slug'],
                'stock'        => $maxStock,
            ];
        }

        // Mejora 2 — registra interacción tipo 'cart' con peso 3
        $userId = (int) $_SESSION['user_id'];
        $productId = (int) $product['id'];
        $productModel->recordInteraction($userId, $productId, 'cart');

        // Actualiza interés por categoría con peso CART (3 puntos)
        if (!empty($product['category_slug'])) {
            require_once APP_PATH . '/Models/CategoryModel.php';
            $cat = (new CategoryModel())->getBySlug($product['category_slug']);
            if ($cat) {
                $productModel->updateUserInterest($userId, (int) $cat['id'], 'cart');
            }
        }

        $_SESSION['cart_success'] = '¡Producto añadido al carrito!';
        $this->redirect(APP_URL . '/cart');
    }

    // POST /cart/update
    public function update(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $variantId = (string) ($_POST['variant_id'] ?? '');
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));

        $this->initCart();

        if (!isset($_SESSION['cart']['items'][$variantId])) {
            $this->redirect(APP_URL . '/cart');
        }

        $item = &$_SESSION['cart']['items'][$variantId];

        $productModel = new ProductModel();
        $variant      = $productModel->getDefaultVariant($item['product_id']);
        $maxStock     = $variant ? (int) $variant['stock'] : ($item['stock'] ?? 1);

        $item['stock']    = $maxStock;
        $item['quantity'] = min($quantity, $maxStock);

        if ($item['quantity'] < $quantity) {
            $_SESSION['cart_error'] = 'Solo quedan ' . $maxStock . ' unidad' . ($maxStock !== 1 ? 'es' : '') . ' disponibles de este producto.';
        }

        $this->redirect(APP_URL . '/cart');
    }

    // POST /cart/remove
    public function remove(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        $variantId = (string) ($_POST['variant_id'] ?? '');

        $this->initCart();
        unset($_SESSION['cart']['items'][$variantId]);

        $this->redirect(APP_URL . '/cart');
    }

    // ─── Helper estático — badge contador del header ──────────────────────────

    public static function getItemCount(): int
    {
        $items = $_SESSION['cart']['items'] ?? [];
        return (int) array_sum(array_column($items, 'quantity'));
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    private function initCart(): void
    {
        if (!isset($_SESSION['cart']['items'])) {
            $_SESSION['cart'] = ['items' => []];
        }
    }

    private function getItems(): array
    {
        return $_SESSION['cart']['items'] ?? [];
    }

    private function calculateTotals(array $items): array
    {
        $total     = 0.0;
        $itemCount = 0;

        foreach ($items as $item) {
            $total     += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        $ivaRate  = 0.21;
        $subtotal = $total / (1 + $ivaRate);
        $iva      = $total - $subtotal;

        return [
            'subtotal'      => round($subtotal, 2),
            'iva'           => round($iva, 2),
            'total'         => round($total, 2),
            'item_count'    => $itemCount,
            'product_count' => count($items),
        ];
    }

    private function getRelatedProducts(array $items): array
    {
        if (empty($items)) return [];

        $productModel  = new ProductModel();
        require_once APP_PATH . '/Models/CategoryModel.php';
        $categoryModel = new CategoryModel();

        $slugs   = array_column($items, 'slug');
        $related = [];
        $seen    = array_flip($slugs);

        foreach ($slugs as $slug) {
            $product = $productModel->getBySlug($slug);
            if (!$product || empty($product['category_slug'])) continue;

            $cat = $categoryModel->getBySlug($product['category_slug']);
            if (!$cat) continue;

            foreach ($productModel->getRelated($product['id'], (int) $cat['id'], 8) as $candidate) {
                $key = $candidate['slug'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $related[]  = $candidate;
                    if (count($related) >= 4) break 2;
                }
            }
        }

        return $related;
    }
}
