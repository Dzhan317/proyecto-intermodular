<?php
/*
 * Listado de productos por categoría.
 * Sidebar diferenciado visualmente del grid de productos.
 * Filtros: precio (slider doble), marca (checkboxes dinámicos) y stock.
 */
ob_start();

$categoryName = htmlspecialchars($category['name']);
$categorySlug = htmlspecialchars($category['slug']);
$baseUrl      = APP_URL . '/category/' . $categorySlug;

$sortOptions = [
    'newest'     => 'Más recientes',
    'price_asc'  => 'Precio: menor a mayor',
    'price_desc' => 'Precio: mayor a menor',
];

$rangeMin   = (int) floor($priceRange['min']);
$rangeMax   = (int) ceil($priceRange['max']);
$currentMin = (int) floor($minPrice);
$currentMax = (int) ceil($maxPrice);
?>

<div id="listingTop"></div>

<!-- Cabecera -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <nav class="text-xs text-[var(--color-text-muted)] mb-1">
            <a href="<?= APP_URL ?>/" class="hover:text-[var(--color-text-primary)] transition-colors">Inicio</a>
            <span class="mx-1">›</span>
            <span class="text-[var(--color-text-secondary)]"><?= $categoryName ?></span>
        </nav>
        <h1 class="text-2xl font-bold text-[var(--color-text-primary)]"><?= $categoryName ?></h1>
        <p class="text-[var(--color-text-muted)] text-sm mt-1">
            <?= $total ?> <?= $total === 1 ? 'producto' : 'productos' ?>
            <?php if ($hasActiveFilters): ?>
                <span class="text-[var(--color-warning)]">· Filtros activos</span>
            <?php endif; ?>
        </p>
    </div>

    <!-- Selector de orden -->
    <div class="flex items-center gap-2">
        <label for="sortSelect" class="text-sm text-[var(--color-text-secondary)] flex-shrink-0">Ordenar:</label>
        <select id="sortSelect"
                class="bg-[var(--color-bg-card)] text-[var(--color-text-primary)] border border-[var(--color-border)] rounded-xl
                       px-3 py-2 text-sm focus:outline-none focus:border-[var(--color-brand)]
                       transition-colors cursor-pointer">
            <?php foreach ($sortOptions as $value => $label): ?>
                <option value="<?= $value ?>" <?= $sort === $value ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Layout: sidebar + grid -->
