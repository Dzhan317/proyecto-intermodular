<?php
declare(strict_types=1);

/*
 * Checkout completo con Stripe Checkout.
 * Flujo: Envío → Pago (Stripe) → Success / Cancel
 *
 * Requiere la librería stripe-php en public/vendor/stripe/init.php
 * Instalación: descargar desde github.com/stripe/stripe-php/releases
 */

require_once APP_PATH . '/Models/OrderModel.php';
require_once APP_PATH . '/Models/UserModel.php';

class CheckoutController extends Controller
{
    private const SHIPPING_OPTIONS = [
        'standard' => [
            'label'       => 'Envío estándar',
            'description' => 'Entrega en 2-4 días laborables',
            'cost'        => 0.00,
            'free_from'   => 0.00,
        ],
        'express' => [
            'label'       => 'Envío express',
            'description' => 'Entrega en 24 horas',
            'cost'        => 4.99,
            'free_from'   => null,
        ],
        'pickup_point' => [
            'label'       => 'Recogida en tienda',
            'description' => 'Disponible en 24 horas',
            'cost'        => 0.00,
            'free_from'   => null,
        ],
    ];

    // GET /checkout/shipping
    public function shipping(array $params): void
    {
        $this->requireAuth();

        if (empty($_SESSION['cart']['items'])) {
            $this->redirect(APP_URL . '/cart');
        }

        $orderModel     = new OrderModel();
        $userModel      = new UserModel();
        $user           = $userModel->findById((int) $_SESSION['user_id']);
        $savedAddress   = $orderModel->getDefaultAddress((int) $_SESSION['user_id']);
        $cartItems      = $_SESSION['cart']['items'] ?? [];
        $cartTotal      = $this->calcCartTotal($cartItems);
        $shippingMethod = $_SESSION['checkout']['shipping_type'] ?? 'standard';

        $this->view('checkout.shipping', [
            'pageTitle'      => 'Datos de envío — PrimeLux SmartShop',
            'user'           => $user,
            'savedAddress'   => $savedAddress,
            'shippingOptions' => self::SHIPPING_OPTIONS,
            'selectedShipping' => $shippingMethod,
            'cartItems'      => $cartItems,
            'cartTotal'      => $cartTotal,
            'csrfToken'      => $this->csrfToken(),
            'error'          => $_SESSION['checkout_error'] ?? '',
        ]);

        unset($_SESSION['checkout_error']);
    }

