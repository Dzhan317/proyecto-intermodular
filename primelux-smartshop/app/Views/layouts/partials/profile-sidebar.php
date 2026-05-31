<?php
/*
 * Sidebar del perfil de usuario.
 * $activeTab indica qué pestaña está activa:
 * 'profile' | 'security' | 'addresses' | 'orders' | 'support'
 */
$activeTab = (string) ($activeTab ?? 'profile');

$tabs = [
    ['id' => 'profile',   'label' => 'Inicio',     'href' => APP_URL . '/profile'],
    ['id' => 'addresses', 'label' => 'Dirección',   'href' => APP_URL . '/profile/addresses'],
    ['id' => 'orders',    'label' => 'Mis pedidos', 'href' => APP_URL . '/orders'],
    ['id' => 'security',  'label' => 'Seguridad',   'href' => APP_URL . '/profile/security'],
    ['id' => 'support',   'label' => 'Soporte',     'href' => APP_URL . '/support'],
];
?>
<nav class="w-full md:w-56 flex-shrink-0">
    <ul class="space-y-1">
        <?php foreach ($tabs as $tab): ?>
            <?php
            $isActive = ($activeTab === $tab['id']);
            ?>
            <li>
                <a href="<?= $tab['href'] ?>"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors
                          <?= $isActive
                              ? 'bg-[var(--color-bg-card)] text-[var(--color-text-primary)] font-semibold border-l-2 border-[var(--color-brand)]'
                              : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                    <?= htmlspecialchars($tab['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
