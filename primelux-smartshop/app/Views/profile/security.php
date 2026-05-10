<?php
/*
 * Perfil — pestaña Seguridad.
 * Cambio de contraseña, estado del 2FA (siempre activo) y sesión actual.
 */
ob_start();

$csrfToken = (string) ($csrfToken ?? '');
$activeTab = (string) ($activeTab ?? 'security');
$device = (string) ($device ?? 'Dispositivo actual');
?>


<div class="flex flex-col md:flex-row gap-8">

    <?php require_once APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <div class="flex-1 space-y-6">

        <h1 class="text-2xl font-bold text-[var(--color-text-primary)]">Seguridad</h1>

        <!-- Feedback -->
        <?php if (!empty($success)): ?>
            <div class="p-3 alert-success rounded-xl text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="p-3 alert-error rounded-xl text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Bloque 1: Cambiar contraseña -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-5">Cambiar contraseña</h2>

            <form method="POST" action="<?= APP_URL ?>/profile/password" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <!-- Contraseña actual -->
                <div class="mb-4">
                    <label for="current_password" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Contraseña actual
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password"
                               autocomplete="current-password" required
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl pl-4 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                                      focus:ring-[var(--color-brand)] transition-colors">
                        <button type="button" id="toggleCurrent"
                                class="absolute inset-y-0 right-3 flex items-center px-1">
                            <img id="eyeCurrent"
                                 src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                                 alt="Mostrar" class="w-5 h-5 icon">
                        </button>
                    </div>
                </div>

                <!-- Nueva contraseña -->
                <div class="mb-3">
                    <label for="new_password" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Nueva contraseña
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password"
                               autocomplete="new-password" required
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl pl-4 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                                      focus:ring-[var(--color-brand)] transition-colors">
                        <button type="button" id="toggleNew"
                                class="absolute inset-y-0 right-3 flex items-center px-1">
                            <img id="eyeNew"
                                 src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                                 alt="Mostrar" class="w-5 h-5 icon">
                        </button>
                    </div>
                </div>

                <!-- Indicador de fuerza -->
                <div class="mb-4 p-3 bg-[var(--color-bg-secondary)] border border-[var(--color-border)] rounded-xl">
                    <p class="text-xs text-[var(--color-text-muted)] mb-2">La contraseña debe cumplir:</p>
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

                <!-- Confirmar contraseña — con toggle igual que los demás campos -->
                <div class="mb-5">
                    <label for="confirm_password" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Confirmar nueva contraseña
                    </label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password"
                               autocomplete="new-password" required
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl pl-4 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                                      focus:ring-[var(--color-brand)] transition-colors">
                        <button type="button" id="toggleConfirm"
                                class="absolute inset-y-0 right-3 flex items-center px-1">
                            <img id="eyeConfirm"
                                 src="<?= APP_URL ?>/assets/img/icons/ojo.svg"
                                 alt="Mostrar" class="w-5 h-5 icon">
                        </button>
                    </div>
                    <p id="matchError" class="text-xs text-[var(--color-error)] mt-1 hidden">
                        Las contraseñas no coinciden.
                    </p>
                </div>

                <button type="submit"
                        class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-[var(--color-text-primary)] font-semibold
                               px-6 py-2.5 rounded-xl text-sm transition-colors">
                    Actualizar contraseña
                </button>
            </form>
        </div>

        <!-- Bloque 2: Autenticación en dos factores -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-1">
                        Autenticación en dos factores (2FA)
                    </h2>
                    <p class="text-[var(--color-text-secondary)] text-sm">
                        Se envía un código a tu correo cada vez que inicias sesión.
                    </p>
                </div>
                <span class="flex items-center gap-1.5 bg-[var(--color-success)]/10 text-[var(--color-success)]
                             text-xs font-semibold px-3 py-1.5 rounded-full border border-[var(--color-success)]/30
                             flex-shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-[var(--color-success)]"></span>
                    Activo
                </span>
            </div>
        </div>

        <!-- Bloque 3: Sesión actual -->
        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
            <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-4">Sesión actual</h2>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[var(--color-bg-secondary)] border border-[var(--color-border)]
                                flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[var(--color-text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0
                                     002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[var(--color-text-primary)] text-sm font-medium">Este dispositivo</p>
                        <p class="text-[var(--color-text-muted)] text-xs mt-0.5"><?= htmlspecialchars($device) ?></p>
                    </div>
                </div>
                <a href="<?= APP_URL ?>/logout"
                   class="text-[var(--color-error)] hover:text-[var(--color-error)] text-sm font-medium transition-colors">
                    Cerrar sesión
                </a>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle('current_password', 'toggleCurrent',  'eyeCurrent');
    initPasswordToggle('new_password',     'toggleNew',      'eyeNew');
    initPasswordToggle('confirm_password', 'toggleConfirm',  'eyeConfirm');
    initPasswordStrength('new_password');
    initPasswordMatch('new_password', 'confirm_password', 'matchError');
});
</script>

<?php
$content   = ob_get_clean();
$csrfToken = $csrfToken ?? '';
require_once APP_PATH . '/Views/layouts/main.php';
