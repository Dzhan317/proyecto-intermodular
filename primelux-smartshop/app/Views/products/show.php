<?php
/*
 * Detalle de producto.
 * Imagen, precio, stock, botón añadir al carrito (activo en Fase 5)
 * y productos relacionados.
 */
ob_start();

$price    = number_format((float) $product['base_price'], 2, ',', '.') . ' €';
$stock    = (int) ($variant['stock'] ?? 0);
$hasStock = $stock > 0;
$imageUrl = $product['image_url'] ?? null;
$docRoot  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$hasImage = $imageUrl && file_exists($docRoot . $imageUrl);
?>

<!-- Breadcrumb -->
<nav class="text-xs text-[var(--color-text-muted)] mb-6">
    <a href="<?= APP_URL ?>/" class="hover:text-[var(--color-text-primary)] transition-colors">Inicio</a>
    <?php if (!empty($product['category_slug'])): ?>
        <span class="mx-1">›</span>
        <a href="<?= APP_URL ?>/category/<?= htmlspecialchars($product['category_slug']) ?>"
           class="hover:text-[var(--color-text-primary)] transition-colors">
            <?= htmlspecialchars($product['category_name'] ?? '') ?>
        </a>
    <?php endif; ?>
    <span class="mx-1">›</span>
    <span class="text-[var(--color-text-secondary)]"><?= htmlspecialchars($product['name']) ?></span>
</nav>

<!-- Producto principal -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">

    <!-- Imagen -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden
                aspect-square flex items-center justify-center p-8">
        <?php if ($hasImage): ?>
            <img src="<?= APP_URL . htmlspecialchars($imageUrl) ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 class="w-full h-full object-contain">
        <?php else: ?>
            <div class="flex flex-col items-center gap-3 text-[var(--color-border)]">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2
                             0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                             00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm">Imagen no disponible</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info y acciones -->
    <div class="flex flex-col">

        <?php if (!empty($product['category_name'])): ?>
            <span class="text-[var(--color-brand)] text-xs font-semibold uppercase tracking-wider mb-2">
                <?= htmlspecialchars($product['category_name']) ?>
            </span>
        <?php endif; ?>

        <h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-3 leading-snug">
            <?= htmlspecialchars($product['name']) ?>
        </h1>

        <div class="text-3xl font-bold text-[var(--color-warning)] mb-4"><?= $price ?></div>

        <!-- Stock -->
        <div class="flex items-center gap-2 mb-5">
            <?php if ($hasStock): ?>
                <span class="w-2 h-2 rounded-full bg-[var(--color-success)] flex-shrink-0"></span>
                <span class="text-[var(--color-success)] text-sm font-medium">
                    <?= $stock <= 5 ? "Solo quedan {$stock} unidades" : 'En stock' ?>
                </span>
            <?php else: ?>
                <span class="w-2 h-2 rounded-full bg-[var(--color-error)] flex-shrink-0"></span>
                <span class="text-[var(--color-error)] text-sm font-medium">Sin stock</span>
            <?php endif; ?>
        </div>

        <!-- Descripción -->
        <?php if (!empty($product['description'])): ?>
            <p class="text-[var(--color-text-secondary)] text-sm leading-relaxed mb-6">
                <?= htmlspecialchars($product['description']) ?>
            </p>
        <?php endif; ?>

        <!-- Acciones -->
        <?php if ($hasStock): ?>
            <div class="space-y-3">
                <!-- Selector de cantidad -->
                <div class="flex items-center gap-3">
                    <label class="text-sm text-[var(--color-text-secondary)]">Cantidad:</label>
                    <div class="flex items-center gap-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                                rounded-xl overflow-hidden">
                        <button type="button" id="qtyMinus"
                                class="w-10 h-10 flex items-center justify-center
                                       text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                       hover:bg-[var(--color-bg-hover)] transition-colors text-lg font-bold">
                            −
                        </button>
                        <span id="qtyValue"
                              class="w-10 text-center text-[var(--color-text-primary)] font-semibold text-sm">
                            1
                        </span>
                        <button type="button" id="qtyPlus"
                                class="w-10 h-10 flex items-center justify-center
                                       text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                       hover:bg-[var(--color-bg-hover)] transition-colors text-lg font-bold">
                            +
                        </button>
                    </div>
                </div>

                <!-- Formulario añadir al carrito -->
                <form id="addToCartForm" method="POST" action="<?= APP_URL ?>/cart/add">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="variant_id" value="<?= (int) ($variant['id'] ?? 0) ?>">
                    <input type="hidden" name="slug"       value="<?= htmlspecialchars($product['slug']) ?>">
                    <input type="hidden" name="quantity"   id="cartQuantity" value="1">

                    <button type="submit"
                            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                                   text-white font-semibold py-3 rounded-xl text-sm
                                   transition-colors duration-200">
                        Añadir al carrito
                    </button>
                </form>
            </div>
        <?php else: ?>
            <button disabled
                    class="w-full bg-[var(--color-bg-hover)] text-[var(--color-text-muted)] font-semibold py-3
                           rounded-xl text-sm cursor-not-allowed">
                Sin stock
            </button>
        <?php endif; ?>

    </div>
</div>

<!-- Productos relacionados -->
<?php if (!empty($related)): ?>
    <section>
        <h2 class="text-xl font-bold text-[var(--color-text-primary)] mb-5">Productos relacionados</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($related as $product): ?>
                <?php require APP_PATH . '/Views/products/partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initQuantitySelector('qtyMinus', 'qtyPlus', 'qtyValue', <?= $stock ?>);

    // Sincroniza el selector de cantidad con el campo oculto del formulario
    var qtyValue   = document.getElementById('qtyValue');
    var cartQtyInput = document.getElementById('cartQuantity');
    if (qtyValue && cartQtyInput) {
        var observer = new MutationObserver(function () {
            cartQtyInput.value = qtyValue.textContent.trim();
        });
        observer.observe(qtyValue, { childList: true });
    }
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
