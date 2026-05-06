<?php
/*
 * Pantalla de recuperación de contraseña.
 * Mensaje siempre idéntico: nunca revela si el email está registrado.
 */
$pageTitle = '¿Olvidaste tu contraseña? — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-white mb-2">¿Olvidaste tu contraseña?</h1>
<p class="text-[#9CA3AF] text-sm mb-6">
    Introduce tu correo y te enviaremos un enlace para restablecerla.
</p>

<form method="POST" action="<?= APP_URL ?>/forgot-password" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-5">
        <label for="email" class="block text-sm text-[#9CA3AF] mb-2">Correo electrónico</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-[#6B7280]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7
                             a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </span>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email ?? '') ?>"
                   placeholder="tu@correo.com"
                   autocomplete="email" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl pl-10 pr-4 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
        </div>
    </div>

    <button type="submit"
            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] active:bg-[#1E40AF]
                   text-white font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200 mb-4">
        Enviar enlace
    </button>

    <div class="text-center">
        <a href="<?= APP_URL ?>/login"
           class="text-[#60A5FA] hover:text-[#93C5FD] text-sm transition-colors">
            ← Volver al inicio de sesión
        </a>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