    // POST /checkout/shipping
    public function saveShipping(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        if (empty($_SESSION['cart']['items'])) {
            $this->redirect(APP_URL . '/cart');
        }

        // Validación básica
        $required = ['street', 'city', 'province', 'postal_code', 'phone'];
        foreach ($required as $field) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $_SESSION['checkout_error'] = 'Por favor, rellena todos los campos obligatorios.';
                $this->redirect(APP_URL . '/checkout/shipping');
            }
        }

        $shippingType = $_POST['shipping_type'] ?? 'standard';
        if (!array_key_exists($shippingType, self::SHIPPING_OPTIONS)) {
            $shippingType = 'standard';
        }

        // Guarda en sesión — mapeado directamente a columnas de orders
        $_SESSION['checkout'] = [
            'street'        => trim($_POST['street']      ?? ''),
            'city'          => trim($_POST['city']        ?? ''),
            'province'      => trim($_POST['province']    ?? ''),
            'postal_code'   => trim($_POST['postal_code'] ?? ''),
            'country'       => 'España',
            'phone'         => trim($_POST['phone']       ?? ''),
            'shipping_type' => $shippingType,
            'shipping_cost' => self::SHIPPING_OPTIONS[$shippingType]['cost'],
        ];

        $this->redirect(APP_URL . '/checkout/payment');
    }

    // GET /checkout/payment
    public function payment(array $params): void
    {
        $this->requireAuth();

        if (empty($_SESSION['cart']['items']) || empty($_SESSION['checkout'])) {
            $this->redirect(APP_URL . '/cart');
        }

        $cartItems    = $_SESSION['cart']['items'];
        $cartTotal    = $this->calcCartTotal($cartItems);
        $shippingCost = (float) ($_SESSION['checkout']['shipping_cost'] ?? 0);
        $total        = round($cartTotal + $shippingCost, 2);
        $shipping     = self::SHIPPING_OPTIONS[$_SESSION['checkout']['shipping_type'] ?? 'standard'];

        $this->view('checkout.payment', [
            'pageTitle'    => 'Método de pago — PrimeLux SmartShop',
            'cartItems'    => $cartItems,
            'cartTotal'    => $cartTotal,
            'shippingCost' => $shippingCost,
            'total'        => $total,
            'shipping'     => $shipping,
            'csrfToken'    => $this->csrfToken(),
            'error'        => $_SESSION['checkout_error'] ?? '',
        ]);

        unset($_SESSION['checkout_error']);
    }

    // POST /checkout/payment — inicia sesión de Stripe
    public function initiatePayment(array $params): void
    {
        $this->requireAuth();
        $this->validateCsrf();

        if (empty($_SESSION['cart']['items']) || empty($_SESSION['checkout'])) {
            $this->redirect(APP_URL . '/cart');
        }

        $stripeLib = ROOT_PATH . '/public/vendor/stripe/init.php';
        if (!file_exists($stripeLib)) {
            $_SESSION['checkout_error'] = 'Error al procesar el pago. Inténtalo de nuevo.';
            error_log('[CheckoutController] Stripe library not found at: ' . $stripeLib);
            $this->redirect(APP_URL . '/checkout/payment');
        }

        require_once $stripeLib;

        $cartItems    = $_SESSION['cart']['items'];
        $shippingCost = (float) ($_SESSION['checkout']['shipping_cost'] ?? 0);
        $shipping     = self::SHIPPING_OPTIONS[$_SESSION['checkout']['shipping_type'] ?? 'standard'];

        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

            // Construye los line_items de Stripe
            $lineItems = [];
            foreach ($cartItems as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'eur',
                        'unit_amount'  => (int) round($item['price'] * 100), // en céntimos
                        'product_data' => [
                            'name' => $item['name'],
                        ],
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            // Añade envío como línea si tiene coste
            if ($shippingCost > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'eur',
                        'unit_amount'  => (int) round($shippingCost * 100),
                        'product_data' => [
                            'name' => $shipping['label'],
                        ],
                    ],
                    'quantity' => 1,
                ];
            }

            $user = (new UserModel())->findById((int) $_SESSION['user_id']);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $user['email'] ?? '',
                'success_url'          => APP_URL . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => APP_URL . '/checkout/cancel',
                'locale'               => 'es',
            ]);

            // Guarda el ID de sesión de Stripe para verificarlo en success
            $_SESSION['checkout']['stripe_session_id'] = $session->id;

            header('Location: ' . $session->url);
            exit;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            error_log('[CheckoutController] Stripe error: ' . $e->getMessage());
            $_SESSION['checkout_error'] = 'Error al conectar con la pasarela de pago. Inténtalo de nuevo.';
            $this->redirect(APP_URL . '/checkout/payment');
        }
    }

    // GET /checkout/success
    public function success(array $params): void
    {
        $this->requireAuth();

        $sessionId = $_GET['session_id'] ?? '';
        if (!$sessionId || empty($_SESSION['checkout'])) {
            $this->redirect(APP_URL . '/cart');
        }

        $stripeLib = ROOT_PATH . '/public/vendor/stripe/init.php';
        if (!file_exists($stripeLib)) {
            $this->redirect(APP_URL . '/');
        }

        require_once $stripeLib;

        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            $stripeSession = \Stripe\Checkout\Session::retrieve($sessionId);

            // Verifica que el pago fue completado
            if ($stripeSession->payment_status !== 'paid') {
                $_SESSION['cart_error'] = 'El pago no se completó. Inténtalo de nuevo.';
                $this->redirect(APP_URL . '/cart');
            }

            $cartItems = $_SESSION['cart']['items'];
            $cartTotal = $this->calcCartTotal($cartItems);
            $total     = round($cartTotal + (float) ($_SESSION['checkout']['shipping_cost'] ?? 0), 2);
            $userId    = (int) $_SESSION['user_id'];

            $orderModel = new OrderModel();

            // Crea el pedido en BD
            $orderId = $orderModel->createFromCheckout(
                $_SESSION['checkout'],
                $cartItems,
                $total,
                $sessionId,
                $userId
            );

            // Guarda la dirección por defecto del usuario
            $orderModel->saveAddress($userId, $_SESSION['checkout']);

            // Recupera el pedido completo para el email y la vista
            $order = $orderModel->findById($orderId);

            // Envía email de confirmación
            $this->sendConfirmationEmail($order);

            // Limpia sesión
            unset($_SESSION['cart'], $_SESSION['checkout']);

            $this->view('checkout.success', [
                'pageTitle' => 'Pedido confirmado — PrimeLux SmartShop',
                'order'     => $order,
            ]);

        } catch (\Throwable $e) {
            error_log('[CheckoutController] Success error: ' . $e->getMessage());
            $this->redirect(APP_URL . '/');
        }
    }

    // GET /checkout/cancel
    public function cancel(array $params): void
    {
        $this->requireAuth();

        $this->view('checkout.cancel', [
            'pageTitle' => 'Pago cancelado — PrimeLux SmartShop',
        ]);
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    private function calcCartTotal(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return round($total, 2);
    }

    /**
     * Envía email de confirmación de pedido.
     * Reutiliza MailService igual que el email de 2FA.
     * Sale desde no-reply@primeluxshop.es
     */
    private function sendConfirmationEmail(array $order): void
    {
        try {
            require_once APP_PATH . '/Services/MailService.php';

            $mailer  = new MailService();
            $subject = 'Pedido confirmado #' . $order['id'] . ' — PrimeLux SmartShop';
            $html    = $this->buildConfirmationEmailHtml($order);

            $mailer->send(
                $order['user_email'],
                $order['user_name'],
                $subject,
                $html
            );
        } catch (\Throwable $e) {
            // El email no interrumpe el flujo si falla
            error_log('[CheckoutController] Email confirmación fallido: ' . $e->getMessage());
        }
    }

    /**
     * Plantilla HTML del email de confirmación.
     * Misma estructura dark que el email de 2FA — reutiliza el estilo.
     */
    private function buildConfirmationEmailHtml(array $order): string
    {
        $appUrl      = defined('APP_URL') ? APP_URL : '';
        $orderId     = (int) $order['id'];
        $name        = htmlspecialchars($order['user_name'] ?? '');
        $total       = number_format((float) $order['total'], 2, ',', '.');
        $shippingLabels = ['standard' => 'Envío estándar', 'express' => 'Envío express', 'pickup_point' => 'Recogida en tienda'];
        $shipping       = htmlspecialchars($shippingLabels[$order['shipping_type'] ?? 'standard'] ?? ucfirst($order['shipping_type'] ?? 'standard'));
        $street      = htmlspecialchars($order['street'] ?? '');
        $city        = htmlspecialchars($order['city']   ?? '');
        $postalCode  = htmlspecialchars($order['postal_code'] ?? '');

        // Líneas de los productos
        $itemsHtml = '';
        foreach ($order['items'] ?? [] as $item) {
            $itemName     = htmlspecialchars($item['product_name_snapshot']);
            $itemQty      = (int) $item['quantity'];
            $itemPrice    = number_format((float) $item['unit_price'], 2, ',', '.');
            $itemSubtotal = number_format((float) $item['subtotal'], 2, ',', '.');
            $itemsHtml   .= "
                <tr>
                    <td style='padding:8px 0;color:#9CA3AF;font-size:13px;border-bottom:1px solid #374151;'>
                        {$itemName}
                    </td>
                    <td style='padding:8px 0;color:#9CA3AF;font-size:13px;border-bottom:1px solid #374151;text-align:center;'>
                        x{$itemQty}
                    </td>
                    <td style='padding:8px 0;color:#F59E0B;font-size:13px;border-bottom:1px solid #374151;text-align:right;font-weight:600;'>
                        {$itemSubtotal} €
                    </td>
                </tr>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#0D1B2A;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr><td align="center" style="padding:40px 20px;">
      <table width="520" cellpadding="0" cellspacing="0" border="0"
             style="background:#1A2535;border-radius:16px;overflow:hidden;max-width:520px;">

        <!-- Cabecera -->
        <tr><td style="background:#2563EB;padding:24px 32px;text-align:center;">
          <span style="color:#FFFFFF;font-size:20px;font-weight:700;">PrimeLux SmartShop</span>
        </td></tr>

        <!-- Confirmación -->
        <tr><td style="padding:32px 32px 24px;">
          <p style="color:#10B981;font-size:16px;font-weight:600;margin:0 0 4px;">
            ✓ Pedido confirmado
          </p>
          <p style="color:#9CA3AF;font-size:14px;margin:0 0 24px;">
            Hola, <strong style="color:#FFFFFF;">{$name}</strong> — tu pedido
            <strong style="color:#FFFFFF;">#</strong><strong style="color:#EAB308;">#{$orderId}</strong>
            ha sido procesado correctamente.
          </p>

          <!-- Productos -->
          <table width="100%" cellpadding="0" cellspacing="0" border="0"
                 style="margin-bottom:24px;">
            <thead>
              <tr>
                <th style="color:#6B7280;font-size:11px;text-align:left;padding-bottom:8px;
                           text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #374151;">
                  Producto
                </th>
                <th style="color:#6B7280;font-size:11px;text-align:center;padding-bottom:8px;
                           text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #374151;">
                  Cant.
                </th>
                <th style="color:#6B7280;font-size:11px;text-align:right;padding-bottom:8px;
                           text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #374151;">
                  Subtotal
                </th>
              </tr>
            </thead>
            <tbody>{$itemsHtml}</tbody>
          </table>

          <!-- Total -->
          <div style="background:#111C2E;border-radius:10px;padding:16px 20px;margin-bottom:24px;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="color:#9CA3AF;font-size:13px;">Método de envío</td>
                <td style="color:#FFFFFF;font-size:13px;text-align:right;">{$shipping}</td>
              </tr>
              <tr>
                <td style="color:#9CA3AF;font-size:13px;padding-top:8px;">Dirección</td>
                <td style="color:#FFFFFF;font-size:13px;text-align:right;padding-top:8px;">
                  {$street}, {$postalCode} {$city}
                </td>
              </tr>
              <tr>
                <td style="color:#FFFFFF;font-size:16px;font-weight:700;padding-top:16px;
                           border-top:1px solid #374151;">Total pagado</td>
                <td style="color:#EAB308;font-size:18px;font-weight:700;text-align:right;
                           padding-top:16px;border-top:1px solid #374151;">
                  {$total} €
                </td>
              </tr>
            </table>
          </div>

          <!-- CTA -->
          <div style="text-align:center;margin-bottom:16px;">
            <a href="{$appUrl}" style="display:inline-block;background:#2563EB;color:#FFFFFF;
               text-decoration:none;padding:12px 28px;border-radius:10px;
               font-weight:600;font-size:14px;">
              Ir a la tienda
            </a>
          </div>

        </td></tr>

        <!-- Footer -->
        <tr><td style="padding:16px 32px;text-align:center;border-top:1px solid #374151;">
          <p style="color:#4B5563;font-size:12px;margin:0;">
            © 2026 PrimeLux SmartShop &nbsp;·&nbsp;
            <a href="{$appUrl}" style="color:#2563EB;text-decoration:none;">primeluxshop.es</a>
          </p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
    }
}
