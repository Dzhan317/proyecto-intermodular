<?php
/*
 * Checkout — Pago cancelado.
 * Stripe redirige aquí si el usuario pulsa "Volver" desde la página de Stripe.
 * El carrito permanece intacto.
 */
ob_start();
$checkoutStep = 'payment';
?>

<div class="max-w-md mx-auto text-center py-12">
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-10">

        <div class="w-14 h-14 rounded-full bg-[var(--color-error-bg)] border border-[var(--color-error-border)]
                    flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-[var(--color-error)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>

        <h1 class="text-lg font-bold text-[var(--color-text-primary)] mb-2">
            Pago cancelado
        </h1>
        <p class="text-sm text-[var(--color-text-secondary)] mb-8">
            No se ha realizado ningún cargo. Tu carrito sigue disponible con los mismos productos.
        </p>

        <div class="flex flex-col gap-3">
            <a href="<?= APP_URL ?>/checkout/payment"
               class="block w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                      text-white font-semibold py-3 rounded-xl text-sm transition-colors">
                Volver al pago
            </a>
            <a href="<?= APP_URL ?>/cart"
               class="block w-full text-center text-sm text-[var(--color-text-muted)]
                      hover:text-[var(--color-text-primary)] transition-colors">
                Ver mi carrito
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/checkout.php';
