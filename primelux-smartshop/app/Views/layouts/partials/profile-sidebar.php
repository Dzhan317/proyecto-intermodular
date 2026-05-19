<?php
/*
 * Sidebar del perfil de usuario.
 * $activeTab indica qué pestaña está activa:
 * 'profile' | 'security' | 'addresses' | 'orders'
 */
$activeTab = (string) ($activeTab ?? 'profile');

$tabs = [
    ['id' => 'profile',   'label' => 'Inicio',     'href' => APP_URL . '/profile',           'phase' => true],
    ['id' => 'addresses', 'label' => 'Dirección',   'href' => APP_URL . '/profile/addresses', 'phase' => true],
    ['id' => 'orders',    'label' => 'Mis pedidos', 'href' => APP_URL . '/orders',            'phase' => true],
    ['id' => 'security',  'label' => 'Seguridad',   'href' => APP_URL . '/profile/security',  'phase' => true],
    ['id' => 'support',   'label' => 'Soporte',     'href' => null,                           'phase' => false],
];
?>
<nav class="w-full md:w-56 flex-shrink-0">
    <ul class="space-y-1">
        <?php foreach ($tabs as $tab): ?>
            <?php
            $isActive   = ($activeTab === $tab['id']);
            $isDisabled = !$tab['phase'];
            ?>
            <li>
                <?php if ($isDisabled): ?>
                    <span class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm
                                 text-[var(--color-text-disabled)] cursor-not-allowed select-none">
                        <?= htmlspecialchars($tab['label']) ?>
                        <span class="ml-auto text-xs bg-[var(--color-bg-card)]
                                     text-[var(--color-text-disabled)]
                                     px-2 py-0.5 rounded-full">
                            Próximamente
                        </span>
                    </span>
                <?php else: ?>
                    <a href="<?= $tab['href'] ?>"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors
                              <?= $isActive
                                  ? 'bg-[var(--color-bg-card)] text-[var(--color-text-primary)] font-semibold border-l-2 border-[var(--color-brand)]'
                                  : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                        <?= htmlspecialchars($tab['label']) ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
