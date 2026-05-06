<?php
/*
 * Formulario para establecer una nueva contraseña.
 * El usuario llega aquí desde el enlace recibido por email.
 */
$pageTitle = 'Nueva contraseña — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-white mb-2">Crea una nueva contraseña</h1>
<p class="text-[#9CA3AF] text-sm mb-6">Elige una contraseña segura para tu cuenta.</p>

<form method="POST" action="<?= APP_URL ?>/reset-password/<?= htmlspecialchars($token) ?>" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <!-- Nueva contraseña -->
    <div class="mb-3">
        <label for="password" class="block text-sm text-[#9CA3AF] mb-2">Nueva contraseña</label>
        <div class="relative">
            <input type="password" id="password" name="password"
                   placeholder="Crea tu contraseña"
                   autocomplete="new-password" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl pl-4 pr-11 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
            <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-3 flex items-center px-1">
                <img id="eyeIcon"
                     src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                     alt="Mostrar contraseña"
                     class="w-5 h-5 icon">
            </button>
        </div>
    </div>

    <!-- Indicador de fuerza -->
    <div class="mb-4 p-3 bg-[#111827] border border-[#374151] rounded-xl">
        <p class="text-xs text-[#6B7280] mb-2">La contraseña debe cumplir:</p>
        <div class="grid grid-cols-2 gap-1 text-xs">
            <div id="req-length"  class="req-item flex items-center gap-1.5 text-req-pending">
                <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
                Mínimo 10 caracteres
            </div>
            <div id="req-upper"   class="req-item flex items-center gap-1.5 text-req-pending">
                <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
                2 mayúsculas
            </div>
            <div id="req-lower"   class="req-item flex items-center gap-1.5 text-req-pending">
                <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
                2 minúsculas
            </div>
            <div id="req-number"  class="req-item flex items-center gap-1.5 text-req-pending">
                <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
                2 números
            </div>
            <div id="req-special" class="req-item flex items-center gap-1.5 text-req-pending col-span-2">
                <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
                1 carácter especial (!@#$%...)
            </div>
        </div>
    </div>

    <!-- Confirmar contraseña — con toggle igual que el campo anterior -->
    <div class="mb-6">
        <label for="password_confirm" class="block text-sm text-[#9CA3AF] mb-2">
            Confirmar contraseña
        </label>
        <div class="relative">
            <input type="password" id="password_confirm" name="password_confirm"
                   placeholder="Repite tu contraseña"
                   autocomplete="new-password" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl pl-4 pr-11 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
            <button type="button" id="toggleConfirm"
                    class="absolute inset-y-0 right-3 flex items-center px-1">
                <img id="eyeConfirm"
                     src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                     alt="Mostrar contraseña"
                     class="w-5 h-5 icon">
            </button>
        </div>
        <p id="matchError" class="text-xs text-red-400 mt-1 hidden">
            Las contraseñas no coinciden.
        </p>
    </div>

    <button type="submit"
            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] active:bg-[#1E40AF]
                   text-white font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200 mb-4">
        Guardar nueva contraseña
    </button>

    <div class="text-center">
        <a href="<?= APP_URL ?>/login"
           class="text-[#60A5FA] hover:text-[#93C5FD] text-sm transition-colors">
            ← Volver al inicio de sesión
        </a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle('password',         'togglePassword', 'eyeIcon');
    initPasswordToggle('password_confirm', 'toggleConfirm',  'eyeConfirm');
    initPasswordStrength('password');
    initPasswordMatch('password', 'password_confirm', 'matchError');
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
