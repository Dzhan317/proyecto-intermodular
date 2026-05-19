<?php
/*
 * Registro de nuevo usuario.
 * Indicador de fuerza de contraseña en tiempo real y validación de coincidencia.
 */
$pageTitle = 'Crear cuenta: PrimeLux SmartShop';
ob_start();
?>

<h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-1">Bienvenido a PrimeLux</h1>
<p class="text-[var(--color-text-secondary)] text-xs mb-6">Los campos marcados con * son obligatorios.</p>

<form method="POST" action="<?= APP_URL ?>/register" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <!-- Email (pre-rellenado) -->
    <div class="mb-4">
        <label class="block text-sm text-[var(--color-text-secondary)] mb-2">Correo electrónico</label>
        <div class="flex items-center justify-between bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                    rounded-xl px-4 py-3">
            <span class="text-[var(--color-text-primary)] text-sm"><?= htmlspecialchars($email ?? '') ?></span>
            <a href="<?= APP_URL ?>/login"
               class="text-[var(--color-link)] hover:text-[var(--color-link-hover)] text-xs font-medium transition-colors">
                Editar
            </a>
        </div>
        <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
    </div>

    <!-- Nombre y apellidos -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
            <label for="name" class="block text-sm text-[var(--color-text-secondary)] mb-2">Nombre *</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($name ?? '') ?>"
                   placeholder="Tu nombre"
                   autocomplete="given-name" required
                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                          rounded-xl px-4 py-3 text-sm
                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                          transition-colors">
        </div>
        <div>
            <label for="last_name" class="block text-sm text-[var(--color-text-secondary)] mb-2">Apellidos *</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?= htmlspecialchars($lastName ?? '') ?>"
                   placeholder="Tus apellidos"
                   autocomplete="family-name" required
                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                          rounded-xl px-4 py-3 text-sm
                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                          transition-colors">
        </div>
    </div>

    <!-- Contraseña -->
    <div class="mb-3">
        <label for="password" class="block text-sm text-[var(--color-text-secondary)] mb-2">Contraseña *</label>
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
    <div class="mb-5">
        <label for="password_confirm" class="block text-sm text-[var(--color-text-secondary)] mb-2">
            Confirmar contraseña *
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

    <!-- Checkboxes -->
    <div class="bg-[var(--color-bg-secondary)] border border-[var(--color-border)] rounded-xl p-4 mb-5 space-y-3">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="marketing" value="1"
                   class="mt-0.5 accent-[var(--color-brand)]">
            <span class="text-xs text-[var(--color-text-secondary)]">
                Quiero recibir correos sobre ofertas y novedades de PrimeLux SmartShop.
            </span>
        </label>
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="terms" value="1" id="termsCheck" required
                   class="mt-0.5 accent-[var(--color-brand)]">
            <span class="text-xs text-[var(--color-text-secondary)]">
                Acepto los
                <a href="<?= APP_URL ?>/terms"   class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]">términos de uso</a>
                y confirmo que he leído la
                <a href="<?= APP_URL ?>/privacy" class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]">Política de Privacidad</a>. *
            </span>
        </label>
    </div>

    <button type="submit"
            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] active:bg-[var(--color-brand-active)]
                   text-[var(--color-text-primary)] font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200">
        Crear cuenta
    </button>
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
