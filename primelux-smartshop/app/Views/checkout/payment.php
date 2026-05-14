<?php
/*
 * Checkout — Paso 2: Método de pago.
 * Diseño basado en docs/designs/checkout/checkout_pago.png.
 * Solo Stripe (tarjeta) es funcional.
 * PayPal, Google Pay y Apple Pay aparecen deshabilitados con "Próximamente".
 */
ob_start();
$checkoutStep = 'payment';
?>

<?php if (!empty($error)): ?>
    <div class="mb-4 p-3 alert-error rounded-xl text-sm">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- ── Método de pago ──────────────────────────────────────────── -->
    <div class="lg:col-span-2">
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-5">
                Método de pago
            </h2>

            <!-- Stripe — activo -->
            <form method="POST" action="<?= APP_URL ?>/checkout/payment">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <label class="flex items-start gap-3 p-4 rounded-xl border border-[var(--color-brand)]
                              bg-[var(--color-brand)]/5 cursor-pointer mb-3">
                    <input type="radio" name="payment_method" value="stripe"
                           checked class="mt-0.5 accent-[var(--color-brand)]">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                Tarjeta (Stripe)
                            </span>
                            <span class="text-xs">🔒</span>
                            <span class="text-xs text-[var(--color-success)] font-medium">
                                Pago seguro
                            </span>
                        </div>
                        <p class="text-xs text-[var(--color-text-muted)] mt-1">
                            Tus datos serán procesados por Stripe de forma segura.
                            No almacenamos información de tarjeta.
                        </p>
                    </div>
                </label>

                <!-- Métodos deshabilitados — aparecen visualmente pero no funcionan -->
                <?php
                $disabledMethods = [
                    'PayPal'     => 'Serás redirigido a PayPal para completar el pago.',
                    'Google Pay' => 'Paga rápidamente con tu cuenta de Google.',
                    'Apple Pay'  => 'Paga de forma segura con Apple Pay.',
                ];
                ?>
                <?php foreach ($disabledMethods as $method => $desc): ?>
                    <div class="relative flex items-start gap-3 p-4 rounded-xl border
                                border-[var(--color-border)] mb-3 opacity-50 cursor-not-allowed
                                select-none">
                        <input type="radio" disabled
                               class="mt-0.5 cursor-not-allowed">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-[var(--color-text-secondary)]">
                                    <?= $method ?>
                                </span>
                                <span class="text-xs bg-[var(--color-bg-secondary)]
                                             text-[var(--color-text-disabled)]
                                             border border-[var(--color-border)]
                                             px-2 py-0.5 rounded-full">
                                    Próximamente
                                </span>
                            </div>
                            <p class="text-xs text-[var(--color-text-disabled)] mt-1">
                                <?= $desc ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Botón pagar -->
                <button type="submit"
                        class="w-full mt-2 bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                               text-white font-semibold py-3 rounded-xl text-sm
                               transition-colors uppercase tracking-wider">
                    Pagar ahora
                </button>

                <p class="text-center text-xs text-[var(--color-text-muted)] mt-3">
                    Al pagar aceptas los
                    <a href="#" class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]">
                        términos de uso
                    </a>
                    de PrimeLux SmartShop.
                </p>
            </form>

        </div>

        <!-- Volver al envío -->
        <a href="<?= APP_URL ?>/checkout/shipping"
           class="block text-center text-xs text-[var(--color-text-muted)]
                  hover:text-[var(--color-text-primary)] transition-colors mt-4">
            ← Volver a datos de envío
        </a>
    </div>

    <!-- ── Resumen del carrito ─────────────────────────────────────── -->
    <?php require APP_PATH . '/Views/checkout/partials/cart-summary.php'; ?>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/checkout.php';
