<?php
/*
 * Cabecera global — páginas autenticadas y públicas.
 * Navegación principal simplificada para Fase 4.
 */
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$supportEnabled = class_exists('SupportController') || file_exists(APP_PATH . '/Controllers/SupportController.php');
$cartEnabled = class_exists('CartController') || file_exists(APP_PATH . '/Controllers/CartController.php');

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
?>
<header class="bg-[var(--color-bg-secondary)] border-b border-[var(--color-divider)] sticky top-0 z-[80] overflow-visible">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
            <a href="<?= APP_URL ?>/" class="flex-shrink-0">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_principal.png"
                     alt="PrimeLux SmartShop"
                     class="h-20 w-auto">
            </a>

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
                                  d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </span>
                <?php endif; ?>

                <a href="<?= APP_URL ?>/profile" title="Mi perfil"
                   class="p-2 rounded-xl transition-colors <?= str_contains($currentUri, '/profile') ? 'text-[var(--color-brand)] bg-[var(--color-bg-card)]' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                <?php if ($cartEnabled): ?>
                    <a href="<?= APP_URL ?>/cart" title="Carrito"
                       class="relative p-2 rounded-xl text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)] transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </a>
                <?php else: ?>
                    <span title="Carrito disponible en la siguiente fase" class="relative p-2 rounded-xl text-[var(--color-text-disabled)] cursor-not-allowed opacity-70">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="border-t border-[var(--color-divider)] bg-[var(--color-bg-main)] relative z-[80] overflow-visible">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 overflow-visible">
          <ul class="flex items-center gap-3 overflow-x-auto md:overflow-visible scrollbar-hide py-3">
              <li class="flex-shrink-0">
                  <a href="<?= APP_URL ?>/"
                     class="block px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap <?= $isHome ? 'bg-[var(--color-brand)] text-white' : 'bg-transparent text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                      Inicio
                  </a>
              </li>

              <li class="relative flex-shrink-0 overflow-visible" id="categoriesDropdownWrapper">
                  <button type="button"
                          id="categoriesDropdownButton"
                          aria-expanded="false"
                          aria-controls="categoriesDropdownMenu"
                          class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap <?= $isCat ? 'bg-[var(--color-bg-hover)] text-white' : 'text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                      Categorías

                      <svg id="categoriesDropdownIcon"
                           class="w-4 h-4 transition-transform"
                           fill="none"
                           stroke="currentColor"
                           viewBox="0 0 24 24">
                          <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"/>
                      </svg>
                  </button>

                  <?php if (!empty($navCategories)): ?>
                      <div id="categoriesDropdownMenu"
                           class="absolute left-0 top-full mt-2 hidden z-[120]">
                          <div class="min-w-[240px] rounded-2xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-2xl shadow-black/40 overflow-hidden ring-1 ring-black/10">
                              <?php foreach ($navCategories as $cat): ?>
                                  <a href="<?= APP_URL ?>/category/<?= htmlspecialchars($cat['slug']) ?>"
                                     class="block px-4 py-3 text-sm text-[var(--color-text-secondary)] hover:bg-[var(--color-bg-hover)] hover:text-[var(--color-text-primary)] transition-colors <?= str_contains($currentUri, '/category/' . $cat['slug']) ? 'bg-[var(--color-bg-hover)] text-[var(--color-text-primary)]' : '' ?>">
                                      <?= htmlspecialchars($cat['name']) ?>
                                  </a>
                              <?php endforeach; ?>
                          </div>
                      </div>
                  <?php endif; ?>
              </li>

              <li class="flex-shrink-0">
                  <a href="<?= APP_URL ?>/sobre-nosotros"
                     class="block px-5 py-2.5 rounded-xl text-sm font-medium transition-colors whitespace-nowrap <?= $isAbout ? 'bg-[var(--color-brand)] text-white' : 'bg-transparent text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]' ?>">
                      Sobre nosotros
                  </a>
              </li>
          </ul>
      </div>
  </nav>
  
  <script src="<?= APP_URL ?>/assets/js/header-dropdown.js"></script>
</header>
