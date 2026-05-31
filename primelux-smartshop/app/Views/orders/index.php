<?php
/*
 * Mis pedidos — listado de pedidos del usuario autenticado.
 * Muestra número, fecha, total, estado y enlace al detalle.
 */
ob_start();

$statusMap = [
    'pending'   => ['label' => 'Pendiente',  'color' => 'text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'paid'      => ['label' => 'Pagado',     'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'shipped'   => ['label' => 'Enviado',    'color' => 'text-[var(--color-info)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'delivered' => ['label' => 'Entregado',  'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'cancelled' => ['label' => 'Cancelado',  'color' => 'text-[var(--color-error)] bg-[var(--color-error-bg)] border-[var(--color-error-border)]'],
];
?>

<div class="flex flex-col md:flex-row gap-6">

    <?php require APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 min-w-0 space-y-4">

        <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">
            Mis pedidos
        </h2>

        <?php if (empty($orders)): ?>

            <!-- Estado vacío -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)]
                        p-12 text-center">
                <svg class="w-12 h-12 text-[var(--color-border)] mx-auto mb-4"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                             M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-1">
                    Sin pedidos todavía
                </h3>
                <p class="text-xs text-[var(--color-text-secondary)] mb-5">
                    Cuando realices tu primer pedido, aparecerá aquí.
                </p>
                <a href="<?= APP_URL ?>/"
                   class="inline-block bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                          text-white font-semibold px-5 py-2.5 rounded-xl text-xs transition-colors">
                    Explorar tienda
                </a>
            </div>

        <?php else: ?>

            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[var(--color-border)]">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Pedido</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order):
                                $s = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'color' => 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'];
                            ?>
                                <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">
                                    <td class="px-6 py-4 font-semibold text-[var(--color-text-primary)]">
                                        #<?= (int) $order['id'] ?>
                                    </td>
                                    <td class="px-6 py-4 text-[var(--color-text-muted)]">
                                        <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-[var(--color-warning)]">
                                        <?= number_format((float) $order['total'], 2, ',', '.') ?> €
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border <?= $s['color'] ?>">
                                            <?= $s['label'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= APP_URL ?>/orders/<?= (int) $order['id'] ?>"
                                           class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]
                                                  text-xs font-medium transition-colors">
                                            Ver detalle →
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
