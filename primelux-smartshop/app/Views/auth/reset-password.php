<?php
/*
 * Formulario para establecer una nueva contraseña.
 * El usuario llega aquí desde el enlace recibido por email.
 */
$pageTitle = 'Nueva contraseña — PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-2">Crea una nueva contraseña</h1>
<p class="text-[var(--color-text-secondary)] text-sm mb-6">Elige una contraseña segura para tu cuenta.</p>

<form method="POST" action="<?= APP_URL ?>/reset-password/<?= htmlspecialchars($token) ?>" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <!-- Nueva contraseña -->
    <div class="mb-3">
        <label for="password" class="block text-sm text-[var(--color-text-secondary)] mb-2">Nueva contraseña</label>
        <div class="relative">
            <input type="password" id="password" name="password"
                   placeholder="Crea tu contraseña"
                   autocomplete="new-password" required
                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                          rounded-xl pl-4 pr-11 py-3 text-sm
                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
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

    <!-- Indicador de fuerza — requisitos de contraseña -->
    <?php require APP_PATH . '/Views/auth/partials/password-requirements.php'; ?>

    <!-- Confirmar contraseña — con toggle igual que el campo anterior -->
    <div class="mb-6">
        <label for="password_confirm" class="block text-sm text-[var(--color-text-secondary)] mb-2">
            Confirmar contraseña
        </label>
        <div class="relative">
            <input type="password" id="password_confirm" name="password_confirm"
                   placeholder="Repite tu contraseña"
                   autocomplete="new-password" required
                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                          rounded-xl pl-4 pr-11 py-3 text-sm
                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                          transition-colors">
            <button type="button" id="toggleConfirm"
                    class="absolute inset-y-0 right-3 flex items-center px-1">
                <img id="eyeConfirm"
                     src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                     alt="Mostrar contraseña"
                     class="w-5 h-5 icon">
            </button>
        </div>
        <p id="matchError" class="text-xs text-[var(--color-error)] mt-1 hidden">
            Las contraseñas no coinciden.
        </p>
    </div>

    <button type="submit"
            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] active:bg-[var(--color-brand-active)]
                   text-[var(--color-text-primary)] font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200 mb-4">
        Guardar nueva contraseña
    </button>

    <div class="text-center">
        <a href="<?= APP_URL ?>/login"
           class="text-[var(--color-link)] hover:text-[var(--color-link-hover)] text-sm transition-colors">
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
