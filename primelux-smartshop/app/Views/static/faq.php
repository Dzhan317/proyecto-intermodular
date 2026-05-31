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
        Preguntas frecuentes
    </h1>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Resolvemos las dudas más habituales sobre pedidos, pagos, envíos y tu cuenta en PrimeLux SmartShop.
    </p>
</div>

<!-- Preguntas -->
<div class="space-y-4 mb-10">

    <?php
    $faqs = [
        [
            'q' => '¿Cómo realizo un pedido?',
            'a' => 'Navega por el catálogo, añade los productos que quieras al carrito y sigue el proceso de checkout. Necesitarás una cuenta registrada para finalizar la compra.',
        ],
        [
            'q' => '¿Qué métodos de pago aceptáis?',
            'a' => 'Aceptamos pagos con tarjeta de crédito y débito (Visa, Mastercard, American Express) a través de Stripe, una plataforma de pago segura y certificada.',
        ],
        [
            'q' => '¿Puedo cancelar o modificar mi pedido?',
            'a' => 'Una vez confirmado el pago, el pedido entra en proceso de preparación. Si necesitas hacer algún cambio, contacta con nosotros lo antes posible a través del soporte.',
        ],
        [
            'q' => '¿Cuánto tarda en llegar mi pedido?',
            'a' => 'Los plazos de entrega habituales son de 2 a 5 días laborables para la península. Para Canarias, Baleares y envíos internacionales el plazo puede ser mayor.',
        ],
        [
            'q' => '¿Cómo puedo hacer una devolución?',
            'a' => 'Dispones de 14 días naturales desde la recepción del pedido para solicitar una devolución. El producto debe estar en su estado original y con el embalaje intacto. Contacta con soporte para iniciar el proceso.',
        ],
        [
            'q' => '¿Es seguro comprar en PrimeLux SmartShop?',
            'a' => 'Sí. La tienda opera bajo HTTPS, los pagos se procesan íntegramente a través de Stripe y nunca almacenamos datos de tarjetas. Además, las cuentas están protegidas con autenticación en dos pasos.',
        ],
        [
            'q' => '¿Cómo activo la verificación en dos pasos?',
            'a' => 'La verificación en dos pasos se activa automáticamente en cada inicio de sesión. Recibirás un código de 6 dígitos en tu correo electrónico que deberás introducir para acceder.',
        ],
        [
            'q' => '¿Puedo consultar mis pedidos anteriores?',
            'a' => 'Sí. Accede a tu cuenta y entra en la sección "Mis pedidos" desde el menú de usuario. Allí encontrarás el historial completo con el estado de cada pedido.',
        ],
    ];
    ?>

    <?php foreach ($faqs as $i => $faq): ?>
        <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
            <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-2">
                <?= htmlspecialchars($faq['q']) ?>
            </h2>
            <p class="text-[var(--color-text-secondary)] text-sm leading-7">
                <?= htmlspecialchars($faq['a']) ?>
            </p>
        </div>
    <?php endforeach; ?>

</div>

<!-- CTA soporte -->
<div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8 text-center">
    <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-2">
        ¿No encuentras lo que buscas?
    </h3>
    <p class="text-[var(--color-text-secondary)] text-sm mb-6">
        Nuestro equipo de soporte está disponible para ayudarte con cualquier consulta.
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
