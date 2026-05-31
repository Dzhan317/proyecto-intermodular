<?php
/*
 * Admin — Listado de categorías con paginación.
 * Muestra columna "Destacada" para controlar qué aparece en la home.
 */
ob_start();

$totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="pt-2">

    <!-- Cabecera: buscador + botón añadir -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <form method="GET" action="<?= APP_URL ?>/admin/categories" class="relative max-w-xs w-full">
            <input type="text"
                   name="q"
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Buscar categoría... (Enter para buscar)"
                   class="w-full bg-[var(--color-bg-card)] text-[var(--color-text-primary)]
                          placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                          rounded-xl pl-4 pr-9 py-2.5 text-sm
                          focus:outline-none focus:border-[var(--color-brand)]
                          focus:ring-1 focus:ring-[var(--color-brand)] transition-colors">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 text-[var(--color-text-muted)]"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
        <a href="<?= APP_URL ?>/admin/categories/create"
           class="flex-shrink-0 bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-white
                  font-semibold px-5 py-2.5 rounded-xl text-sm transition-colors">
            + Añadir categoría
        </a>
    </div>

    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

        <?php if (empty($categories)): ?>
            <div class="px-6 py-12 text-center text-[var(--color-text-muted)] text-sm">
                No hay categorías todavía.
            </div>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-border)]">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">En home</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">
                            <td class="px-6 py-4 font-medium text-[var(--color-text-primary)]">
                                <?= htmlspecialchars($cat['name']) ?>
                            </td>
                            <td class="px-6 py-4 text-[var(--color-text-muted)] font-mono text-xs">
                                <?= htmlspecialchars($cat['slug']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border
                                             <?= $cat['status'] === 'active'
                                                 ? 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'
                                                 : 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]' ?>">
                                    <?= $cat['status'] === 'active' ? 'Activa' : 'Inactiva' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($cat['featured']): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border
                                                 text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-warning)]/30">
                                        ★ Destacada
                                    </span>
                                <?php else: ?>
                                    <span class="text-xs text-[var(--color-text-disabled)]">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?= APP_URL ?>/admin/categories/<?= (int) $cat['id'] ?>/edit"
                                       class="p-1.5 rounded-lg text-[var(--color-text-muted)]
                                              hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                              transition-colors" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST"
                                          action="<?= APP_URL ?>/admin/categories/<?= (int) $cat['id'] ?>/delete"
                                          onsubmit="return confirm('¿Eliminar esta categoría? Solo es posible si no tiene productos asociados.')">
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

            <!-- Paginación -->
            <?php if ($totalPages > 1): ?>
                <div class="px-6 py-4 border-t border-[var(--color-border)] flex items-center justify-between">
                    <p class="text-xs text-[var(--color-text-muted)]">
                        Página <?= $page ?> de <?= $totalPages ?> — <?= $total ?> categorías
                    </p>
                    <div class="flex items-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>"
                               class="px-3 py-1.5 rounded-lg text-sm text-[var(--color-text-secondary)]
                                      hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-hover)]
                                      transition-colors">← Anterior</a>
                        <?php endif; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>"
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
