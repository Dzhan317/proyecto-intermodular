<?php
ob_start();
?>
<!-- Cabecera -->
<div class="bg-gradient-to-r from-[var(--color-bg-secondary)] to-[var(--color-bg-card)]
            border border-[var(--color-border)] rounded-2xl p-8 md:p-10 mb-10">
    <p class="text-[var(--color-accent)] text-sm font-semibold uppercase tracking-[0.2em] mb-3">
        Legal
    </p>
    <h1 class="text-3xl md:text-4xl font-bold text-[var(--color-text-primary)] mb-4 leading-tight">
        Política de cookies
    </h1>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Última actualización: <?= date('d/m/Y') ?>
    </p>
</div>

<!-- Contenido -->
<div class="space-y-6 mb-10">

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">¿Qué son las cookies?</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Las cookies son pequeños archivos de texto que se almacenan en tu navegador cuando visitas una web. Sirven para recordar tus preferencias y mantener tu sesión activa entre páginas.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Cookies que utilizamos</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-border)]">
                        <th class="text-left py-3 pr-4 text-[var(--color-text-primary)] font-semibold">Nombre</th>
                        <th class="text-left py-3 pr-4 text-[var(--color-text-primary)] font-semibold">Tipo</th>
                        <th class="text-left py-3 pr-4 text-[var(--color-text-primary)] font-semibold">Duración</th>
                        <th class="text-left py-3 text-[var(--color-text-primary)] font-semibold">Finalidad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    <tr>
                        <td class="py-3 pr-4 text-[var(--color-text-primary)] font-mono text-xs">primelux_session</td>
                        <td class="py-3 pr-4 text-[var(--color-text-secondary)]">Técnica</td>
                        <td class="py-3 pr-4 text-[var(--color-text-secondary)]">7 días</td>
                        <td class="py-3 text-[var(--color-text-secondary)]">Mantiene tu sesión iniciada y el contenido del carrito.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Cookies de terceros</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Durante el proceso de pago, Stripe puede establecer sus propias cookies técnicas necesarias para procesar la transacción de forma segura. Estas cookies están sujetas a la política de privacidad de Stripe.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">¿Cómo desactivar las cookies?</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Puedes configurar tu navegador para rechazar o eliminar cookies. Ten en cuenta que desactivar la cookie de sesión impedirá que puedas iniciar sesión o usar el carrito de compra.
        </p>
    </div>

</div>

<!-- Navegación legal -->
<div class="flex flex-wrap gap-3">
    <a href="<?= APP_URL ?>/legal/privacy"
       class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
        Política de privacidad →
    </a>
    <span class="text-[var(--color-text-disabled)]">·</span>
    <a href="<?= APP_URL ?>/legal/terms"
       class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
        Términos y condiciones →
    </a>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
