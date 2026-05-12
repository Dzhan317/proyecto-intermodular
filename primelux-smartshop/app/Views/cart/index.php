<?php
/*
 * Vista principal del carrito.
 * Diseño basado en docs/designs/cart/carrito_vista_general.png.
 * — Tarjeta única "CARRITO" con todos los ítems
 * — Resumen lateral sticky con IVA desglosado
 * — Scroll horizontal de productos relacionados
 */
ob_start();

$productCount = $totals['product_count'] ?? 0;
$itemCount    = $totals['item_count']    ?? 0;
?>

<h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-6">
    Carrito
    <?php if ($productCount > 0): ?>
        <span class="text-base font-normal text-[var(--color-text-muted)] ml-2">
            (<?= $productCount ?> <?= $productCount === 1 ? 'producto' : 'productos' ?>)
        </span>
    <?php endif; ?>
</h1>

<?php if (!empty($success)): ?>
    <div class="mb-4 p-3 alert-success rounded-xl text-sm">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="mb-4 p-3 alert-error rounded-xl text-sm">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (empty($items)): ?>

    <?php require APP_PATH . '/Views/cart/empty.php'; ?>

<?php else: ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- ── Tarjeta CARRITO ────────────────────────────────────────── -->
        <div class="lg:col-span-2">
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

                <!-- Cabecera tarjeta -->
                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h2 class="text-sm font-semibold text-[var(--color-text-primary)] uppercase tracking-wider">
                        Carrito
                    </h2>
                </div>

                <!-- Ítems -->
                <?php foreach ($items as $key => $item):
                    $docRoot  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
                    $imgUrl   = $item['image_url'] ?? null;
                    $hasImage = $imgUrl && file_exists($docRoot . $imgUrl);
                    $lineTotal = number_format($item['price'] * $item['quantity'], 2, ',', '.');
                ?>
                    <div class="flex gap-4 px-4 py-4 border-b border-[var(--color-border)] last:border-b-0
                                hover:bg-[var(--color-bg-hover)]/30 transition-colors">

                        <!-- Imagen -->
                        <a href="<?= APP_URL ?>/products/<?= htmlspecialchars($item['slug']) ?>"
                           class="flex-shrink-0 w-24 h-24 rounded-xl bg-[var(--color-bg-secondary)]
                                  border border-[var(--color-border)] flex items-center justify-center overflow-hidden">
                            <?php if ($hasImage): ?>
                                <img src="<?= APP_URL . htmlspecialchars($imgUrl) ?>"
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     loading="lazy"
                                     class="w-full h-full object-contain p-2">
                            <?php else: ?>
                                <svg class="w-8 h-8 text-[var(--color-border)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                             a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                                             00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            <?php endif; ?>
                        </a>

                        <!-- Contenido del ítem -->
                        <div class="flex-1 min-w-0">

                            <!-- Fila superior: nombre + papelera -->
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <a href="<?= APP_URL ?>/products/<?= htmlspecialchars($item['slug']) ?>"
                                   class="text-[var(--color-text-primary)] text-sm font-semibold
                                          hover:text-[var(--color-link)] transition-colors line-clamp-2">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>

                                <!-- Eliminar -->
                                <form method="POST" action="<?= APP_URL ?>/cart/remove" class="flex-shrink-0">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="variant_id" value="<?= htmlspecialchars($key) ?>">
                                    <button type="submit"
                                            title="Eliminar del carrito"
                                            class="p-1.5 rounded-lg text-[var(--color-text-muted)]
                                                   hover:text-[var(--color-error)] hover:bg-[var(--color-error-bg)]
                                                   transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                                                     01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                                                     00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <!-- Variante — mapea a variants.name -->
                            <?php if (!empty($item['variant_name']) && $item['variant_name'] !== 'Unidad'): ?>
                                <span class="inline-block text-xs text-[var(--color-text-muted)]
                                             bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                                             px-2 py-0.5 rounded-md mb-2">
                                    <?= htmlspecialchars($item['variant_name']) ?>
                                </span>
                            <?php endif; ?>

                            <!-- Precio unitario -->
                            <p class="text-[var(--color-text-secondary)] text-xs mb-3">
                                Precio unitario:
                                <span class="text-[var(--color-warning)] font-semibold">
                                    <?= number_format($item['price'], 2, ',', '.') ?> €
                                </span>
                            </p>

                            <!-- Fila inferior: cantidad + subtotal línea -->
                            <div class="flex items-center justify-between gap-3">

                                <!-- Selector de cantidad (formulario POST) -->
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-[var(--color-text-muted)] mr-2">Cantidad</span>
                                    <form method="POST" action="<?= APP_URL ?>/cart/update"
                                          class="flex items-center">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <input type="hidden" name="variant_id" value="<?= htmlspecialchars($key) ?>">
                                        <div class="flex items-center bg-[var(--color-bg-secondary)]
                                                    border border-[var(--color-border)] rounded-xl overflow-hidden">
                                            <button type="submit" name="quantity"
                                                    value="<?= max(1, $item['quantity'] - 1) ?>"
                                                    class="w-9 h-9 flex items-center justify-center text-lg font-bold
                                                           text-[var(--color-text-secondary)] transition-colors
                                                           hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                                           <?= $item['quantity'] <= 1 ? 'opacity-30 pointer-events-none' : '' ?>">
                                                −
                                            </button>
                                            <span class="w-9 text-center text-sm font-semibold
                                                         text-[var(--color-text-primary)]">
                                                <?= (int) $item['quantity'] ?>
                                            </span>
                                            <button type="submit" name="quantity"
                                                    value="<?= $item['quantity'] + 1 ?>"
                                                    class="w-9 h-9 flex items-center justify-center text-lg font-bold
                                                           text-[var(--color-text-secondary)] transition-colors
                                                           hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]">
                                                +
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Subtotal de línea -->
                                <span class="text-[var(--color-text-primary)] text-sm font-bold">
                                    <?= $lineTotal ?> €
                                </span>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- ── RESUMEN ──────────────────────────────────────────────── -->
        <div class="lg:sticky lg:top-24">
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h2 class="text-sm font-semibold text-[var(--color-text-primary)] uppercase tracking-wider">
                        Resumen
                    </h2>
                </div>

                <div class="px-6 py-5">
                    <dl class="space-y-3 mb-5">
                        <div class="flex justify-between text-sm">
                            <dt class="text-[var(--color-text-secondary)]">Subtotal productos</dt>
                            <dd class="text-[var(--color-text-primary)] font-medium">
                                <?= number_format($totals['total'], 2, ',', '.') ?> €
                            </dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-[var(--color-text-secondary)]">IVA</dt>
                            <dd class="text-[var(--color-text-primary)] font-medium">21%</dd>
                        </div>
                        <div class="flex justify-between text-sm">
                            <dt class="text-[var(--color-text-secondary)]">Entrega</dt>
                            <dd class="text-[var(--color-text-muted)] text-xs text-right">
                                Envío calculado en checkout
                            </dd>
                        </div>
                    </dl>

                    <div class="border-t border-[var(--color-border)] pt-4 mb-1">
                        <div class="flex justify-between items-baseline">
                            <span class="text-[var(--color-text-primary)] font-semibold text-sm">
                                Total estimado
                            </span>
                            <span class="text-[var(--color-warning)] font-bold text-xl">
                                <?= number_format($totals['total'], 2, ',', '.') ?> €
                            </span>
                        </div>
                        <p class="text-[var(--color-text-muted)] text-xs mt-1">
                            Entrega estimada: 2-5 días
                        </p>
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <a href="<?= APP_URL ?>/checkout/shipping"
                       class="block w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                              text-white font-semibold py-3 rounded-xl text-sm text-center
                              transition-colors">
                        Continuar
                    </a>
                    <a href="<?= APP_URL ?>/"
                       class="block w-full text-center text-[var(--color-text-muted)]
                              hover:text-[var(--color-text-primary)] text-xs mt-3 transition-colors">
                        ← Seguir comprando
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- ── Productos relacionados ─────────────────────────────────────── -->
    <?php if (!empty($related)): ?>
        <section class="mt-12">
            <h2 class="text-lg font-bold text-[var(--color-text-primary)] mb-5 uppercase tracking-wider">
                Productos relacionados
            </h2>
            <!--
                Scroll horizontal CSS — mismo efecto visual que un carrusel
                sin JavaScript adicional. snap-x para un deslizamiento preciso.
            -->
            <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-thin
                        snap-x snap-mandatory -mx-4 px-4">
                <?php foreach ($related as $product): ?>
                    <div class="flex-shrink-0 w-48 snap-start">
                        <?php require APP_PATH . '/Views/products/partials/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
