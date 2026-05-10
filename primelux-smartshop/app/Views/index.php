<?php
/*
 * Página Sobre nosotros — contenido simple y defendible para Fase 4.
 */
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">
    <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr] items-start">
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-8 lg:p-10 shadow-xl shadow-black/20">
            <span class="inline-flex items-center rounded-full border border-[var(--color-border)] bg-[var(--color-bg-secondary)] px-4 py-1.5 text-sm font-semibold text-[var(--color-brand)]">
                PrimeLux SmartShop
            </span>

            <h1 class="mt-5 text-3xl lg:text-5xl font-bold tracking-tight text-[var(--color-text-primary)]">
                Tecnología premium con una base académica y un enfoque práctico.
            </h1>

            <p class="mt-5 text-base lg:text-lg text-[var(--color-text-secondary)] leading-8">
                PrimeLux SmartShop es un e-commerce académico y multicategoría orientado al ámbito tecnológico.
                El proyecto nace con un objetivo claro: aplicar de forma realista los conceptos trabajados en el
                ciclo formativo de DAW y transformarlos en una tienda online moderna, coherente y funcional.
            </p>

            <p class="mt-4 text-base text-[var(--color-text-secondary)] leading-8">
                Aunque su alcance está ajustado al tiempo disponible del proyecto, la propuesta busca transmitir
                una imagen premium, clara y profesional, con una experiencia visual cuidada, una navegación simple
                y una base sólida de seguridad para el usuario.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <span class="rounded-full bg-[var(--color-bg-secondary)] px-4 py-2 text-sm font-medium text-[var(--color-text-primary)] border border-[var(--color-border)]">Diseño moderno</span>
                <span class="rounded-full bg-[var(--color-bg-secondary)] px-4 py-2 text-sm font-medium text-[var(--color-text-primary)] border border-[var(--color-border)]">Experiencia clara</span>
                <span class="rounded-full bg-[var(--color-bg-secondary)] px-4 py-2 text-sm font-medium text-[var(--color-text-primary)] border border-[var(--color-border)]">Enfoque académico</span>
                <span class="rounded-full bg-[var(--color-bg-secondary)] px-4 py-2 text-sm font-medium text-[var(--color-text-primary)] border border-[var(--color-border)]">Seguridad básica</span>
            </div>
        </div>

        <aside class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-8 shadow-xl shadow-black/20">
            <h2 class="text-2xl font-semibold text-[var(--color-text-primary)]">Contacto</h2>

            <div class="mt-6 space-y-5 text-sm">
                <div>
                    <p class="text-[var(--color-text-muted)] uppercase tracking-wide text-xs">Correo</p>
                    <a href="mailto:admin@primeluxshop.es" class="mt-1 inline-block text-[var(--color-link)] hover:text-[var(--color-link-hover)] transition-colors">
                        admin@primeluxshop.es
                    </a>
                </div>

                <div>
                    <p class="text-[var(--color-text-muted)] uppercase tracking-wide text-xs">Teléfono</p>
                    <p class="mt-1 text-[var(--color-text-secondary)]">+34 921 123 456</p>
                </div>

                <div>
                    <p class="text-[var(--color-text-muted)] uppercase tracking-wide text-xs">Ubicación</p>
                    <p class="mt-1 text-[var(--color-text-secondary)]">Segovia, España</p>
                </div>

                <div>
                    <p class="text-[var(--color-text-muted)] uppercase tracking-wide text-xs">Horario de atención</p>
                    <p class="mt-1 text-[var(--color-text-secondary)]">Lunes a viernes · 09:00 a 14:00 y 16:00 a 19:00</p>
                    <p class="mt-1 text-xs text-[var(--color-text-muted)]">Horario local peninsular</p>
                </div>
            </div>
        </aside>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <article class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-7">
            <h2 class="text-xl font-semibold text-[var(--color-text-primary)]">Qué ofrecemos</h2>
            <p class="mt-4 text-[var(--color-text-secondary)] leading-7">
                Un catálogo multicategoría centrado en áreas tecnológicas afines al entorno formativo de DAW:
                componentes, electrónica, informática, periféricos, redes y software.
            </p>
        </article>

        <article class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-7">
            <h2 class="text-xl font-semibold text-[var(--color-text-primary)]">Cómo está construido</h2>
            <p class="mt-4 text-[var(--color-text-secondary)] leading-7">
                El proyecto combina estructura MVC en PHP, base de datos relacional y un sistema visual apoyado en
                CSS global y Tailwind para agilizar el desarrollo sin perder coherencia.
            </p>
        </article>

        <article class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-7">
            <h2 class="text-xl font-semibold text-[var(--color-text-primary)]">Qué buscamos transmitir</h2>
            <p class="mt-4 text-[var(--color-text-secondary)] leading-7">
                Confianza, claridad y una experiencia de compra limpia. No se trata de inflar funciones, sino de
                resolver bien lo importante dentro del tiempo real del proyecto.
            </p>
        </article>
    </div>

    <div class="mt-8 rounded-3xl border border-[var(--color-border)] bg-gradient-to-r from-[var(--color-bg-card)] to-[var(--color-bg-secondary)] p-8 lg:p-10">
        <h2 class="text-2xl lg:text-3xl font-semibold text-[var(--color-text-primary)]">Una tienda online pensada para demostrar criterio, no solo apariencia.</h2>
        <p class="mt-4 max-w-3xl text-[var(--color-text-secondary)] leading-8">
            PrimeLux SmartShop no pretende simular una gran plataforma con módulos que todavía no existen.
            La intención es construir una base seria, visualmente consistente y técnicamente defendible,
            priorizando funcionalidad real, orden y mantenimiento.
        </p>
    </div>
</section>
