<?php
/*
 * Verificación del código 2FA.
 * 6 inputs individuales. Envío automático al completar los 6 dígitos.
 * Incluye temporizador de expiración y cooldown de reenvío.
 */
$pageTitle   = 'Verificación — PrimeLux SmartShop';
$expiryMins  = defined('TWO_FA_EXPIRY_MINUTES')  ? TWO_FA_EXPIRY_MINUTES  : 10;
$cooldownSec = defined('TWO_FA_RESEND_COOLDOWN') ? TWO_FA_RESEND_COOLDOWN : 60;
$expiryMs    = $expiryMins * 60 * 1000;
ob_start();
?>

<h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-2">Verificación en dos pasos</h1>
<p class="text-[var(--color-text-secondary)] text-sm mb-1">Hemos enviado un código de 6 dígitos a</p>
<p class="text-[var(--color-text-primary)] text-sm font-semibold mb-5"><?= htmlspecialchars($maskedEmail) ?></p>

<?php if (!empty($_SESSION['twofa_success'])): ?>
    <div class="mb-4 p-3 bg-[var(--color-success)]/10 border border-[var(--color-success)]/30 rounded-lg text-[var(--color-success)] text-sm">
        <?= htmlspecialchars($_SESSION['twofa_success']) ?>
    </div>
    <?php unset($_SESSION['twofa_success']); ?>
<?php endif; ?>

<!-- Temporizador de expiración -->
<div class="flex items-center justify-center gap-2 mb-5">
    <div class="w-2 h-2 rounded-full bg-[var(--color-success)]" id="timerDot"></div>
    <span class="text-[var(--color-text-secondary)] text-sm">
        El código expira en <span id="countdown" class="font-semibold text-[var(--color-text-primary)]"></span>
    </span>
</div>

<form method="POST" action="<?= APP_URL ?>/verify-2fa" id="twoFaForm" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <input type="hidden" name="code" id="codeHidden">

    <div id="codeInputs" class="flex justify-center gap-3 mb-6">
        <?php for ($i = 0; $i < 6; $i++): ?>
            <input type="text"
                   inputmode="numeric"
                   maxlength="1"
                   data-index="<?= $i ?>"
                   autocomplete="<?= $i === 0 ? 'one-time-code' : 'off' ?>"
                   class="w-12 h-14 bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] text-center text-2xl font-bold
                          border-2 border-[var(--color-border)] rounded-xl
                          focus:outline-none focus:border-[var(--color-brand)]
                          transition-colors duration-150 caret-transparent">
        <?php endfor; ?>
    </div>

    <button type="submit" id="submitBtn" disabled
            class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] active:bg-[var(--color-brand-active)]
                   text-[var(--color-text-primary)] font-semibold py-3 rounded-xl text-sm
                   transition-colors duration-200
                   disabled:opacity-40 disabled:cursor-not-allowed mb-4">
        Verificar
    </button>
</form>

<form method="POST" action="<?= APP_URL ?>/verify-2fa/resend">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <div class="text-center">
        <p class="text-[var(--color-text-muted)] text-sm mb-1">¿No recibiste el código?</p>
        <button type="submit" id="resendBtn" disabled
                class="text-[var(--color-link)] hover:text-[var(--color-link-hover)] text-sm transition-colors
                       disabled:text-[var(--color-text-disabled)] disabled:cursor-not-allowed">
            Enviar de nuevo <span id="resendTimer"></span>
        </button>
    </div>
</form>

<div class="text-center mt-4">
    <a href="<?= APP_URL ?>/login"
       class="text-[var(--color-text-muted)] hover:text-[var(--color-text-secondary)] text-xs transition-colors">
        ← Volver al inicio de sesión
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    initTwoFactorInputs('codeInputs', 'codeHidden', 'submitBtn', 'twoFaForm');

    // Temporizador de expiración del código
    var expiryMs  = <?= (int) $expiryMs ?>;
    var startTime = Date.now();
    var dot       = document.getElementById('timerDot');
    var countdown = document.getElementById('countdown');

    function updateExpiry() {
        var elapsed   = Date.now() - startTime;
        var remaining = Math.max(0, expiryMs - elapsed);
        var mins      = Math.floor(remaining / 60000);
        var secs      = Math.floor((remaining % 60000) / 1000);

        countdown.textContent = mins + ':' + String(secs).padStart(2, '0');

        if (remaining <= 60000) {
            var errorColor = getComputedStyle(document.documentElement).getPropertyValue('--color-error').trim();
            countdown.style.color = errorColor;
            if (dot) dot.style.background = errorColor;
        }

        if (remaining === 0) {
            countdown.textContent = 'Expirado';
            clearInterval(expiryInterval);
        }
    }

    updateExpiry();
    var expiryInterval = setInterval(updateExpiry, 1000);

    // Cooldown del botón de reenvío
    var resendBtn   = document.getElementById('resendBtn');
    var resendTimer = document.getElementById('resendTimer');
    var remaining   = <?= (int) $cooldownSec ?>;

    function updateResend() {
        if (remaining <= 0) {
            resendBtn.disabled      = false;
            resendTimer.textContent = '';
            clearInterval(resendInterval);
            return;
        }
        resendTimer.textContent = '(' + remaining + 's)';
        remaining--;
    }

    updateResend();
    var resendInterval = setInterval(updateResend, 1000);

});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/auth.php';
