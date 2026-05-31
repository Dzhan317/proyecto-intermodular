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
        Política de privacidad
    </h1>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Última actualización: <?= date('d/m/Y') ?>
    </p>
</div>

<!-- Contenido -->
<div class="space-y-6 mb-10">

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">1. Responsable del tratamiento</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>El responsable del tratamiento de los datos personales recogidos en esta web es <strong class="text-[var(--color-text-primary)]">PrimeLux SmartShop</strong>, con domicilio en Segovia, España, y correo de contacto <strong class="text-[var(--color-text-primary)]">soporte@primeluxshop.es</strong>.</p>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">2. Datos que recogemos</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Al crear una cuenta o realizar una compra, recogemos los siguientes datos:</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Nombre y apellidos</li>
                <li>Dirección de correo electrónico</li>
                <li>Dirección de envío</li>
                <li>Historial de pedidos</li>
            </ul>
            <p>No almacenamos datos de tarjetas de pago. Los pagos son procesados íntegramente por Stripe, que dispone de su propia política de privacidad.</p>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">3. Finalidad del tratamiento</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Los datos recogidos se utilizan exclusivamente para:</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Gestionar tu cuenta de usuario</li>
                <li>Procesar y gestionar tus pedidos</li>
                <li>Enviarte comunicaciones relacionadas con tus compras</li>
                <li>Atender consultas y solicitudes de soporte</li>
            </ul>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">4. Conservación de los datos</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Los datos se conservarán mientras mantengas tu cuenta activa o mientras sean necesarios para la prestación del servicio. Puedes solicitar la eliminación de tu cuenta contactando con nosotros.</p>
        </div>
    </div>

    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">5. Tus derechos</h2>
        <div class="space-y-3 text-[var(--color-text-secondary)] text-sm leading-7">
            <p>Conforme al Reglamento General de Protección de Datos (RGPD), tienes derecho a:</p>
            <ul class="list-disc list-inside space-y-1 ml-2">
                <li>Acceder a tus datos personales</li>
                <li>Rectificar datos inexactos</li>
                <li>Solicitar la supresión de tus datos</li>
                <li>Oponerte al tratamiento</li>
                <li>Solicitar la portabilidad de tus datos</li>
            </ul>
            <p>Para ejercer cualquiera de estos derechos, contacta con nosotros en <strong class="text-[var(--color-text-primary)]">soporte@primeluxshop.es</strong>.</p>
        </div>
    </div>

</div>

<!-- Navegación legal -->
<div class="flex flex-wrap gap-3">
    <a href="<?= APP_URL ?>/legal/cookies"
       class="text-sm text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
        Política de cookies →
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
