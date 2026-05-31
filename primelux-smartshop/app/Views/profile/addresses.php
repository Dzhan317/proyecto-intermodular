<?php
/*
 * Perfil — Pestaña de dirección guardada.
 * Vista de solo lectura. La dirección se guarda/actualiza automáticamente
 * al completar un pedido en checkout.
 */
ob_start();
?>

<div class="flex flex-col md:flex-row gap-6">

    <?php require APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 min-w-0 space-y-4">

        <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">
            Dirección de envío
        </h2>

        <?php if (!empty($address)): ?>
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">

                <div class="flex items-start justify-between gap-4 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex w-2 h-2 rounded-full bg-[var(--color-success)]"></span>
                        <span class="text-xs text-[var(--color-success)] font-medium">
                            Dirección por defecto
                        </span>
                    </div>
                    <span class="text-xs text-[var(--color-text-muted)]">
                        Se precarga automáticamente en el checkout
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            Dirección
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['street'] ?? '') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            Código postal
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['postal_code'] ?? '') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            Ciudad
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['city'] ?? '') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            Provincia
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['province'] ?? '') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            País
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['country'] ?? 'España') ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">
                            Teléfono
                        </p>
                        <p class="text-sm text-[var(--color-text-primary)]">
                            <?= htmlspecialchars($address['phone'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-[var(--color-border)]">
                    <p class="text-xs text-[var(--color-text-muted)]">
                        Esta dirección se actualiza automáticamente cada vez que completas un pedido.
                    </p>
                </div>
            </div>

        <?php else: ?>

            <!-- Estado vacío -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)]
                        p-12 text-center">
                <svg class="w-12 h-12 text-[var(--color-border)] mx-auto mb-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0
                             1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-1">
                    Sin dirección guardada
                </h3>
                <p class="text-xs text-[var(--color-text-secondary)] mb-5">
                    Cuando completes tu primer pedido, tu dirección aparecerá aquí
                    y se precargará automáticamente en futuros checkouts.
                </p>
                <a href="<?= APP_URL ?>/"
                   class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                          text-white font-semibold px-5 py-2.5 rounded-xl text-xs
                          transition-colors">
                    Explorar tienda
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
