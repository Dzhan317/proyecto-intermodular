<?php
declare(strict_types=1);

/*
 * Checkout — base estructural para Fase 6.
 *
 * Este controlador existe para que las rutas de /checkout/* no devuelvan 404
 * mientras el checkout completo se implementa en Fase 6.
 *
 * Flujo completo (Fase 6):
 *   1. GET  /checkout/shipping  → formulario de dirección de envío
 *   2. POST /checkout/shipping  → guarda dirección + método en $_SESSION['checkout']
 *   3. GET  /checkout/payment   → inicia sesión de Stripe Checkout
 *   4. GET  /checkout/success   → Stripe redirige aquí tras pago correcto
 *                                 → crea orden en BD + vacía carrito
 *   5. GET  /checkout/cancel    → Stripe redirige aquí si el usuario cancela
 *
 * Tablas de BD involucradas (Fase 6):
 *   - orders        → se crea al confirmar el pago
 *   - order_items   → una fila por producto (con product_name_snapshot)
 *   - payments      → registro del pago de Stripe
 *   - addresses     → opcionalmente se guarda la dirección del usuario
 *
 * Constantes de config.php necesarias para Fase 6:
 *   - STRIPE_PUBLIC_KEY
 *   - STRIPE_SECRET_KEY
 *   - STRIPE_WEBHOOK_SECRET
 */

class CheckoutController extends Controller
{
    // GET /checkout/shipping
    public function shipping(array $params): void
    {
        $this->requireAuth();

        // Redirige al carrito hasta que se implemente la Fase 6
        $_SESSION['cart_error'] = 'El proceso de pago estará disponible próximamente.';
        $this->redirect(APP_URL . '/cart');
    }

    // POST /checkout/shipping
    public function saveShipping(array $params): void
    {
        $this->requireAuth();
        $this->redirect(APP_URL . '/cart');
    }

    // GET /checkout/payment
    public function payment(array $params): void
    {
        $this->requireAuth();
        $this->redirect(APP_URL . '/cart');
    }

    // GET /checkout/success
    public function success(array $params): void
    {
        $this->requireAuth();
        $this->redirect(APP_URL . '/');
    }

    // GET /checkout/cancel
    public function cancel(array $params): void
    {
        $this->requireAuth();

        $_SESSION['cart_error'] = 'El pago fue cancelado. Tu carrito sigue disponible.';
        $this->redirect(APP_URL . '/cart');
    }
}
