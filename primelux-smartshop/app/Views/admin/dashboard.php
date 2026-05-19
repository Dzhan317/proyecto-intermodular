<?php
/*
 * Admin — Dashboard.
 * 6 tarjetas de estadísticas + tabla de últimos pedidos.
 * Ingresos: pedidos paid + shipped + delivered.
 * Margen bruto: ingresos − coste de productos vendidos.
 */
ob_start();
?>

<div class="pt-2">

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 mb-8">

        <!-- Total pedidos -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Total pedidos</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-brand)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-brand)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-text-primary)]"><?= $totalOrders ?></p>
        </div>

        <!-- Ingresos totales -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Ingresos totales</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-warning)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-warning)]">
                <?= number_format($totalRevenue, 2, ',', '.') ?> €
            </p>
            <p class="text-xs text-[var(--color-text-muted)] mt-1">Pedidos pagados, enviados y entregados</p>
        </div>

        <!-- Coste total -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Coste total</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-error)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-error)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-error)]">
                <?= number_format($totalCost, 2, ',', '.') ?> €
            </p>
            <p class="text-xs text-[var(--color-text-muted)] mt-1">Coste de productos vendidos</p>
        </div>

        <!-- Margen bruto -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Margen bruto</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-success)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-success)]">
                <?= number_format($grossMargin, 2, ',', '.') ?> €
            </p>
            <p class="text-xs text-[var(--color-text-muted)] mt-1">
                <?= $marginPct ?>% de margen sobre ingresos
            </p>
        </div>

        <!-- Usuarios -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Usuarios Registrados</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-success)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-text-primary)]"><?= $totalUsers ?></p>
        </div>

        <!-- Productos activos -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Productos activos</span>
                <div class="w-9 h-9 rounded-xl bg-[var(--color-info)]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[var(--color-info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-[var(--color-text-primary)]"><?= $totalProducts ?></p>
        </div>

    </div>

    <!-- Últimos pedidos -->
    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

        <div class="px-6 py-4 border-b border-[var(--color-border)] flex items-center justify-between">
            <h2 class="text-sm font-semibold text-[var(--color-text-primary)] uppercase tracking-wider">
                Últimos pedidos
            </h2>
            <a href="<?= APP_URL ?>/admin/orders"
               class="text-xs text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
                Ver todos →
            </a>
        </div>

        <?php if (empty($recentOrders)): ?>
            <div class="px-6 py-10 text-center text-[var(--color-text-muted)] text-sm">
                No hay pedidos todavía.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)]">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Pedido</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order):
                            $statusMap = [
                                'paid'      => ['label' => 'Pagado',    'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
                                'pending'   => ['label' => 'Pendiente', 'color' => 'text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
                                'shipped'   => ['label' => 'Enviado',   'color' => 'text-[var(--color-info)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
                                'delivered' => ['label' => 'Entregado', 'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
                                'cancelled' => ['label' => 'Cancelado', 'color' => 'text-[var(--color-error)] bg-[var(--color-error-bg)] border-[var(--color-error-border)]'],
                            ];
                            $s = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'color' => 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'];
                        ?>
                            <tr class="border-b border-[var(--color-divider)] hover:bg-[var(--color-bg-hover)]/30 transition-colors last:border-b-0">
                                <td class="px-6 py-4 font-semibold text-[var(--color-text-primary)]">#<?= (int) $order['id'] ?></td>
                                <td class="px-6 py-4 text-[var(--color-text-secondary)]">
                                    <?= htmlspecialchars($order['user_name'] . ' ' . $order['user_last_name']) ?>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/admin.php';
