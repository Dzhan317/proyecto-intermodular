<?php
/*
 * Home — portada pública.
 * Muestra categorías principales y productos destacados.
 */
ob_start();

$categories = is_array($categories ?? null) ? $categories : [];
$featuredProducts = is_array($featuredProducts ?? null) ? $featuredProducts : [];

$categoryDescriptions = [
    'componentes' => 'Procesadores, memorias, placas base y más hardware.',
    'electronica' => 'Dispositivos y accesorios para tu día a día digital.',
    'informatica' => 'Equipos y soluciones para trabajo, estudio y gaming.',
    'perifericos' => 'Teclados, ratones, monitores y accesorios esenciales.',
    'redes' => 'Routers, switches y conectividad para casa o empresa.',
    'software' => 'Licencias y herramientas para productividad y seguridad.',
];

$categoryVariants = [
    'componentes' => 'gold',
    'perifericos' => 'gold',
    'informatica' => 'blue',
    'software' => 'blue',
    'electronica' => 'hybrid',
    'redes' => 'hybrid',
];
?>

<div class="space-y-10">

    <section class="relative overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)]">
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-brand)]/10 via-transparent to-[var(--color-accent)]/10"></div>
        <div class="relative px-6 py-10 md:px-10 md:py-14">
            <div class="max-w-3xl">
                <h1 class="mt-4 text-4xl font-bold text-[var(--color-text-primary)] md:text-5xl">
                    Tecnología premium para un e-commerce claro, rápido y funcional.
                </h1>
                <p class="mt-4 max-w-2xl text-[var(--color-text-secondary)] text-sm md:text-base">
                    Explora categorías, descubre productos destacados y navega por una interfaz coherente centrada en la experiencia de compra.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="#featured-products"
                       class="inline-flex items-center rounded-xl bg-[var(--color-brand)] px-5 py-3 text-sm font-semibold text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-brand-hover)]">
                        Ver destacados
                    </a>
                    <?php if (!empty($categories)): ?>
                        <a href="<?= APP_URL ?>/category/<?= htmlspecialchars($categories[0]['slug'] ?? '') ?>"
                           class="inline-flex items-center rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-secondary)] px-5 py-3 text-sm font-semibold text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-bg-hover)]">
                            Explorar categorías
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($categories)): ?>
        <section aria-labelledby="home-categories">
            <div class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-link)]">Navegación</p>
                    <h2 id="home-categories" class="mt-2 text-2xl font-bold text-[var(--color-text-primary)]">
                        Categorías principales
                    </h2>
                </div>
                <p class="text-sm text-[var(--color-text-muted)]"><?= count($categories) ?> categorías activas</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <?php foreach ($categories as $category): ?>
                    <?php
                    $slug = (string) ($category['slug'] ?? '');
                    $name = (string) ($category['name'] ?? 'Categoría');
                    $description = $categoryDescriptions[$slug] ?? 'Explora productos seleccionados dentro de esta categoría.';
                    $variant = $categoryVariants[$slug] ?? 'blue';
                    ?>
                    <a href="<?= APP_URL ?>/category/<?= htmlspecialchars($slug) ?>"
                       class="category-card category-card--<?= htmlspecialchars($variant) ?> group block rounded-2xl p-5">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="category-card-title text-lg font-semibold">
                                <?= htmlspecialchars($name) ?>
                            </h3>

                            <span class="category-card-icon inline-flex h-10 w-10 items-center justify-center rounded-xl">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>

                        <p class="category-card-description mt-3 text-sm leading-6">
                            <?= htmlspecialchars($description) ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section id="featured-products" aria-labelledby="featured-products-title">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-link)]">Selección</p>
                <h2 id="featured-products-title" class="mt-2 text-2xl font-bold text-[var(--color-text-primary)]">
                    Productos destacados
                </h2>
            </div>
            <p class="text-sm text-[var(--color-text-muted)]"><?= count($featuredProducts) ?> resultados</p>
        </div>

        <?php if (!empty($featuredProducts)): ?>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <?php require APP_PATH . '/Views/products/partials/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-8 text-center">
                <p class="text-base font-semibold text-[var(--color-text-primary)]">Todavía no hay productos destacados disponibles.</p>
                <p class="mt-2 text-sm text-[var(--color-text-secondary)]">Cuando añadas productos activos a la base de datos, aparecerán aquí automáticamente.</p>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';