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
        Términos y condiciones
    </h1>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Última actualización: <?= date('d/m/Y') ?>
    </p>
</div>

<!-- Contenido -->
<div class="space-y-6 mb-10">

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">1. Aceptación de los términos</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Al acceder y utilizar PrimeLux SmartShop, aceptas quedar vinculado por estos términos y condiciones. Si no estás de acuerdo con alguna parte, te rogamos que no utilices el servicio.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">2. Registro y cuenta de usuario</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Para realizar compras es necesario crear una cuenta. Al registrarte, te comprometes a:</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Proporcionar información veraz y actualizada</li>
                <li>Mantener la confidencialidad de tus credenciales</li>
                <li>Notificarnos cualquier uso no autorizado de tu cuenta</li>
            </ul>
            <p>Nos reservamos el derecho de suspender cuentas que incumplan estos términos.</p>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">3. Precios y pagos</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Todos los precios mostrados incluyen el IVA aplicable. Los pagos se procesan de forma segura a través de Stripe. Una vez confirmado el pago, recibirás una confirmación por correo electrónico.</p>
            <p>Nos reservamos el derecho de modificar los precios en cualquier momento. Los cambios no afectarán a pedidos ya confirmados.</p>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">4. Envíos y entregas</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Los plazos de entrega son orientativos y pueden variar según la disponibilidad del producto y la zona de entrega. PrimeLux SmartShop no se responsabiliza de retrasos ocasionados por causas ajenas a su control.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">5. Devoluciones</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Dispones de 14 días naturales desde la recepción del pedido para ejercer tu derecho de desistimiento, conforme a la Ley General para la Defensa de los Consumidores y Usuarios. El producto debe devolverse en su estado original.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">6. Limitación de responsabilidad</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            PrimeLux SmartShop no se hace responsable de los daños derivados del uso incorrecto del servicio, de interrupciones temporales por mantenimiento, ni de circunstancias ajenas a nuestro control. Nos esforzamos por mantener el servicio disponible y en buen funcionamiento, pero no podemos garantizar un acceso ininterrumpido en todo momento.
        </p>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">7. Legislación aplicable</h2>
        <p class="text-[var(--color-text-secondary)] text-sm leading-7">
            Estos términos se rigen por la legislación española. Para cualquier controversia, las partes se someten a los juzgados y tribunales de Segovia, España.
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
    <a href="<?= APP_URL ?>/legal/cookies"
       class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
        Política de cookies →
    </a>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
