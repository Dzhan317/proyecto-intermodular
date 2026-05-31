<?php
/*
 * Mis pedidos — detalle de un pedido concreto.
 * Muestra productos, dirección de envío, tipo de envío, total y estado.
 * $order incluye $order['items'] gracias a OrderModel::findById().
 */
ob_start();

$statusMap = [
    'pending'   => ['label' => 'Pendiente',  'color' => 'text-[var(--color-warning)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'paid'      => ['label' => 'Pagado',     'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'shipped'   => ['label' => 'Enviado',    'color' => 'text-[var(--color-info)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'],
    'delivered' => ['label' => 'Entregado',  'color' => 'text-[var(--color-success)] bg-[var(--color-success-bg)] border-[var(--color-success-border)]'],
    'cancelled' => ['label' => 'Cancelado',  'color' => 'text-[var(--color-error)] bg-[var(--color-error-bg)] border-[var(--color-error-border)]'],
];

$s = $statusMap[$order['status']] ?? ['label' => ucfirst($order['status']), 'color' => 'text-[var(--color-text-muted)] bg-[var(--color-bg-secondary)] border-[var(--color-border)]'];
?>

<div class="flex flex-col md:flex-row gap-6">

    <?php require APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 min-w-0 space-y-4">

        <!-- Cabecera -->
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">
                Pedido #<?= (int) $order['id'] ?>
            </h2>
            <a href="<?= APP_URL ?>/orders"
               class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
                ← Volver a mis pedidos
            </a>
        </div>

        <!-- Estado y fecha -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Fecha del pedido</p>
                    <p class="text-sm text-[var(--color-text-primary)]">
                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold border <?= $s['color'] ?>">
                    <?= $s['label'] ?>
                </span>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">
            <div class="px-6 py-4 border-b border-[var(--color-border)]">
                <h3 class="text-sm font-semibold text-[var(--color-text-primary)]">Productos</h3>
            </div>
            <div class="divide-y divide-[var(--color-divider)]">
                <?php foreach ($order['items'] as $item): ?>
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-[var(--color-text-primary)] font-medium truncate">
                                <?= htmlspecialchars($item['product_name_snapshot']) ?>
                            </p>
                            <?php if (!empty($item['variant_name'])): ?>
                                <p class="text-xs text-[var(--color-text-muted)] mt-0.5">
                                    <?= htmlspecialchars($item['variant_name']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-[var(--color-text-muted)]">
                                <?= (int) $item['quantity'] ?> × <?= number_format((float) $item['unit_price'], 2, ',', '.') ?> €
                            </p>
                            <p class="text-sm font-semibold text-[var(--color-text-primary)]">
                                <?= number_format((float) $item['subtotal'], 2, ',', '.') ?> €
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Subtotal + Total -->
            <div class="px-6 py-4 border-t border-[var(--color-border)] space-y-2">
                <?php $subtotal = array_sum(array_column($order['items'], 'subtotal')); ?>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-[var(--color-text-muted)]">Subtotal</p>
                    <p class="text-sm text-[var(--color-text-secondary)]">
                        <?= number_format((float) $subtotal, 2, ',', '.') ?> €
                    </p>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-[var(--color-text-muted)]">
                        <?php
                        $shippingLabels = [
                            'standard'     => 'Envío estándar',
                            'express'      => 'Envío express',
                            'pickup_point' => 'Recogida en tienda',
                        ];
                        $shippingLabel = $shippingLabels[$order['shipping_type']] ?? ucfirst($order['shipping_type']);
                        ?>
                        Envío: <?= htmlspecialchars($shippingLabel) ?>
                        (<?= number_format((float) $order['shipping_cost'], 2, ',', '.') ?> €)
                    </p>
                    <p class="text-base font-bold text-[var(--color-warning)]">
                        Total: <?= number_format((float) $order['total'], 2, ',', '.') ?> €
                    </p>
                </div>
            </div>
        </div>

        <!-- Dirección de envío -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-4">Dirección de envío</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Dirección</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['street']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Código postal</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['postal_code']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Ciudad</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['city']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Provincia</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['province']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">País</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['country']) ?></p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-text-muted)] uppercase tracking-wider mb-1">Teléfono</p>
                    <p class="text-sm text-[var(--color-text-primary)]"><?= htmlspecialchars($order['phone']) ?></p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
