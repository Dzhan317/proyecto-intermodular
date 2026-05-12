<?php
declare(strict_types=1);

/*
 * Gestiona el carrito de compra basado en sesión PHP.
 * Las tablas carts/cart_items de la BD quedan como base para una
 * futura migración a carrito persistente (ver 012-cart-persistence-deferred.md).
 *
 * Estructura de $_SESSION['cart']:
 * [
 *   'items' => [
 *     variant_id (string) => [
 *       'product_id'   => int,
 *       'variant_id'   => int,
 *       'variant_name' => string,   // variants.name → "Unidad", "16GB"...
 *       'name'         => string,   // snapshot del nombre al añadir
 *       'price'        => float,    // snapshot del precio (base_price + extra_price)
 *       'quantity'     => int,
 *       'image_url'    => string|null,
 *       'slug'         => string,
 *     ]
 *   ]
 * ]
 *
 * Interacciones: cada add() registra en interactions(type='cart')
 * para alimentar el motor de recomendaciones (Block 4 del schema).
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

        $key   = (string) $variantId;
        $items = &$_SESSION['cart']['items'];

        // Precio real = base_price + extra_price de la variante
        $unitPrice = (float) $product['base_price'] + (float) ($variant['extra_price'] ?? 0);

        if (isset($items[$key])) {
            $newQty              = $items[$key]['quantity'] + $quantity;
            $items[$key]['quantity'] = min($newQty, (int) $variant['stock']);
        } else {
            $items[$key] = [
                'product_id'   => (int) $product['id'],
                'variant_id'   => $variantId,
                'variant_name' => $variant['name'] ?? 'Unidad',
                'name'         => $product['name'],
                'price'        => $unitPrice,
                'quantity'     => min($quantity, (int) $variant['stock']),
                'image_url'    => $product['image_url'] ?? null,
                'slug'         => $product['slug'],
            ];
        }

        // Registra interacción tipo 'cart' para el motor de recomendaciones
        $this->logCartInteraction((int) $product['id']);

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

        if (isset($_SESSION['cart']['items'][$variantId])) {
            $_SESSION['cart']['items'][$variantId]['quantity'] = $quantity;
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

    /**
     * Los precios en BD ya incluyen IVA.
     * Se desglosa informativamente (21%) — coherente con orders.total.
     */
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

    /**
     * Registra interacción de carrito en interactions.
     * Alimenta el motor de recomendaciones (Block 4 del schema).
     * No interrumpe el flujo si falla.
     */
    private function logCartInteraction(int $productId): void
    {
        try {
            Database::getInstance()
                ->prepare('INSERT INTO interactions (user_id, product_id, type) VALUES (?, ?, "cart")')
                ->execute([(int) $_SESSION['user_id'], $productId]);
        } catch (\Throwable $e) {
            error_log('[CartController] Interacción no registrada: ' . $e->getMessage());
        }
    }

    /**
     * Productos relacionados de las categorías presentes en el carrito.
     * Excluye los productos ya añadidos.
     */
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
