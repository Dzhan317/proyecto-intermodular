<?php
/*
 * Resumen del carrito — partial reutilizable en todas las vistas del checkout.
 * Muestra productos, envío y total. Coherente con el mockup checkout_envio.png.
 * Requiere: $cartItems, $cartTotal, $shippingCost (opcional), $total (opcional)
 */
$shippingCost = $shippingCost ?? 0.0;
$total        = $total        ?? $cartTotal ?? 0.0;
$shipping     = $shipping     ?? null;
$docRoot      = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
?>
<div class="lg:sticky lg:top-20">
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

        <div class="flex items-center justify-between px-5 py-4 border-b border-[var(--color-border)]">
            <h3 class="text-sm font-semibold text-[var(--color-text-primary)] uppercase tracking-wider">
                Carrito
            </h3>
            <a href="<?= APP_URL ?>/cart"
               class="text-xs text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
                Editar
            </a>
        </div>

        <!-- Productos -->
        <div class="divide-y divide-[var(--color-border)]">
            <?php foreach ($cartItems as $item):
                $imgUrl   = $item['image_url'] ?? null;
                $hasImage = $imgUrl && file_exists($docRoot . $imgUrl);
            ?>
                <div class="flex items-center gap-3 px-5 py-3">
                    <!-- Imagen -->
                    <div class="w-12 h-12 rounded-lg bg-[var(--color-bg-secondary)]
                                border border-[var(--color-border)] flex-shrink-0
                                flex items-center justify-center overflow-hidden">
                        <?php if ($hasImage): ?>
                            <img src="<?= APP_URL . htmlspecialchars($imgUrl) ?>"
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 class="w-full h-full object-contain p-1">
                        <?php else: ?>
                            <svg class="w-5 h-5 text-[var(--color-border)]"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                         a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2
                                         0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        <?php endif; ?>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-[var(--color-text-primary)] font-medium line-clamp-1">
                            <?= htmlspecialchars($item['name']) ?>
                        </p>
                        <p class="text-xs text-[var(--color-text-muted)] mt-0.5">
                            <?= number_format($item['price'], 2, ',', '.') ?> € ×
                            <?= (int) $item['quantity'] ?>
                        </p>
                    </div>

                    <!-- Subtotal -->
                    <span class="text-xs font-semibold text-[var(--color-warning)] flex-shrink-0">
                        <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?> €
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Totales -->
        <div class="px-5 py-4 border-t border-[var(--color-border)] space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-[var(--color-text-secondary)]">Subtotal</span>
                <span class="text-[var(--color-text-primary)] font-medium">
                    <?= number_format($cartTotal ?? 0, 2, ',', '.') ?> €
                </span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-[var(--color-text-secondary)]">Envío</span>
                <span class="text-[var(--color-text-primary)] font-medium">
                    <?= $shippingCost > 0
                        ? number_format($shippingCost, 2, ',', '.') . ' €'
                        : ($shipping ? 'Gratis' : 'Pendiente') ?>
                </span>
            </div>
            <?php if ($shipping): ?>
                <div class="flex justify-between text-xs">
                    <span class="text-[var(--color-text-muted)]">Método</span>
                    <span class="text-[var(--color-text-muted)]">
                        <?= htmlspecialchars($shipping['label']) ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="flex justify-between items-baseline pt-2 border-t border-[var(--color-border)]">
                <span class="text-sm font-semibold text-[var(--color-text-primary)]">Total</span>
                <span class="text-lg font-bold text-[var(--color-warning)]">
                    <?= number_format($total, 2, ',', '.') ?> €
                </span>
            </div>
            <?php if ($shipping): ?>
                <p class="text-xs text-[var(--color-text-muted)] text-right">
                    Entrega estimada:
                    <?= htmlspecialchars($shipping['description']) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>
