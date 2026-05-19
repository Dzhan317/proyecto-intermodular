<?php
/*
 * Admin — Listado de productos.
 * CRUD completo: crear, editar, eliminar (soft delete).
 */
ob_start();

$totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="pt-2">

    <!-- Barra de acciones -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <form method="GET" action="<?= APP_URL ?>/admin/products" class="flex-1 max-w-sm">
            <div class="relative">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Buscar productos..."
                       class="w-full bg-[var(--color-bg-card)] text-[var(--color-text-primary)]
                              placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                              rounded-xl pl-4 pr-10 py-2.5 text-sm focus:outline-none
                              focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                              transition-colors">
                <button type="submit"
                        class="absolute inset-y-0 right-3 flex items-center text-[var(--color-text-muted)]
                               hover:text-[var(--color-text-primary)] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
        <a href="<?= APP_URL ?>/admin/products/create"
           class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-white
                  font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors whitespace-nowrap">
            + Añadir producto
        </a>
    </div>

    <!-- Tabla -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">
        <?php if (empty($products)): ?>
            <div class="px-6 py-12 text-center text-[var(--color-text-muted)] text-sm">
                <?= $search ? 'No se encontraron productos para "' . htmlspecialchars($search) . '".' : 'No hay productos todavía.' ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)]">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Margen</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">
                                <td class="px-6 py-4">
                                    <p class="font-medium text-[var(--color-text-primary)] line-clamp-1">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </p>
                                    <?php if (!empty($product['brand'])): ?>
                                        <p class="text-xs text-[var(--color-text-muted)] mt-0.5">
                                            <?= htmlspecialchars($product['brand']) ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                    <?= htmlspecialchars($product['category_name'] ?? '—') ?>
                                </td>
                                <td class="px-6 py-4 font-semibold text-[var(--color-warning)]">
                                    <?= number_format((float) $product['base_price'], 2, ',', '.') ?> €
                                </td>
                                <td class="px-6 py-4">
                                    <?php $stock = (int) $product['stock']; ?>
                                    <span class="font-medium <?= $stock === 0 ? 'text-[var(--color-error)]' : ($stock <= 3 ? 'text-[var(--color-warning)]' : 'text-[var(--color-text-primary)]') ?>">
                                        <?= $stock ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $costPrice  = (float) ($product['cost_price'] ?? 0);
                                    $salePrice  = (float) $product['base_price'];
                                    $marginPct  = $salePrice > 0 && $costPrice > 0
                                        ? round((($salePrice - $costPrice) / $salePrice) * 100, 1)
                                        : null;
                                    ?>
                                    <?php if ($marginPct !== null): ?>
                                        <span class="font-medium <?= $marginPct >= 20 ? 'text-[var(--color-success)]' : ($marginPct >= 10 ? 'text-[var(--color-warning)]' : 'text-[var(--color-error)]') ?>">
                                            <?= $marginPct ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-[var(--color-text-disabled)] text-xs">Sin datos</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border
                                                 <?= $product['status'] === 'active'
                                                     ? 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'
                                                     : 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]' ?>">
                                        <?= $product['status'] === 'active' ? 'Activo' : 'Inactivo' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Editar -->
                                        <a href="<?= APP_URL ?>/admin/products/<?= (int) $product['id'] ?>/edit"
                                           class="p-1.5 rounded-lg text-[var(--color-text-muted)]
                                                  hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                                  transition-colors" title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <!-- Eliminar -->
                                        <form method="POST"
                                              action="<?= APP_URL ?>/admin/products/<?= (int) $product['id'] ?>/delete"
                                              onsubmit="return confirm('¿Eliminar este producto?')">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg text-[var(--color-text-muted)]
                                                           hover:text-[var(--color-error)] hover:bg-[var(--color-error-bg)]
                                                           transition-colors" title="Eliminar">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-[var(--color-border)] flex items-center justify-between">
                    <p class="text-xs text-[var(--color-text-muted)]">
                        Página <?= $page ?> de <?= $totalPages ?> — <?= $total ?> productos
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                               class="px-3 py-1.5 rounded-lg text-sm text-[var(--color-text-secondary)]
                                      hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                      transition-colors">← Anterior</a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?><?= $search ? '&q=' . urlencode($search) : '' ?>"
                               class="px-3 py-1.5 rounded-lg text-sm text-[var(--color-text-secondary)]
                                      hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                      transition-colors">Siguiente →</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/admin.php';
