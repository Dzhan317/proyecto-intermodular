<?php
/*
 * Checkout — Confirmación de pedido.
 * Diseño basado en docs/designs/checkout/checkout_confirmacion.png.
 * Stripe redirige aquí tras pago correcto.
 */
ob_start();
$checkoutStep = 'success';

$orderId     = (int) ($order['id'] ?? 0);
$total       = number_format((float) ($order['total'] ?? 0), 2, ',', '.');
$shippingType = match ($order['shipping_type'] ?? 'standard') {
    'express'      => 'Envío express (24-48h)',
    'pickup_point' => 'Recogida en tienda',
    default        => 'Envío estándar (3-5 días)',
};
?>

<div class="max-w-2xl mx-auto">

    <!-- Cabecera confirmación -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-8 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-[var(--color-text-primary)]">
                    Número de pedido: #<?= $orderId ?>
                </h1>
            </div>
            <div class="flex items-center gap-2 text-right">
                <svg class="w-6 h-6 text-[var(--color-success)] flex-shrink-0"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9
                             10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                          clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-success)]">
                        Pedido confirmado
                    </p>
                    <p class="text-xs text-[var(--color-text-muted)]">Gracias por tu compra</p>
                </div>
            </div>
        </div>

        <!-- Datos de contacto -->
        <div class="bg-[var(--color-bg-secondary)] rounded-xl p-5 mb-4">
            <h3 class="text-sm font-semibold text-[var(--color-text-secondary)]
                       uppercase tracking-wider mb-4">
                Datos de contacto
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] mb-1">Nombre</p>
                    <p class="text-sm text-[var(--color-text-primary)] font-medium">
                        <?= htmlspecialchars(($order['user_name'] ?? '') . ' ') ?>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] mb-1">Correo electrónico</p>
                    <p class="text-sm text-[var(--color-text-primary)] font-medium">
                        <?= htmlspecialchars($order['user_email'] ?? '') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Dirección y envío -->
        <div class="bg-[var(--color-bg-secondary)] rounded-xl p-5">
            <h3 class="text-sm font-semibold text-[var(--color-text-secondary)]
                       uppercase tracking-wider mb-4">
                Dirección y método de entrega
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] mb-1">Dirección de envío</p>
                    <p class="text-sm text-[var(--color-text-primary)]">
                        <?= htmlspecialchars($order['street'] ?? '') ?><br>
                        <?= htmlspecialchars(($order['postal_code'] ?? '') . ' ' . ($order['city'] ?? '')) ?><br>
                        <?= htmlspecialchars($order['province'] ?? '') ?>,
                        <?= htmlspecialchars($order['country'] ?? 'España') ?>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] mb-1">Método de entrega</p>
                    <p class="text-sm text-[var(--color-text-primary)]">
                        <?= htmlspecialchars($shippingType) ?>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] mb-1">Entrega estimada</p>
                    <p class="text-sm text-[var(--color-text-primary)]">
                        <?= match ($order['shipping_type'] ?? 'standard') {
                            'express'      => 'Mañana o pasado mañana',
                            'pickup_point' => 'Disponible en 24 horas',
                            default        => 'Entre 3 y 5 días hábiles',
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total pagado -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-[var(--color-text-secondary)]
                           uppercase tracking-wider mb-1">
                    Total pagado
                </p>
                <p class="text-xs text-[var(--color-text-muted)]">IVA incluido</p>
            </div>
            <span class="text-3xl font-bold text-[var(--color-warning)]">
                <?= $total ?> €
            </span>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center">
        <a href="<?= APP_URL ?>/"
           class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                  text-white font-semibold px-8 py-3 rounded-xl text-sm
                  transition-colors">
            Ver tienda
        </a>
        <p class="text-xs text-[var(--color-text-muted)] mt-4">
            Recibirás un email de confirmación en
            <strong class="text-[var(--color-text-secondary)]">
                <?= htmlspecialchars($order['user_email'] ?? '') ?>
            </strong>
        </p>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/checkout.php';
