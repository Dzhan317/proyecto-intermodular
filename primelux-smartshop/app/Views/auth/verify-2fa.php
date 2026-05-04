<?php
/*
 * Verificación del código 2FA.
 * Pantalla completa se implementa en Fase 2.
 */
$pageTitle = 'Verificación — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-white mb-2">Verificación en dos pasos</h1>
<p class="text-[#9CA3AF] text-sm mb-6">
    Hemos enviado un código de verificación a tu correo electrónico.
</p>

<form method="POST" action="<?= APP_URL ?>/verify-2fa" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-5">
        <label for="code" class="block text-sm text-[#9CA3AF] mb-2">Código de verificación</label>
        <input type="text" id="code" name="code"
               placeholder="Introduce el código de 6 dígitos"
               maxlength="6" autocomplete="one-time-code" required
               class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                      rounded-xl px-4 py-3 text-sm text-center tracking-widest
                      focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                      transition-colors">
    </div>

    <button type="submit"
            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold
                   py-3 rounded-xl text-sm transition-colors duration-200 mb-4">
        Verificar
    </button>

    <div class="text-center">
        <a href="<?= APP_URL ?>/login" class="text-[#60A5FA] hover:text-[#93C5FD] text-sm transition-colors">
            ← Volver al inicio de sesión
        </a>
    </div>
</form>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
