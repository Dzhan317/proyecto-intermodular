<?php
/*
 * Formulario para establecer una nueva contraseña.
 * El usuario llega aquí desde el enlace recibido por email.
 */
$pageTitle = 'Nueva contraseña — PrimeLux SmartShop';
ob_start();
?>

<?php if (!empty($expired)): ?>
    <!-- Enlace expirado — no se muestra el formulario -->
    <h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-2">Enlace expirado</h1>
    <p class="text-[var(--color-text-secondary)] text-sm mb-6">
        Este enlace ya no es válido o ha expirado. Los enlaces de restablecimiento son válidos durante 1 hora.
    </p>
    <a href="<?= APP_URL ?>/forgot-password"
       class="w-full block text-center bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
              text-[var(--color-text-primary)] font-semibold py-3 rounded-xl text-sm
              transition-colors duration-200 mb-4">
        Solicitar un nuevo enlace
    </a>
    <div class="text-center">
        <a href="<?= APP_URL ?>/login"
           class="text-[var(--color-link)] hover:text-[var(--color-link-hover)] text-sm transition-colors">
            ← Volver al inicio de sesión
        </a>
    </div>
<?php else: ?>

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
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg id="eyeIconHide" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--color-text-muted)] hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                </svg>
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
                <svg id="eyeConfirm" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg id="eyeConfirmHide" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-[var(--color-text-muted)] hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                </svg>
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

<?php endif; ?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/layouts/auth.php';
