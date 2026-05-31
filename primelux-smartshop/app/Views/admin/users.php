<?php
/*
 * Admin — Listado de usuarios.
 * Permite buscar y cambiar estado (activo/bloqueado) de los usuarios.
 * Nadie puede modificar al superadmin.
 */
ob_start();

$totalPages   = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="pt-2">

    <!-- Barra de acciones -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <form method="GET" action="<?= APP_URL ?>/admin/users" class="flex-1 max-w-sm">
            <div class="relative">
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Buscar por nombre o email... (Enter para buscar)"
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>
        <p class="text-sm text-[var(--color-text-muted)]"><?= $total ?> usuarios en total</p>
    </div>

    <!-- Tabla -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">
        <?php if (empty($users)): ?>
            <div class="px-6 py-12 text-center text-[var(--color-text-muted)] text-sm">
                <?= $search ? 'No se encontraron usuarios para "' . htmlspecialchars($search) . '".' : 'No hay usuarios todavía.' ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)]">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Registro</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u):
                            $isSelf        = ((int) $u['id'] === (int) ($_SESSION['user_id'] ?? 0));
                            $isTargetSuper = ((int) $u['id'] === 1);
                            $canModify     = !$isSelf && !$isTargetSuper;
                        ?>
                            <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">

                                <!-- Nombre -->
                                <td class="px-6 py-4 font-medium text-[var(--color-text-primary)]">
                                    <?= htmlspecialchars($u['name'] . ' ' . $u['last_name']) ?>
                                    <?php if ($isSelf): ?>
                                        <span class="ml-1 text-xs text-[var(--color-brand)]">(tú)</span>
                                    <?php endif; ?>
                                    <?php if ($isTargetSuper): ?>
                                        <span class="ml-1 text-xs text-[var(--color-warning)]">★</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                    <?= htmlspecialchars($u['email']) ?>
                                </td>

                                <!-- Rol -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border
                                                 <?= $u['role'] === 'admin'
                                                        ? 'text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-warning)]/30'
                                                        : 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]' ?>">
                                        <?= $u['role'] === 'admin' ? 'Admin' : 'Cliente' ?>
                                    </span>
                                </td>

                                <!-- Estado -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border
                                                 <?= $u['status'] === 'active'
                                                        ? 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'
                                                        : 'text-[var(--color-error)] bg-[var(--color-error-bg)] border-[var(--color-error-border)]' ?>">
                                        <?= $u['status'] === 'active' ? 'Activo' : 'Bloqueado' ?>
                                    </span>
                                </td>

                                <!-- Fecha registro -->
                                <td class="px-6 py-4 text-[var(--color-text-muted)]">
                                    <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        <?php if (!$canModify): ?>
                                            <span class="text-xs text-[var(--color-text-disabled)]">—</span>

                                        <?php else: ?>

                                            <!-- Cambiar estado -->
                                            <form method="POST"
                                                action="<?= APP_URL ?>/admin/users/<?= (int) $u['id'] ?>"
                                                onsubmit="return confirm('¿Cambiar el estado de este usuario?')">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="status"
                                                    value="<?= $u['status'] === 'active' ? 'blocked' : 'active' ?>">
                                                <button type="submit"
                                                    class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors
                                                               <?= $u['status'] === 'active'
                                                                    ? 'text-[var(--color-error)] border-[var(--color-error)]/30 hover:bg-[var(--color-error-bg)]'
                                                                    : 'text-[var(--color-success)] border-[var(--color-success)]/30 hover:bg-[var(--color-success-bg)]' ?>">
                                                    <?= $u['status'] === 'active' ? 'Bloquear' : 'Activar' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>

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
                        Página <?= $page ?> de <?= $totalPages ?> — <?= $total ?> usuarios
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
 