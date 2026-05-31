<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Panel Admin — PrimeLux SmartShop') ?></title>
    <meta name="robots" content="noindex">

    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>window.APP_URL = '<?= APP_URL ?>';</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sora: ['Sora', 'sans-serif'] } } }
        }
    </script>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] min-h-screen font-sora">

<div class="flex min-h-screen">

    <!-- ── Sidebar ──────────────────────────────────────────────────────── -->
    <?php
    $adminUri   = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $adminLinks = [
        ['href' => APP_URL . '/admin',            'label' => 'Dashboard',   'match' => '/admin',            'exact' => true,  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['href' => APP_URL . '/admin/products',   'label' => 'Productos',   'match' => '/admin/products',   'exact' => false, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['href' => APP_URL . '/admin/categories', 'label' => 'Categorías',  'match' => '/admin/categories', 'exact' => false, 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
        ['href' => APP_URL . '/admin/orders',     'label' => 'Pedidos',     'match' => '/admin/orders',     'exact' => false, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['href' => APP_URL . '/admin/users',      'label' => 'Usuarios',    'match' => '/admin/users',      'exact' => false, 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['href' => APP_URL . '/admin/support',    'label' => 'Soporte',     'match' => '/admin/support',    'exact' => false, 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
    ];
    ?>
    <aside id="adminSidebar" class="hidden md:flex w-60 flex-shrink-0 bg-[var(--color-bg-surface)] border-r border-[var(--color-bg-surface)] flex-col min-h-screen sticky top-0">

        <!-- Logo -->
        <div class="px-6 py-5 border-b border-[var(--color-bg-hover)]">
            <a href="<?= APP_URL ?>/admin">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_principal_header.webp"
                     alt="PrimeLux Admin" class="h-12 w-auto">
            </a>
            <p class="text-xs text-[var(--color-text-disabled)] mt-1 font-medium uppercase tracking-wider">
                Panel de administración
            </p>
        </div>

        <!-- Navegación -->
        <nav class="flex-1 px-3 py-4">
            <ul class="space-y-1">
                <?php foreach ($adminLinks as $link):
                    $isActive = $link['exact']
                        ? $adminUri === parse_url($link['href'], PHP_URL_PATH)
                        : str_starts_with($adminUri, parse_url($link['match'], PHP_URL_PATH));
                ?>
                    <li>
                        <a href="<?= $link['href'] ?>"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                                  <?= $isActive
                                      ? 'bg-[var(--color-brand)] text-white'
                                      : 'text-[var(--color-bg-hover)] hover:text-white hover:bg-[var(--color-bg-surface-hover)]' ?>">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $link['icon'] ?>"/>
                            </svg>
                            <?= $link['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- Footer del sidebar -->
        <div class="px-3 py-4 border-t border-[var(--color-bg-hover)] space-y-1">
            <a href="<?= APP_URL ?>/"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                      text-[var(--color-bg-hover)] hover:text-white
                      hover:bg-[var(--color-bg-surface-hover)] transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ver tienda
            </a>
            <a href="<?= APP_URL ?>/logout"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                      text-[var(--color-error)] hover:text-white hover:bg-[var(--color-error-bg)]
                      transition-colors">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Cerrar sesión
            </a>
        </div>

    </aside>

    <!-- ── Contenido principal ───────────────────────────────────────────── -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Header del admin -->
        <header class="bg-[var(--color-bg-surface)] border-b border-[var(--color-bg-hover)] px-4 md:px-8 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <!-- Botón hamburguesa — solo visible en móvil -->
                <button type="button" id="adminMenuToggle"
                        class="md:hidden p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-white hover:bg-[var(--color-bg-surface-hover)] transition-colors">
                    <svg id="adminHamburgerIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="adminCloseIcon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <h1 class="text-base md:text-lg font-semibold text-white truncate">
                    <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
                </h1>
            </div>
            <span class="text-sm text-[var(--color-bg-hover)] hidden sm:block">
                Bienvenido, <strong class="text-white">
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </strong>
            </span>
        </header>

        <!-- Panel menú móvil del admin — oculto por defecto -->
        <div id="adminMobileMenu"
             class="hidden md:hidden bg-[var(--color-bg-surface)] border-b border-[var(--color-bg-hover)]">
            <nav class="px-3 py-3">
                <ul class="space-y-1">
                    <?php foreach ($adminLinks as $link):
                        $isActive = $link['exact']
                            ? $adminUri === parse_url($link['href'], PHP_URL_PATH)
                            : str_starts_with($adminUri, parse_url($link['match'], PHP_URL_PATH));
                    ?>
                        <li>
                            <a href="<?= $link['href'] ?>"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                                      <?= $isActive
                                          ? 'bg-[var(--color-brand)] text-white'
                                          : 'text-[var(--color-bg-hover)] hover:text-white hover:bg-[var(--color-bg-surface-hover)]' ?>">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $link['icon'] ?>"/>
                                </svg>
                                <?= $link['label'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="border-t border-[var(--color-bg-hover)] mt-2 pt-2 space-y-1">
                    <a href="<?= APP_URL ?>/"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                              text-[var(--color-bg-hover)] hover:text-white hover:bg-[var(--color-bg-surface-hover)] transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Ver tienda
                    </a>
                    <a href="<?= APP_URL ?>/logout"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                              text-[var(--color-error)] hover:text-white hover:bg-[var(--color-error-bg)] transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar sesión
                    </a>
                </div>
            </nav>
        </div>

        <!-- Alertas globales -->
        <div class="px-4 md:px-8 pt-6">
            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 alert-success rounded-xl text-sm">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 alert-error rounded-xl text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contenido -->
        <main class="flex-1 px-4 md:px-8 pb-8">
            <?= $content ?>
        </main>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggle       = document.getElementById('adminMenuToggle');
    var menu         = document.getElementById('adminMobileMenu');
    var hamburger    = document.getElementById('adminHamburgerIcon');
    var closeIcon    = document.getElementById('adminCloseIcon');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', function () {
        var isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        hamburger.classList.toggle('hidden', !isOpen);
        closeIcon.classList.toggle('hidden', isOpen);
    });

    // Cierra al pulsar fuera
    document.addEventListener('click', function (e) {
        if (!menu.classList.contains('hidden') &&
            !menu.contains(e.target) &&
            !toggle.contains(e.target)) {
            menu.classList.add('hidden');
            hamburger.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });
});
</script>
</body>
</html>
