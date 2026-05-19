<?php
/*
 * Cabecera global — páginas autenticadas y públicas.
 * Incluye badge con contador de ítems del carrito.
 * Incluye desplegable de usuario con acceso a perfil, pedidos, admin y logout.
 */
$currentUri     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$supportEnabled = class_exists('SupportController') || file_exists(APP_PATH . '/Controllers/SupportController.php');
$cartEnabled    = class_exists('CartController')    || file_exists(APP_PATH . '/Controllers/CartController.php');
$ordersEnabled  = class_exists('OrderController')   || file_exists(APP_PATH . '/Controllers/OrderController.php');
$adminEnabled   = class_exists('AdminController')   || file_exists(APP_PATH . '/Controllers/AdminController.php');

// Badge del carrito — solo si el usuario está autenticado
$cartCount = 0;
if ($cartEnabled && isset($_SESSION['user_id'])) {
    require_once APP_PATH . '/Controllers/CartController.php';
    $cartCount = CartController::getItemCount();
}

$navCategories = [];
try {
    if (!class_exists('CategoryModel')) {
        require_once APP_PATH . '/Models/CategoryModel.php';
    }
    $navCategories = (new CategoryModel())->getAll();
} catch (\Throwable $e) {
    $navCategories = [];
}

$isHome  = ($currentUri === '/');
$isAbout = ($currentUri === '/sobre-nosotros');
$isCat   = str_starts_with($currentUri, '/category/');

