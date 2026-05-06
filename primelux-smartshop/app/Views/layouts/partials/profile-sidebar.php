<?php
/*
 * Sidebar del perfil de usuario.
 * $activeTab indica qué pestaña está activa: 'profile' | 'security'
 * MIS PEDIDOS y SOPORTE son placeholders para Fases 7 y 8.
 */
$tabs = [
    ['id' => 'profile',  'label' => 'Inicio',      'href' => APP_URL . '/profile',          'phase' => true],
    ['id' => 'orders',   'label' => 'Mis pedidos',  'href' => null,                          'phase' => false],
    ['id' => 'security', 'label' => 'Seguridad',    'href' => APP_URL . '/profile/security', 'phase' => true],
    ['id' => 'support',  'label' => 'Soporte',      'href' => null,                          'phase' => false],
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
                    <!-- Pestaña pendiente de implementar -->
                    <span class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm
                                 text-[#4B5563] cursor-not-allowed select-none">
                        <?= htmlspecialchars($tab['label']) ?>
                        <span class="ml-auto text-xs bg-[#1F2937] text-[#4B5563]
                                     px-2 py-0.5 rounded-full">Próximamente</span>
                    </span>
                <?php else: ?>
                    <a href="<?= $tab['href'] ?>"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors
                              <?= $isActive
                                  ? 'bg-[#1F2937] text-white font-semibold border-l-2 border-[#2563EB]'
                                  : 'text-[#9CA3AF] hover:text-white hover:bg-[#1F2937]' ?>">
                        <?= htmlspecialchars($tab['label']) ?>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
