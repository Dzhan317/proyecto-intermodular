<?php
ob_start();
?>
<section class="mb-10">
    <div class="bg-gradient-to-r from-[var(--color-bg-secondary)] to-[var(--color-bg-card)]
                border border-[var(--color-border)] rounded-2xl p-8 md:p-10">
        <p class="text-[var(--color-accent)] text-sm font-semibold uppercase tracking-[0.2em] mb-3">
            PrimeLux SmartShop
        </p>
        <h1 class="text-3xl md:text-4xl font-bold text-[var(--color-text-primary)] mb-4 leading-tight">
            Tecnología premium con enfoque académico,
            diseño moderno y una experiencia de compra clara.
        </h1>
        <p class="max-w-3xl text-[var(--color-text-secondary)] leading-7">
            PrimeLux SmartShop es un e-commerce académico y multicategoría desarrollado para aplicar de forma práctica
            los conceptos del C.F.G.S. de Desarrollo de Aplicaciones Web. El proyecto combina catálogo, navegación,
            autenticación y una base visual coherente con una idea clara: construir una tienda online seria,
            cuidada y defendible dentro del tiempo real del proyecto.
        </p>
    </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <article class="lg:col-span-2 bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Qué es PrimeLux SmartShop</h2>
        <div class="space-y-4 text-[var(--color-text-secondary)] leading-7">
            <p>
                PrimeLux SmartShop es una tienda online orientada a tecnología, con una propuesta visual premium y una
                estructura pensada para crecer por fases. Actualmente integra categorías relacionadas con el ámbito de DAW,
                como componentes, electrónica, informática, periféricos, redes y software.
            </p>
            <p>
                El objetivo no es vender por vender, sino demostrar una implementación sólida de un e-commerce académico:
                rutas ordenadas, separación por controladores, vistas coherentes, uso de PHP con MVC, base de datos,
                variables CSS globales y soporte visual con Tailwind para agilizar el desarrollo.
            </p>
        </div>
    </article>

    <aside class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Contacto</h2>
        <dl class="space-y-4 text-sm">
            <div>
                <dt class="text-[var(--color-text-muted)] mb-1">Correo</dt>
                <dd class="text-[var(--color-text-primary)]">admin@primeluxshop.es</dd>
            </div>
            <div>
                <dt class="text-[var(--color-text-muted)] mb-1">Ubicación</dt>
                <dd class="text-[var(--color-text-primary)]">Segovia, España</dd>
            </div>
            <div>
                <dt class="text-[var(--color-text-muted)] mb-1">Teléfono</dt>
                <dd class="text-[var(--color-text-primary)]">+34 921 000 247</dd>
            </div>
            <div>
                <dt class="text-[var(--color-text-muted)] mb-1">Horario</dt>
                <dd class="text-[var(--color-text-primary)]">Lunes a viernes · 09:00 a 14:00 · 16:00 a 19:00</dd>
            </div>
        </dl>
    </aside>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Diseño moderno</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            La interfaz prioriza claridad, jerarquía visual y consistencia entre vistas. La paleta, la navegación y los
            componentes se apoyan en una base común para evitar improvisaciones visuales.
        </p>
    </div>
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Seguridad básica real</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            El proyecto incorpora medidas básicas enfocadas a usuario y sesión, con un planteamiento realista para el
            alcance disponible. No vende humo: aplica seguridad útil sin complicar innecesariamente la base del sistema.
        </p>
    </div>
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Enfoque por fases</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            PrimeLux SmartShop se ha planteado por etapas, priorizando primero catálogo, navegación, autenticación,
            perfil de usuario y coherencia visual antes de extender funciones más avanzadas.
        </p>
    </div>
</section>

<section class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
    <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Categorías actuales</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <?php foreach (['Componentes', 'Electrónica', 'Informática', 'Periféricos', 'Redes', 'Software'] as $item): ?>
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-secondary)] px-4 py-3 text-sm font-medium text-[var(--color-text-primary)] text-center">
                <?= htmlspecialchars($item) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-[var(--color-text-secondary)] leading-7">
        La tienda está pensada para transmitir confianza, orden y una propuesta tecnológica clara. El objetivo es que la
        experiencia resulte moderna, directa y creíble, sin elementos vacíos ni secciones que todavía no estén realmente implementadas.
    </p>
</section>
<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
