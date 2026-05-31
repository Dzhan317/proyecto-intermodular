<?php
ob_start();
?>
<!-- Cabecera -->
<div class="bg-gradient-to-r from-[var(--color-bg-secondary)] to-[var(--color-bg-card)]
            border border-[var(--color-border)] rounded-2xl p-8 md:p-10 mb-10">
    <p class="text-[var(--color-accent)] text-sm font-semibold uppercase tracking-[0.2em] mb-3">
        Ayuda
    </p>
    <h1 class="text-3xl md:text-4xl font-bold text-[var(--color-text-primary)] mb-4 leading-tight">
        Envíos y devoluciones
    </h1>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Toda la información sobre los métodos de envío disponibles, plazos de entrega y cómo gestionar una devolución.
    </p>
</div>

<!-- Métodos de envío -->
<div class="mb-10">
    <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-6">Métodos de envío</h2>

    <div class="space-y-4">

        <!-- Envío estándar -->
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[var(--color-bg-secondary)] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-[var(--color-text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 10a2 2 0 002 2h8a2 2 0 002-2L19 8"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-1">Envío estándar</h3>
                    <p class="text-sm text-[var(--color-text-secondary)]">Entrega en 2-4 días laborables en la península.</p>
                </div>
            </div>
            <span class="text-[var(--color-accent)] font-semibold text-sm flex-shrink-0">Gratis</span>
        </div>

        <!-- Envío express -->
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[var(--color-bg-secondary)] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-[var(--color-text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-1">Envío express</h3>
                    <p class="text-sm text-[var(--color-text-secondary)]">Entrega en 24 horas para pedidos realizados antes de las 14:00 h.</p>
                </div>
            </div>
            <span class="text-[var(--color-accent)] font-semibold text-sm flex-shrink-0">4,99 €</span>
        </div>

        <!-- Recogida en tienda -->
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-[var(--color-bg-secondary)] flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-[var(--color-text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-1">Recogida en tienda</h3>
                    <p class="text-sm text-[var(--color-text-secondary)]">Disponible en 24 horas en nuestra tienda de Segovia.</p>
                </div>
            </div>
            <span class="text-[var(--color-accent)] font-semibold text-sm flex-shrink-0">Gratis</span>
        </div>

    </div>
</div>

<!-- Devoluciones -->
<div class="mb-10">
    <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-6">Devoluciones</h2>

    <div class="space-y-4">

        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
            <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-2">Plazo de devolución</h3>
            <p class="text-sm text-[var(--color-text-secondary)] leading-7">
                Dispones de <strong class="text-[var(--color-text-primary)]">14 días naturales</strong> desde la recepción del pedido para solicitar una devolución, conforme a la Ley General para la Defensa de los Consumidores y Usuarios.
            </p>
        </div>

        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
            <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-2">Condiciones</h3>
            <ul class="text-sm text-[var(--color-text-secondary)] leading-7 list-disc list-inside space-y-1 ml-2">
                <li>El producto debe estar en su estado original y sin usar</li>
                <li>Debe conservarse el embalaje original</li>
                <li>No se aceptan devoluciones de productos personalizados o perecederos</li>
            </ul>
        </div>

        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
            <h3 class="text-base font-semibold text-[var(--color-text-primary)] mb-2">Cómo iniciar una devolución</h3>
            <p class="text-sm text-[var(--color-text-secondary)] leading-7">
                Contacta con nuestro equipo de soporte desde tu cuenta indicando el número de pedido y el motivo de la devolución. Te guiaremos durante todo el proceso.
            </p>
        </div>

    </div>
</div>

<!-- CTA soporte -->
<div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8 text-center">
    <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-2">
        ¿Necesitas ayuda con tu pedido?
    </h3>
    <p class="text-[var(--color-text-secondary)] text-sm mb-6">
        Nuestro equipo de soporte está disponible para gestionar cualquier incidencia.
    </p>
    <a href="<?= APP_URL ?>/support"
       class="inline-flex items-center gap-2 bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
              text-white font-semibold px-6 py-3 rounded-xl text-sm transition-colors">
        Contactar con soporte
    </a>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