// Datos del usuario para el desplegable
$headerUserName = '';
if (isset($_SESSION['user_id'])) {
    $headerUserName = trim(($_SESSION['user_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? ''));
    if (empty($headerUserName)) {
        $headerUserName = $_SESSION['user_email'] ?? '';
    }
}
$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
?>
<header class="bg-[var(--color-bg-secondary)] border-b border-[var(--color-divider)] sticky top-0 z-[80] overflow-visible">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
            <a href="<?= APP_URL ?>/" class="flex-shrink-0">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_principal_header.webp"
                     alt="PrimeLux SmartShop"
                     class="h-20 w-auto">
            </a>

            <!-- Buscador desktop — visible solo en md+ -->
            <form method="GET" action="<?= APP_URL ?>/products" class="flex-1 max-w-xl hidden md:block">
                <div class="relative">
                    <input type="text" name="q" placeholder="Buscar productos..."
                           class="w-full bg-[var(--color-bg-card)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                  border border-[var(--color-border)] rounded-xl pl-4 pr-10 py-2.5 text-sm
                                  focus:outline-none focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)] transition-colors">
                    <button type="submit"
                            class="absolute inset-y-0 right-3 flex items-center text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors"
                            aria-label="Buscar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-1">

                <!-- Botón lupa móvil — visible solo en <md -->
                <button type="button"
                        id="mobileSearchToggle"
                        aria-label="Abrir buscador"
                        aria-expanded="false"
                        class="md:hidden p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- Soporte -->
                <?php if ($supportEnabled): ?>
                    <a href="<?= APP_URL ?>/support" title="Soporte"
                       class="p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <span title="Soporte próximamente" class="p-2 rounded-xl text-[var(--color-text-disabled)] cursor-not-allowed opacity-70">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2h-5l-5 5v-5z"/>
                        </svg>
                    </span>
                <?php endif; ?>

                <!-- Desplegable de usuario -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative" id="userDropdownWrapper">
                        <button type="button"
                                id="userDropdownButton"
                                aria-expanded="false"
                                aria-controls="userDropdownMenu"
                                class="p-2 rounded-xl transition-colors
                                       <?= str_contains($currentUri, '/profile') || str_contains($currentUri, '/admin')
                                           ? 'text-[var(--color-brand)] bg-[var(--color-bg-card)]'
                                           : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>"
                                title="Mi cuenta">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </button>

                        <div id="userDropdownMenu"
                             class="absolute right-0 top-full mt-2 hidden z-[200]">
                            <div class="min-w-[220px] rounded-2xl border border-[var(--color-border)]
                                        bg-[var(--color-bg-card)] shadow-2xl shadow-black/40
                                        overflow-hidden ring-1 ring-black/10">

                                <!-- Cabecera del desplegable — nombre del usuario -->
                                <div class="px-4 py-3 border-b border-[var(--color-border)]">
                                    <p class="text-xs text-[var(--color-text-muted)] mb-0.5">Conectado como</p>
                                    <p class="text-sm font-semibold text-[var(--color-text-primary)] truncate">
                                        <?= htmlspecialchars($headerUserName) ?>
                                    </p>
                                </div>

                                <!-- Opciones del cliente -->
                                <div class="py-1">
                                    <a href="<?= APP_URL ?>/profile"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm
                                              text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                              hover:bg-[var(--color-bg-hover)] transition-colors">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Mi perfil
                                    </a>

                                    <?php if ($ordersEnabled): ?>
                                        <a href="<?= APP_URL ?>/orders"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm
                                                  text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]
                                                  hover:bg-[var(--color-bg-hover)] transition-colors">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Mis pedidos
                                        </a>
                                    <?php else: ?>
                                        <span class="flex items-center gap-3 px-4 py-2.5 text-sm
                                                     text-[var(--color-text-disabled)] cursor-not-allowed select-none">
                                            <svg class="w-4 h-4 flex-shrink-0 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Mis pedidos
                                            <span class="ml-auto text-xs bg-[var(--color-bg-secondary)]
                                                         text-[var(--color-text-disabled)]
                                                         px-2 py-0.5 rounded-full">
                                                Próximamente
                                            </span>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Panel admin — solo visible para admins -->
                                <?php if ($isAdmin): ?>
                                    <div class="border-t border-[var(--color-border)] py-1">
                                        <a href="<?= APP_URL ?>/admin"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm
                                                  text-[var(--color-warning)] hover:text-white
                                                  hover:bg-[var(--color-bg-hover)] transition-colors">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Panel de administración
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Cerrar sesión -->
                                <div class="border-t border-[var(--color-border)] py-1">
                                    <a href="<?= APP_URL ?>/logout"
                                       class="flex items-center gap-3 px-4 py-2.5 text-sm
                                              text-[var(--color-error)] hover:text-white
                                              hover:bg-[var(--color-error-bg)] transition-colors">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Cerrar sesión
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Usuario no autenticado — enlace directo al login -->
                    <a href="<?= APP_URL ?>/login" title="Iniciar sesión"
                       class="p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                <?php endif; ?>

                <!-- Carrito con badge -->
                <?php if ($cartEnabled): ?>
                    <a href="<?= APP_URL ?>/cart" title="Carrito"
                       class="relative p-2 rounded-xl transition-colors
                              <?= str_contains($currentUri, '/cart')
                                  ? 'text-[var(--color-brand)] bg-[var(--color-bg-card)]'
                                  : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                     1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <?php if ($cartCount > 0): ?>
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1
                                         bg-[var(--color-error)] text-white text-[10px] font-bold
                                         rounded-full flex items-center justify-center leading-none">
                                <?= $cartCount > 99 ? '99+' : $cartCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <span title="Carrito disponible próximamente"
                          class="relative p-2 rounded-xl text-[var(--color-text-disabled)] cursor-not-allowed opacity-70">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                     1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <nav class="border-t border-[var(--color-divider)] bg-[var(--color-bg-main)] relative z-[80] overflow-visible">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <ul class="flex items-center justify-center gap-3 overflow-visible scrollbar-hide py-3">

                <li class="flex-shrink-0">
                    <a href="<?= APP_URL ?>/"
                       class="block px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap
                              <?= $isHome
                                  ? 'bg-[var(--color-brand)] text-white'
                                  : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                        Inicio
                    </a>
                </li>

                <li class="relative flex-shrink-0" id="categoriesDropdownWrapper">
                    <button type="button"
                            id="categoriesDropdownButton"
                            aria-expanded="false"
                            aria-controls="categoriesDropdownMenu"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium
                                   transition-colors whitespace-nowrap
                                   <?= $isCat
                                       ? 'bg-[var(--color-bg-hover)] text-white'
                                       : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                        Categorías
                        <svg id="categoriesDropdownIcon"
                             class="w-4 h-4 transition-transform"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <?php if (!empty($navCategories)): ?>
                        <div id="categoriesDropdownMenu"
                             class="absolute left-0 top-full mt-2 hidden z-[200]">
                            <div class="min-w-[240px] max-h-80 overflow-y-auto rounded-2xl border border-[var(--color-border)]
                                bg-[var(--color-bg-card)] shadow-2xl shadow-black/40
                                    ring-1 ring-black/10">
                                <?php foreach ($navCategories as $cat): ?>
                                    <a href="<?= APP_URL ?>/category/<?= htmlspecialchars($cat['slug']) ?>"
                                       class="block px-4 py-3 text-sm text-[var(--color-text-secondary)]
                                              hover:bg-[var(--color-bg-hover)] hover:text-[var(--color-text-primary)]
                                              transition-colors
                                              <?= str_contains($currentUri, '/category/' . $cat['slug'])
                                                  ? 'bg-[var(--color-bg-hover)] text-[var(--color-text-primary)]'
                                                  : '' ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </li>

                <li class="flex-shrink-0">
                    <a href="<?= APP_URL ?>/sobre-nosotros"
                       class="block px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap
                              <?= $isAbout
                                  ? 'bg-[var(--color-brand)] text-white'
                                  : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                        Sobre nosotros
                    </a>
                </li>

            </ul>
        </div>
    </nav>

    <!-- Panel buscador móvil — visible solo en <md, oculto por defecto -->
    <div id="mobileSearchPanel"
         class="hidden md:hidden border-t border-[var(--color-divider)] bg-[var(--color-bg-secondary)] px-4 py-3">
        <form method="GET" action="<?= APP_URL ?>/products">
            <div class="relative">
                <input id="mobileSearchInput"
                       type="text" name="q"
                       placeholder="Buscar productos..."
                       autocomplete="off"
                       class="w-full bg-[var(--color-bg-card)] text-[var(--color-text-primary)]
                              placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                              rounded-xl pl-4 pr-10 py-2.5 text-sm
                              focus:outline-none focus:border-[var(--color-brand)]
                              focus:ring-1 focus:ring-[var(--color-brand)] transition-colors">
                <button type="submit"
                        class="absolute inset-y-0 right-3 flex items-center text-[var(--color-text-muted)]
                               hover:text-[var(--color-text-primary)] transition-colors"
                        aria-label="Buscar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script src="<?= APP_URL ?>/assets/js/header-dropdown.js"></script>

    <script>
    /* ── Buscador móvil — toggle del panel expandible ── */
    document.addEventListener('DOMContentLoaded', function () {
        var toggleBtn = document.getElementById('mobileSearchToggle');
        var panel     = document.getElementById('mobileSearchPanel');
        var input     = document.getElementById('mobileSearchInput');

        if (!toggleBtn || !panel) return;

        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = !panel.classList.contains('hidden');

            if (isOpen) {
                panel.classList.add('hidden');
                toggleBtn.setAttribute('aria-expanded', 'false');
            } else {
                panel.classList.remove('hidden');
                toggleBtn.setAttribute('aria-expanded', 'true');
                if (input) input.focus();
            }
        });

        // Cierra al pulsar Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
                toggleBtn.setAttribute('aria-expanded', 'false');
                toggleBtn.focus();
            }
        });
    });
    </script>
</header>
