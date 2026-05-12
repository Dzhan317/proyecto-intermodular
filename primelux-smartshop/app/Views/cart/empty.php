<?php
/*
 * Estado vacío del carrito.
 * Diseño basado en docs/designs/cart/carrito_vacio.png.
 * Se incluye desde cart/index.php cuando no hay ítems.
 */
?>
<div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] py-16 px-8">

    <!-- Cabecera -->
    <div class="text-center border-b border-[var(--color-border)] pb-8 mb-8">
        <h2 class="text-sm font-semibold text-[var(--color-text-primary)] uppercase tracking-wider">
            Carrito
        </h2>
    </div>

    <!-- Estado vacío -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-10">
        <p class="text-[var(--color-text-primary)] text-lg">
            Tu carrito está vacío
        </p>
        <div class="w-16 h-16 bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                    rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-8 h-8 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                         1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
    </div>

    <p class="text-center text-[var(--color-text-secondary)] text-sm mb-10">
        Añade productos para empezar tu compra
    </p>

    <div class="flex justify-center">
        <a href="<?= APP_URL ?>/"
           class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                  text-white font-semibold px-10 py-3 rounded-xl text-sm
                  transition-colors uppercase tracking-wider">
            Ir a comprar
        </a>
    </div>

</div>
