<?php
/*
 * Admin — Listado de pedidos.
 * Permite cambiar el estado de cada pedido.
 */
ob_start();

$totalPages = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

$statusMap = [
    'pending'   => ['label' => 'Pendiente',  'color' => 'text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'paid'      => ['label' => 'Pagado',     'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'shipped'   => ['label' => 'Enviado',    'color' => 'text-[var(--color-info)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'delivered' => ['label' => 'Entregado',  'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'cancelled' => ['label' => 'Cancelado',  'color' => 'text-[var(--color-error)] bg-[var(--color-error-bg)] border-[var(--color-error-border)]'],
];
?>

<div class="pt-2">

    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

        <?php if (empty($orders)): ?>
            <div class="px-6 py-12 text-center text-[var(--color-text-muted)] text-sm">
                No hay pedidos todavía.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)]">
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Pedido</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Cliente</th>
                            <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Fecha</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Total</th>
                            <th class="px-3 md:px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                            <th class="px-3 md:px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Cambiar estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $s = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'color' => 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'];
                        ?>
                            <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">
                                <td class="px-3 md:px-6 py-4 font-semibold text-[var(--color-text-primary)]">
                                    #<?= (int) $order['id'] ?>
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <p class="text-[var(--color-text-primary)] font-medium">
                                        <?= htmlspecialchars($order['user_name'] . ' ' . $order['user_last_name']) ?>
                                    </p>
                                    <p class="hidden md:block text-xs text-[var(--color-text-muted)]">
                                        <?= htmlspecialchars($order['user_email']) ?>
                                    </p>
                                </td>
                                <td class="hidden md:table-cell px-6 py-4 text-[var(--color-text-muted)]">
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="px-3 md:px-6 py-4 font-semibold text-[var(--color-warning)]">
                                    <?= number_format((float) $order['total'], 2, ',', '.') ?> €
                                </td>
                                <td class="px-3 md:px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border <?= $s['color'] ?>">
                                        <?= $s['label'] ?>
                                    </span>
                                </td>
                                <td class="px-3 md:px-6 py-4 text-right">
                                    <form method="POST"
                                          action="<?= APP_URL ?>/admin/orders/<?= (int) $order['id'] ?>"
                                          class="inline-flex items-center gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                        <select name="status"
                                                class="bg-[var(--color-bg-secondary)] text-[var(--color-text-secondary)]
                                                       border border-[var(--color-border)] rounded-lg px-2 py-1.5
                                                       text-xs focus:outline-none focus:border-[var(--color-brand)]
                                                       transition-colors">
                                            <?php foreach ($statusMap as $val => $info): ?>
                                                <option value="<?= $val ?>"
                                                        <?= $order['status'] === $val ? 'selected' : '' ?>>
                                                    <?= $info['label'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                       bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                                                       text-white transition-colors">
                                            Guardar
                                        </button>
                                    </form>
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
                        Página <?= $page ?> de <?= $totalPages ?> — <?= $total ?> pedidos
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
