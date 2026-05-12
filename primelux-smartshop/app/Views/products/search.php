<?php
/*
 * Vista de resultados del buscador.
 * Muestra el grid de productos encontrados o el estado vacío
 * cuando ningún producto coincide con el término buscado.
 */
ob_start();

$baseUrl = APP_URL . '/products?' . http_build_query(['q' => $query]);
?>

<!-- Cabecera -->
<div class="mb-6">
    <nav class="text-xs text-[var(--color-text-muted)] mb-2">
        <a href="<?= APP_URL ?>/" class="hover:text-[var(--color-text-primary)] transition-colors">
            Inicio
        </a>
        <span class="mx-1">›</span>
        <span class="text-[var(--color-text-secondary)]">Búsqueda</span>
    </nav>

    <h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-1">
        Resultados para
        <span class="text-[var(--color-brand)]">
            "<?= htmlspecialchars($query) ?>"
        </span>
    </h1>
    <p class="text-[var(--color-text-muted)] text-sm">
        <?php if ($total > 0): ?>
            <?= $total ?> <?= $total === 1 ? 'producto encontrado' : 'productos encontrados' ?>
        <?php endif; ?>
    </p>
</div>

<?php if (empty($products)): ?>

    <!-- Estado vacío -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)]
                p-16 text-center max-w-lg mx-auto">
        <svg class="w-14 h-14 text-[var(--color-border)] mx-auto mb-5"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <h2 class="text-white font-semibold mb-2">
            No se encontraron productos
        </h2>
        <p class="text-[var(--color-text-secondary)] text-sm mb-2">
            No hay productos en la tienda que coincidan con
            <strong class="text-[var(--color-text-primary)]">
                "<?= htmlspecialchars($query) ?>"
            </strong>.
        </p>
        <p class="text-[var(--color-text-muted)] text-xs mb-8">
            Prueba con otro término o explora nuestras categorías.
        </p>
        <a href="<?= APP_URL ?>/"
           class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                  text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
            Ver todas las categorías
        </a>
    </div>

<?php else: ?>

    <!-- Grid de resultados -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <?php foreach ($products as $product): ?>
            <?php require APP_PATH . '/Views/products/partials/product-card.php'; ?>
        <?php endforeach; ?>
    </div>

    <!-- Paginación -->
    <?php if ($pages > 1): ?>
        <nav class="flex items-center justify-center gap-2">

            <?php if ($page > 1): ?>
                <a href="<?= $baseUrl ?>&page=<?= $page - 1 ?>"
                   class="px-4 py-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                          rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                          hover:border-[var(--color-brand)] text-sm transition-colors">
                    ← Anterior
                </a>
            <?php endif; ?>

            <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                <a href="<?= $baseUrl ?>&page=<?= $i ?>"
                   class="w-10 h-10 flex items-center justify-center rounded-xl text-sm border transition-colors
                          <?= $i === $page
                              ? 'bg-[var(--color-brand)] border-[var(--color-brand)] text-white font-semibold'
                              : 'bg-[var(--color-bg-card)] border-[var(--color-border)] text-[var(--color-text-secondary)]
                                 hover:text-[var(--color-text-primary)] hover:border-[var(--color-brand)]' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
                <a href="<?= $baseUrl ?>&page=<?= $page + 1 ?>"
                   class="px-4 py-2 bg-[var(--color-bg-card)] border border-[var(--color-border)]
                          rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                          hover:border-[var(--color-brand)] text-sm transition-colors">
                    Siguiente →
                </a>
            <?php endif; ?>

        </nav>
    <?php endif; ?>

<?php endif; ?>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
