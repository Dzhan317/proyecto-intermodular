<?php
/*
 * Partial — Indicador de requisitos de contraseña.
 *
 * Incluido en: register.php, reset-password.php, profile/security.php
 *
 * Los IDs (req-length, req-upper…) los lee initPasswordStrength() en auth.js.
 * Si cambias las reglas aquí, actualiza también:
 *   - AuthService::validatePasswordStrength()  (backend)
 *   - PASSWORD_RULES en auth.js                (frontend)
 */
?>
<div class="mb-4 p-3 bg-[var(--color-bg-secondary)] border border-[var(--color-border)] rounded-xl">
    <p class="text-xs text-[var(--color-text-muted)] mb-2">La contraseña debe cumplir:</p>
    <div class="grid grid-cols-2 gap-1 text-xs">
        <div id="req-length"  class="req-item flex items-center gap-1.5 text-req-pending">
            <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
            Mínimo 12 caracteres
        </div>
        <div id="req-upper"   class="req-item flex items-center gap-1.5 text-req-pending">
            <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
            1 mayúscula
        </div>
        <div id="req-lower"   class="req-item flex items-center gap-1.5 text-req-pending">
            <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
            1 minúscula
        </div>
        <div id="req-number"  class="req-item flex items-center gap-1.5 text-req-pending">
            <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
            1 número
        </div>
        <div id="req-special" class="req-item flex items-center gap-1.5 text-req-pending col-span-2">
            <span class="req-dot w-1.5 h-1.5 rounded-full bg-req-pending flex-shrink-0"></span>
            1 carácter especial (!@#$%...)
        </div>
    </div>
</div>
