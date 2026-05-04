<?php
/*
 * Registro de nuevo usuario.
 * Indicador de fuerza de contraseña en tiempo real y validación de coincidencia.
 */
$pageTitle = 'Crear cuenta — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-white mb-1">Bienvenido a PrimeLux</h1>
<p class="text-[#9CA3AF] text-xs mb-6">Los campos marcados con * son obligatorios.</p>

<form method="POST" action="<?= APP_URL ?>/register" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-4">
        <label class="block text-sm text-[#9CA3AF] mb-2">Correo electrónico</label>
        <div class="flex items-center justify-between bg-[#111827] border border-[#374151]
                    rounded-xl px-4 py-3">
            <span class="text-white text-sm"><?= htmlspecialchars($email ?? '') ?></span>
            <a href="<?= APP_URL ?>/login"
               class="text-[#60A5FA] hover:text-[#93C5FD] text-xs font-medium transition-colors">
                Editar
            </a>
        </div>
        <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
            <label for="name" class="block text-sm text-[#9CA3AF] mb-2">Nombre *</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($name ?? '') ?>"
                   placeholder="Tu nombre" autocomplete="given-name" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl px-4 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
        </div>
        <div>
            <label for="last_name" class="block text-sm text-[#9CA3AF] mb-2">Apellidos *</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?= htmlspecialchars($lastName ?? '') ?>"
                   placeholder="Tus apellidos" autocomplete="family-name" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl px-4 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
        </div>
    </div>

    <div class="mb-3">
        <label for="password" class="block text-sm text-[#9CA3AF] mb-2">Contraseña *</label>
        <div class="relative">
            <input type="password" id="password" name="password"
                   placeholder="Crea tu contraseña" autocomplete="new-password" required
                   class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                          rounded-xl pl-4 pr-11 py-3 text-sm
                          focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                          transition-colors">
            <button type="button" id="togglePassword"
                    class="absolute inset-y-0 right-3 flex items-center px-1">
                <img id="eyeIcon" src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                     alt="Mostrar contraseña" class="w-5 h-5 icon">
            </button>
        </div>
    </div>

    <div class="mb-4 p-3 bg-[#111827] border border-[#374151] rounded-xl">
        <p class="text-xs text-[#6B7280] mb-2">La contraseña debe cumplir:</p>
        <div class="grid grid-cols-2 gap-1 text-xs">
            <div id="req-length"  class="req-item flex items-center gap-1.5 text-req-pending"><span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>Mínimo 10 caracteres</div>
            <div id="req-upper"   class="req-item flex items-center gap-1.5 text-req-pending"><span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>2 mayúsculas</div>
            <div id="req-lower"   class="req-item flex items-center gap-1.5 text-req-pending"><span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>2 minúsculas</div>
            <div id="req-number"  class="req-item flex items-center gap-1.5 text-req-pending"><span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>2 números</div>
            <div id="req-special" class="req-item flex items-center gap-1.5 text-req-pending col-span-2"><span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>1 carácter especial (!@#$%...)</div>
        </div>
    </div>

    <div class="mb-5">
        <label for="password_confirm" class="block text-sm text-[#9CA3AF] mb-2">Confirmar contraseña *</label>
        <input type="password" id="password_confirm" name="password_confirm"
               placeholder="Repite tu contraseña" autocomplete="new-password" required
               class="w-full bg-[#111827] text-white placeholder-[#6B7280] border border-[#374151]
                      rounded-xl px-4 py-3 text-sm
                      focus:outline-none focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]
                      transition-colors">
        <p id="matchError" class="text-xs text-red-400 mt-1 hidden">Las contraseñas no coinciden.</p>
    </div>

    <div class="bg-[#111827] border border-[#374151] rounded-xl p-4 mb-5 space-y-3">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="marketing" value="1" class="mt-0.5 accent-[#2563EB]">
            <span class="text-xs text-[#9CA3AF]">Quiero recibir correos sobre ofertas y novedades de PrimeLux SmartShop.</span>
        </label>
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="terms" value="1" id="termsCheck" required class="mt-0.5 accent-[#2563EB]">
            <span class="text-xs text-[#9CA3AF]">Acepto los <a href="<?= APP_URL ?>/terms" class="text-[#60A5FA] hover:text-[#93C5FD]">términos de uso</a> y confirmo que he leído la <a href="<?= APP_URL ?>/privacy" class="text-[#60A5FA] hover:text-[#93C5FD]">Política de Privacidad</a>. *</span>
        </label>
    </div>

    <button type="submit"
            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] active:bg-[#1E40AF]
                   text-white font-semibold py-3 rounded-xl text-sm transition-colors duration-200">
        Crear cuenta
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle('password', 'togglePassword', 'eyeIcon');
    initPasswordStrength('password');
    initPasswordMatch('password', 'password_confirm', 'matchError');
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
