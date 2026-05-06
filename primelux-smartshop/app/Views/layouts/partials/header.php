<?php
/*
 * Cabecera global — páginas autenticadas.
 * Logo secundario (bolsa), búsqueda placeholder Fase 4 e iconos de navegación.
 */
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<header class="bg-[#111827] border-b border-[#1F2937] sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">

            <!-- Logo secundario -->
            <a href="<?= APP_URL ?>/" class="flex-shrink-0">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_secundario.png"
                     alt="PrimeLux SmartShop" class="h-10 w-auto">
            </a>

            <!-- Búsqueda — activa en Fase 4 -->
            <div class="flex-1 max-w-xl">
                <div class="relative">
                    <input type="text" placeholder="Buscar productos..." disabled
                           class="w-full bg-[#1F2937] text-[#6B7280] placeholder-[#6B7280]
                                  border border-[#374151] rounded-xl pl-4 pr-10 py-2.5 text-sm
                                  cursor-not-allowed opacity-60">
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Iconos de acción -->
            <div class="flex items-center gap-1">

                <!-- Soporte -->
                <a href="<?= APP_URL ?>/support" title="Soporte"
                   class="p-2 rounded-xl text-[#9CA3AF] hover:text-white hover:bg-[#1F2937] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2
                                 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </a>

                <!-- Perfil -->
                <a href="<?= APP_URL ?>/profile" title="Mi perfil"
                   class="p-2 rounded-xl transition-colors
                          <?= str_contains($currentUri, '/profile')
                              ? 'text-[#2563EB] bg-[#1F2937]'
                              : 'text-[#9CA3AF] hover:text-white hover:bg-[#1F2937]' ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>

                <!-- Carrito -->
                <a href="<?= APP_URL ?>/cart" title="Carrito"
                   class="p-2 rounded-xl text-[#9CA3AF] hover:text-white hover:bg-[#1F2937] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>

            </div>
        </div>
    </div>
</header>
