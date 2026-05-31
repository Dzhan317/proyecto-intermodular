<?php
/*
 * Admin — Listado de conversaciones de soporte.
 * Muestra ID, usuario, asunto, estado, último mensaje y enlace al detalle.
 */
ob_start();

$totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;
?>

<div class="pt-2">

    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

        <?php if (empty($conversations)): ?>
            <div class="px-6 py-12 text-center text-[var(--color-text-muted)] text-sm">
                No hay conversaciones todavía.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)]">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Asunto</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Último mensaje</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($conversations as $conv): ?>
                            <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0 cursor-pointer"
                                onclick="window.location.href='<?= APP_URL ?>/admin/support/<?= (int) $conv['id'] ?>'"
                                title="Ver conversación #<?= (int) $conv['id'] ?>"
                                role="link"
                                tabindex="0"
                                onkeydown="if(event.key==='Enter')window.location.href='<?= APP_URL ?>/admin/support/<?= (int) $conv['id'] ?>'">
                                <td class="px-6 py-4 font-semibold text-[var(--color-text-primary)]">
                                    #<?= (int) $conv['id'] ?>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[var(--color-text-primary)] font-medium">
                                        <?= htmlspecialchars($conv['user_name'] . ' ' . $conv['user_last_name']) ?>
                                    </p>
                                    <p class="text-xs text-[var(--color-text-muted)]">
                                        <?= htmlspecialchars($conv['user_email']) ?>
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-secondary)] max-w-[200px] truncate">
                                    <?= htmlspecialchars($conv['subject']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium
                                                 <?= $conv['status'] === 'open'
                                                     ? 'text-[var(--color-success)]'
                                                     : 'text-[var(--color-error)]' ?>">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0
                                                     <?= $conv['status'] === 'open'
                                                         ? 'bg-[var(--color-success)]'
                                                         : 'bg-[var(--color-error)]' ?>"></span>
                                        <?= $conv['status'] === 'open' ? 'Abierto' : 'Cerrado' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[var(--color-text-muted)] max-w-[180px] truncate text-xs">
                                    <?php if (!empty($conv['last_message'])): ?>
                                        "<?= htmlspecialchars(mb_substr($conv['last_message'], 0, 40)) ?><?= mb_strlen($conv['last_message']) > 40 ? '…' : '' ?>"
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?= APP_URL ?>/admin/support/<?= (int) $conv['id'] ?>"
                                       class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]
                                              text-xs font-medium transition-colors">
                                        Ver →
                                    </a>
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
                        Página <?= $page ?> de <?= $totalPages ?> — <?= $total ?> conversaciones
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
