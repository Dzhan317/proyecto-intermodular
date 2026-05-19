<?php
/*
 * Detalle de producto.
 * Galería de imágenes con miniaturas laterales.
 * Productos relacionados inteligentes (motor de recomendaciones).
 */
ob_start();

$price    = number_format((float) $product['base_price'], 2, ',', '.') . ' €';
$stock    = (int) ($variant['stock'] ?? 0);
$hasStock = $stock > 0;
$docRoot  = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');

// Separa imagen principal y galería
$mainImage     = null;
$galleryImages = [];

if (!empty($images)) {
    foreach ($images as $img) {
        if ($img['is_main']) {
            $mainImage = $img;
        } elseif (file_exists($docRoot . $img['image_url'])) {
            $galleryImages[] = $img;
        }
    }
}

if (!$mainImage) {
    $mainImage = ['image_url' => $product['image_url'] ?? null, 'is_main' => 1];
}

$hasMainImage = $mainImage['image_url'] && file_exists($docRoot . $mainImage['image_url']);
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

    <!-- Galería -->
    <div class="flex gap-3">

        <?php if (!empty($galleryImages)): ?>
            <!-- Miniaturas laterales -->
            <div class="flex flex-col gap-2 flex-shrink-0">
                <button type="button"
                        onclick="switchImage('<?= APP_URL . htmlspecialchars($mainImage['image_url'] ?? '') ?>', this)"
                        class="gallery-thumb active-thumb w-16 h-16 rounded-xl border-2 border-[var(--color-brand)]
                               bg-[var(--color-bg-card)] overflow-hidden flex items-center justify-center
                               transition-all hover:border-[var(--color-brand)]">
                    <?php if ($hasMainImage): ?>
                        <img src="<?= APP_URL . htmlspecialchars($mainImage['image_url']) ?>"
                             alt="Vista principal" class="w-full h-full object-contain p-1">
                    <?php endif; ?>
                </button>
                <?php foreach ($galleryImages as $img): ?>
                    <button type="button"
                            onclick="switchImage('<?= APP_URL . htmlspecialchars($img['image_url']) ?>', this)"
                            class="gallery-thumb w-16 h-16 rounded-xl border-2 border-[var(--color-border)]
                                   bg-[var(--color-bg-card)] overflow-hidden flex items-center justify-center
                                   transition-all hover:border-[var(--color-brand)]">
                        <img src="<?= APP_URL . htmlspecialchars($img['image_url']) ?>"
                             alt="Vista adicional" class="w-full h-full object-contain p-1">
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Imagen principal -->
        <div class="flex-1 bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)]
                    overflow-hidden aspect-square flex items-center justify-center p-8">
            <?php if ($hasMainImage): ?>
                <img id="mainProductImage"
                     src="<?= APP_URL . htmlspecialchars($mainImage['image_url']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="w-full h-full object-contain transition-opacity duration-200">
            <?php else: ?>
                <div class="flex flex-col items-center gap-3 text-[var(--color-border)]">
                    <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586
                                 a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2
                                 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Imagen no disponible</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info y acciones -->
    <div class="flex flex-col">

        <?php if (!empty($product['category_name'])): ?>
            <span class="text-[var(--color-brand)] text-xs font-semibold uppercase tracking-wider mb-2">
                <?= htmlspecialchars($product['category_name']) ?>
            </span>
        <?php endif; ?>

        <?php if (!empty($product['brand'])): ?>
            <span class="text-[var(--color-text-muted)] text-xs mb-1">
                <?= htmlspecialchars($product['brand']) ?>
            </span>
        <?php endif; ?>

        <h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-3 leading-snug">
            <?= htmlspecialchars($product['name']) ?>
        </h1>

        <div class="text-3xl font-bold text-[var(--color-warning)] mb-4"><?= $price ?></div>

        <!-- Badge de stock -->
        <div class="mb-5">
            <?php if (!$hasStock): ?>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--color-error)]">
                    <span class="w-2 h-2 rounded-full bg-[var(--color-error)] flex-shrink-0"></span>
                    Sin stock
                </span>
            <?php elseif ($stock === 1): ?>
                <span class="inline-flex items-center gap-2 rounded-md border border-[var(--color-error)]
                             bg-[var(--color-error-bg)] px-3 py-1.5 text-sm font-semibold text-[var(--color-error)]">
                    ¡Última unidad!
                </span>
            <?php elseif ($stock <= 3): ?>
                <span class="inline-flex items-center gap-2 rounded-md border border-[var(--color-warning)]
                             bg-[var(--color-bg-secondary)] px-3 py-1.5 text-sm font-semibold text-[var(--color-warning)]">
                    Solo quedan <?= $stock ?> unidades
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--color-success)]">
                    <span class="w-2 h-2 rounded-full bg-[var(--color-success)] flex-shrink-0"></span>
                    En stock
                </span>
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
                <div class="flex items-center gap-3">
                    <label class="text-sm text-[var(--color-text-secondary)]">Cantidad:</label>
                    <div class="flex items-center gap-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                                rounded-xl overflow-hidden">
                        <button type="button" id="qtyMinus"
                                class="w-10 h-10 flex items-center justify-center
                                       text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                       hover:bg-[var(--color-bg-hover)] transition-colors text-lg font-bold">−</button>
                        <span id="qtyValue"
                              class="w-10 text-center text-[var(--color-text-primary)] font-semibold text-sm">1</span>
                        <button type="button" id="qtyPlus"
                                class="w-10 h-10 flex items-center justify-center
                                       text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                       hover:bg-[var(--color-bg-hover)] transition-colors text-lg font-bold">+</button>
                    </div>
                </div>

                <form id="addToCartForm" method="POST" action="<?= APP_URL ?>/cart/add">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="variant_id" value="<?= (int) ($variant['id'] ?? 0) ?>">
                    <input type="hidden" name="slug"       value="<?= htmlspecialchars($product['slug']) ?>">
                    <input type="hidden" name="quantity"   id="cartQuantity" value="1">
                    <button type="submit"
                            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                                   text-white font-semibold py-3 rounded-xl text-sm transition-colors duration-200">
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

<!-- Productos relacionados inteligentes -->
<?php if (!empty($related)): ?>
    <section class="mb-12">
        <div class="mb-5">
            <h2 class="text-xl font-bold text-[var(--color-text-primary)]">
                También te puede interesar
            </h2>
        </div>
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

    var cartQtyInput = document.getElementById('cartQuantity');
    var qtyMinus     = document.getElementById('qtyMinus');
    var qtyPlus      = document.getElementById('qtyPlus');
    if (cartQtyInput && qtyMinus && qtyPlus) {
        qtyMinus.addEventListener('click', function () {
            cartQtyInput.value = document.getElementById('qtyValue').textContent.trim();
        });
        qtyPlus.addEventListener('click', function () {
            cartQtyInput.value = document.getElementById('qtyValue').textContent.trim();
        });
    }
});

// Galería — cambia imagen principal al hacer clic en miniatura
function switchImage(url, btn) {
    var main = document.getElementById('mainProductImage');
    if (!main) return;

    main.style.opacity = '0';
    setTimeout(function () {
        main.src = url;
        main.style.opacity = '1';
    }, 150);

    // Resalta la miniatura activa
    document.querySelectorAll('.gallery-thumb').forEach(function (el) {
        el.classList.remove('border-[var(--color-brand)]');
        el.classList.add('border-[var(--color-border)]');
    });
    if (btn) {
        btn.classList.remove('border-[var(--color-border)]');
        btn.classList.add('border-[var(--color-brand)]');
    }
}
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
