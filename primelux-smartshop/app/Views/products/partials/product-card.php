<?php
/*
 * Tarjeta de producto reutilizable.
 * Requiere $product en scope con: name, slug, base_price, image_url, stock.
 */
$imageUrl   = $product['image_url'] ?? null;
$price      = number_format((float) $product['base_price'], 2, ',', '.') . ' €';
$name       = htmlspecialchars($product['name']);
$slug       = htmlspecialchars($product['slug']);
$productUrl = APP_URL . '/products/' . $slug;
$stock      = (int) ($product['stock'] ?? 1);
$hasStock   = $stock > 0;
$docRoot    = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
$hasImage   = $imageUrl && file_exists($docRoot . $imageUrl);
?>
<a href="<?= $productUrl ?>"
   class="group relative bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden
          flex flex-col hover:border-[var(--color-brand)] hover:shadow-lg hover:shadow-black/20
          transition-all duration-200">

    <!-- Badge de stock -->
    <?php if (!$hasStock): ?>
        <span class="absolute top-3 left-3 z-10 inline-flex items-center rounded-md
                     border border-[var(--color-error)] bg-[var(--color-error)]
                     px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide
                     text-white shadow-md shadow-black/25">
            Sin stock
        </span>
    <?php elseif ($stock === 1): ?>
        <span class="absolute top-3 left-3 z-10 inline-flex items-center rounded-md
                     border border-[var(--color-error)] bg-[var(--color-error-bg)]
                     px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide
                     text-[var(--color-error)] shadow-md shadow-black/25">
            ¡Última unidad!
        </span>
    <?php elseif ($stock <= 3): ?>
        <span class="absolute top-3 left-3 z-10 inline-flex items-center rounded-md
                     border border-[var(--color-warning)] bg-[var(--color-bg-secondary)]
                     px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide
                     text-[var(--color-warning)] shadow-md shadow-black/25">
            Quedan <?= $stock ?>
        </span>
    <?php endif; ?>

    <!-- Imagen -->
    <div class="relative aspect-square bg-[var(--color-bg-secondary)] overflow-hidden">
        <?php if ($hasImage): ?>
            <img src="<?= htmlspecialchars(APP_URL . $imageUrl) ?>"
                 alt="<?= $name ?>"
                 loading="lazy"
                 class="w-full h-full object-contain p-4
                        group-hover:scale-105 transition-transform duration-300
                        <?= !$hasStock ? 'opacity-50' : '' ?>">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center
                        <?= !$hasStock ? 'opacity-40' : '' ?>">
                <svg class="w-16 h-16 text-[var(--color-border)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2
                             0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                             00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="p-4 flex flex-col flex-1">
        <h3 class="text-[var(--color-text-primary)] text-sm font-medium leading-snug mb-2 line-clamp-2
                   group-hover:text-[var(--color-link)] transition-colors flex-1">
            <?= $name ?>
        </h3>
        <div class="flex items-center justify-between mt-2">
            <span class="text-[var(--color-warning)] font-bold text-base"><?= $price ?></span>
            <span class="text-xs text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] px-2 py-1 rounded-lg
                         group-hover:text-[var(--color-text-primary)] transition-colors">
                Ver más
            </span>
        </div>
    </div>
</a>
