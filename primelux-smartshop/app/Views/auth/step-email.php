<?php
/*
 * Paso 1 del login: el usuario introduce su email.
 * Si el email existe → pantalla de contraseña. Si no → registro.
 * Mensaje de error siempre idéntico (evita enumeración de usuarios).
 */
$pageTitle = 'Acceder — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-2">¡Bienvenido!</h1>
<p class="text-[var(--color-text-secondary)] text-sm mb-6">
    Introduce tu correo para iniciar sesión o crear una cuenta.
</p>

<form method="POST" action="<?= APP_URL ?>/auth/check-email" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-4">
        <label for="email" class="block text-sm text-[var(--color-text-secondary)] mb-2">Correo electrónico</label>
        <div class="relative">
            <!-- Icono inline — evita problemas de carga de SVG externo -->
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7
                             a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email ?? '') ?>"
                   placeholder="tu@correo.com"
                   autocomplete="email" required
                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                          border border-[var(--color-border)] rounded-xl pl-10 pr-4 py-3 text-sm
                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                          focus:ring-[var(--color-brand)] transition-colors">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] active:bg-[var(--color-brand-active)]
                   text-[var(--color-text-primary)] font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200 mt-2">
        Continuar
    </button>
</form>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
