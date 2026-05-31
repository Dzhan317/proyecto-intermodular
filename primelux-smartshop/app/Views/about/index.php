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
        <p class="text-[var(--color-text-secondary)] leading-7">
            PrimeLux SmartShop nació como proyecto final del C.F.G.S. de Desarrollo de Aplicaciones Web,
            con un objetivo claro: construir una tienda online real, funcional y defendible, no solo un ejercicio.
            Catálogo, autenticación, pagos, soporte y una interfaz cuidada, todo desarrollado desde cero.
        </p>
    </div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
    <article class="lg:col-span-2 bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Qué es PrimeLux SmartShop</h2>
        <div class="space-y-4 text-[var(--color-text-secondary)] leading-7">
            <p>
                PrimeLux SmartShop es un e-commerce académico y multicategoría con una propuesta visual cuidada
                y una arquitectura pensada para crecer por fases. Cada parte del proyecto tiene un motivo:
                nada está puesto por rellenar.
            </p>
            <p>
                Por dentro, el proyecto aplica un patrón MVC propio sin frameworks, con rutas limpias,
                controladores separados por responsabilidad, vistas coherentes entre sí y una base de datos
                estructurada. Tailwind CSS gestiona el diseño y las variables CSS globales mantienen
                la coherencia visual en toda la aplicación.
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

        </dl>
    </aside>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Diseño moderno</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            La interfaz está pensada para que cada pantalla se sienta parte del mismo sitio. Paleta de color,
            tipografía, espaciado y componentes comparten una base común que da coherencia visual
            a toda la aplicación, sin depender del azar.
        </p>
    </div>
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Seguridad básica real</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            El proyecto incorpora medidas reales enfocadas a usuario y sesión: hashing de contraseñas,
            protección CSRF, autenticación en dos pasos, gestión de inactividad por rol y cookies seguras
            con SameSite Lax. Un planteamiento sólido y justificable dentro del alcance del proyecto.
        </p>
    </div>
    <div class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-[var(--color-text-primary)] mb-3">Enfoque por fases</h3>
        <p class="text-[var(--color-text-secondary)] leading-7">
            El desarrollo se organizó por fases para avanzar con orden: primero lo esencial,
            catálogo, autenticación y perfil, y después funcionalidades como pagos, historial de pedidos
            y soporte. Cada fase construye sobre la anterior sin dejar cabos sueltos.
        </p>
    </div>
</section>

<section class="bg-[var(--color-bg-card)] border border-[var(--color-border)] rounded-2xl p-6 md:p-8">
    <h2 class="text-xl font-semibold text-[var(--color-text-primary)] mb-4">Categorías actuales</h2>
    <div class="flex flex-wrap gap-3 mb-6">
        <?php foreach ($categories as $cat): ?>
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-secondary)] px-4 py-3 text-sm font-medium text-[var(--color-text-primary)] text-center">
                <?= htmlspecialchars($cat['name']) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-[var(--color-text-secondary)] leading-7">
        Estas son las categorías disponibles actualmente en la tienda. El catálogo incluye productos de demostración organizados por categoría, con fichas, imágenes y stock configurados para mostrar el funcionamiento completo de la plataforma.
    </p>
</section>
<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
