<?php
/*
 * Paso 2 del login: el usuario introduce su contraseña.
 * Error siempre genérico para evitar enumeración de usuarios.
 */
$pageTitle = 'Iniciar sesión — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-white mb-2">¡Te damos la bienvenida!</h1>
<p class="text-[#9CA3AF] text-sm mb-6">Introduce tu contraseña para continuar.</p>

<form method="POST" action="<?= APP_URL ?>/login" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div class="mb-4">
        <label class="block text-sm text-[#9CA3AF] mb-2">Correo electrónico</label>
        <div class="flex items-center justify-between bg-[#111827] border border-[#374151]
                    rounded-xl px-4 py-3">
            <span class="text-white text-sm"><?= htmlspecialchars($email) ?></span>
            <a href="<?= APP_URL ?>/login"
               class="text-[#60A5FA] hover:text-[#93C5FD] text-xs font-medium transition-colors">
                Editar
            </a>
        </div>
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
    </div>

    <div class="mb-2">
        <label for="password" class="block text-sm text-[#9CA3AF] mb-2">Contraseña</label>
        <div class="relative">
            <input type="password" id="password" name="password"
                   placeholder="Introduce tu contraseña"
                   autocomplete="current-password" required
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

    <div class="text-right mb-6">
        <a href="<?= APP_URL ?>/forgot-password"
           class="text-[#60A5FA] hover:text-[#93C5FD] text-xs transition-colors">
            ¿Has olvidado tu contraseña?
        </a>
    </div>

    <button type="submit"
            class="w-full bg-[#2563EB] hover:bg-[#1D4ED8] active:bg-[#1E40AF]
                   text-white font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200">
        Iniciar sesión
    </button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle('password', 'togglePassword', 'eyeIcon');
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