<div class="flex flex-col lg:flex-row gap-6 items-start">

    <!-- ── Panel de filtros ── diferenciado visualmente del grid ─────── -->
    <aside class="w-full lg:w-60 flex-shrink-0 lg:sticky lg:top-24">
        <form id="filterForm" method="GET" action="<?= $baseUrl ?>">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <input type="hidden" name="page" value="1">

            <!--
                Fondo más oscuro que las tarjetas de producto (bg-[var(--color-bg-main)] vs bg-[var(--color-bg-card)])
                + borde izquierdo azul de acento para identificarlo como panel de control
            -->
            <div class="bg-[var(--color-bg-main)] rounded-2xl border border-[var(--color-divider)]
                        border-l-2 border-l-[var(--color-brand)] overflow-hidden">

                <!-- Cabecera del panel -->
                <div class="flex items-center justify-between px-5 py-4
                            border-b border-[var(--color-divider)]">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--color-brand)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0
                                     01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1
                                     1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        <span class="text-[var(--color-text-primary)] text-sm font-semibold">Filtros</span>
                    </div>
                    <?php if ($hasActiveFilters): ?>
                        <a href="<?= $baseUrl ?>?sort=<?= htmlspecialchars($sort) ?>"
                           class="text-xs text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
                            Limpiar
                        </a>
                    <?php endif; ?>
                </div>

                <div class="px-5 py-5 space-y-6">

                    <!-- ── Rango de precio ─────────────────────────────── -->
                    <div>
                        <p class="text-xs font-semibold text-[var(--color-text-muted)] uppercase
                                  tracking-wider mb-4">
                            Precio
                        </p>

                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[var(--color-warning)] text-sm font-bold"
                                  id="displayMin"><?= $currentMin ?> €</span>
                            <div class="h-px flex-1 mx-3 bg-[var(--color-bg-hover)]"></div>
                            <span class="text-[var(--color-warning)] text-sm font-bold"
                                  id="displayMax"><?= $currentMax ?> €</span>
                        </div>

                        <div class="relative h-6 flex items-center mx-1">
                            <div class="absolute w-full h-1.5 bg-[var(--color-bg-card)] rounded-full"></div>
                            <div id="sliderRange"
                                 class="absolute h-1.5 bg-[var(--color-brand)] rounded-full pointer-events-none"></div>
                            <input type="range" id="sliderMin" name="min_price"
                                   min="<?= $rangeMin ?>" max="<?= $rangeMax ?>"
                                   value="<?= $currentMin ?>"
                                   class="price-slider absolute w-full appearance-none
                                          bg-transparent pointer-events-none cursor-pointer">
                            <input type="range" id="sliderMax" name="max_price"
                                   min="<?= $rangeMin ?>" max="<?= $rangeMax ?>"
                                   value="<?= $currentMax ?>"
                                   class="price-slider absolute w-full appearance-none
                                          bg-transparent pointer-events-none cursor-pointer">
                        </div>

                        <div class="flex justify-between text-xs text-[var(--color-text-disabled)] mt-2">
                            <span><?= $rangeMin ?> €</span>
                            <span><?= $rangeMax ?> €</span>
                        </div>
                    </div>

                    <!-- ── Marca ───────────────────────────────────────── -->
                    <?php if (!empty($availableBrands)): ?>
                    <div>
                        <p class="text-xs font-semibold text-[var(--color-text-muted)] uppercase
                                  tracking-wider mb-3">
                            Marca
                        </p>
                        <div class="space-y-2 max-h-44 overflow-y-auto pr-1 scrollbar-thin">
                            <?php foreach ($availableBrands as $brand): ?>
                                <?php $checked = in_array($brand, $selectedBrands, true); ?>
                                <label class="flex items-center gap-2.5 cursor-pointer group">
                                    <input type="checkbox"
                                           name="brands[]"
                                           value="<?= htmlspecialchars($brand) ?>"
                                           <?= $checked ? 'checked' : '' ?>
                                           class="w-4 h-4 rounded border-[var(--color-border)]
                                                  bg-[var(--color-bg-card)] accent-[var(--color-brand)]
                                                  cursor-pointer">
                                    <span class="text-sm transition-colors
                                                 <?= $checked
                                                     ? 'text-[var(--color-text-primary)] font-medium'
                                                     : 'text-[var(--color-text-secondary)] group-hover:text-[var(--color-text-primary)]' ?>">
                                        <?= htmlspecialchars($brand) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- ── Disponibilidad ──────────────────────────────── -->
                    <div>
                        <p class="text-xs font-semibold text-[var(--color-text-muted)] uppercase
                                  tracking-wider mb-3">
                            Disponibilidad
                        </p>
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <div class="relative flex-shrink-0">
                                <input type="checkbox" name="in_stock" value="1"
                                       <?= $inStock ? 'checked' : '' ?>
                                       id="inStockCheck"
                                       class="sr-only peer">
                                <div class="w-9 h-5 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                                            rounded-full peer-checked:bg-[var(--color-brand)]
                                            peer-checked:border-[var(--color-brand)] transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white
                                            rounded-full shadow transition-transform
                                            peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm text-[var(--color-text-secondary)] group-hover:text-[var(--color-text-primary)]
                                         transition-colors peer-checked:text-[var(--color-text-primary)]">
                                Solo en stock
                            </span>
                        </label>
                    </div>

                </div>

                <!-- Botón aplicar -->
                <div class="px-5 pb-5">
                    <button type="submit"
                            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-[var(--color-text-primary)]
                                   font-semibold py-2.5 rounded-xl text-sm
                                   transition-colors duration-200">
                        Aplicar filtros
                    </button>
                </div>

            </div>
        </form>
    </aside>

    <!-- ── Grid de productos ─────────────────────────────────────────── -->
    <div class="flex-1 min-w-0">

        <?php if (empty($products)): ?>
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-16 text-center">
                <svg class="w-14 h-14 text-[var(--color-border)] mx-auto mb-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-[var(--color-text-primary)] font-semibold mb-2">No hay resultados</h3>
                <p class="text-[var(--color-text-muted)] text-sm mb-5">
                    Ningún producto coincide con los filtros aplicados.<br>
                    Prueba a ampliar el rango de precio o quita algún filtro.
                </p>
                <a href="<?= $baseUrl ?>"
                   class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-[var(--color-text-primary)]
                          font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
                    Ver todos los productos
                </a>
            </div>

        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
                <?php foreach ($products as $product): ?>
                    <?php require APP_PATH . '/Views/products/partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <?php if ($pages > 1): ?>
                <?php
                $paginationBase = $baseUrl . '?' . http_build_query(array_filter([
                    'sort'      => $sort,
                    'min_price' => $minPrice > $priceRange['min'] ? (int) $minPrice : null,
                    'max_price' => $maxPrice < $priceRange['max'] ? (int) $maxPrice : null,
                    'in_stock'  => $inStock ? '1' : null,
                    'brands'    => !empty($selectedBrands) ? $selectedBrands : null,
                ]));
                ?>
                <nav class="flex items-center justify-center gap-2">
                    <?php if ($page > 1): ?>
                        <a href="<?= $paginationBase ?>&page=<?= $page - 1 ?>"
                           class="pagination-link px-4 py-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                                  rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                  hover:border-[var(--color-brand)] text-sm transition-colors">
                            ← Anterior
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                        <a href="<?= $paginationBase ?>&page=<?= $i ?>"
                           class="pagination-link w-10 h-10 flex items-center justify-center
                                  rounded-xl text-sm border transition-colors
                                  <?= $i === $page
                                      ? 'bg-[var(--color-brand)] border-[var(--color-brand)] text-[var(--color-text-primary)] font-semibold'
                                      : 'bg-[var(--color-bg-card)] border-[var(--color-border)] text-[var(--color-text-secondary)]
                                         hover:text-[var(--color-text-primary)] hover:border-[var(--color-brand)]' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $pages): ?>
                        <a href="<?= $paginationBase ?>&page=<?= $page + 1 ?>"
                           class="pagination-link px-4 py-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                                  rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                  hover:border-[var(--color-brand)] text-sm transition-colors">
                            Siguiente →
                        </a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    initPriceSlider('sliderMin', 'sliderMax', 'sliderRange', 'displayMin', 'displayMax');

    document.getElementById('sortSelect').addEventListener('change', function () {
        var form = document.getElementById('filterForm');
        form.querySelector('[name="sort"]').value = this.value;
        form.submit();
    });

    document.querySelectorAll('.pagination-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var href   = this.href;
            var target = document.getElementById('listingTop');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(function () { window.location.href = href; }, 350);
            } else {
                window.location.href = href;
            }
        });
    });

});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
