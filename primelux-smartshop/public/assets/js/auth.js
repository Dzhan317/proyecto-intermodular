/**
 * auth.js — Comportamientos de las pantallas de autenticación.
 *
 * Usa window.APP_URL definido en el layout para construir rutas
 * sin mezclar PHP en este archivo.
 */

/* ─── Toggle mostrar / ocultar contraseña ─────────────────────────────────── */

function initPasswordToggle(inputId, buttonId, iconId) {
    var input  = document.getElementById(inputId);
    var button = document.getElementById(buttonId);
    var iconShow = document.getElementById(iconId);
    var iconHide = document.getElementById(iconId + 'Hide');

    if (!input || !button || !iconShow) return;

    button.addEventListener('click', function () {
        var isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        // Alterna entre los dos SVGs inline
        iconShow.classList.toggle('hidden', isHidden);
        if (iconHide) iconHide.classList.toggle('hidden', !isHidden);
    });
}

/* ─── Indicador de fuerza de contraseña ───────────────────────────────────── */

// Reglas NIST 2025 — longitud como factor principal, 1 de cada tipo.
// Si cambias estos valores, actualiza también:
//   - AuthService::validatePasswordStrength()         (backend)
//   - app/Views/auth/partials/password-requirements.php (textos HTML)
var PASSWORD_RULES = {
    'req-length':  function (v) { return v.length >= 12; },
    'req-upper':   function (v) { return (v.match(/[A-Z]/g) || []).length >= 1; },
    'req-lower':   function (v) { return (v.match(/[a-z]/g) || []).length >= 1; },
    'req-number':  function (v) { return (v.match(/[0-9]/g) || []).length >= 1; },
    'req-special': function (v) { return /[^A-Za-z0-9]/.test(v); },
};

function initPasswordStrength(inputId) {
    var input = document.getElementById(inputId);
    if (!input) return;

    input.addEventListener('input', function () {
        Object.keys(PASSWORD_RULES).forEach(function (id) {
            var el  = document.getElementById(id);
            if (!el) return;
            var dot = el.querySelector('.req-dot');
            var ok  = PASSWORD_RULES[id](input.value);

            el.classList.toggle('text-req-ok',      ok);
            el.classList.toggle('text-req-pending', !ok);

            if (dot) {
                dot.classList.toggle('bg-req-ok',      ok);
                dot.classList.toggle('bg-req-pending', !ok);
            }
        });
    });
}

/* ─── Validación de coincidencia de contraseñas ───────────────────────────── */

function initPasswordMatch(passwordId, confirmId, errorId) {
    var password = document.getElementById(passwordId);
    var confirm  = document.getElementById(confirmId);
    var error    = document.getElementById(errorId);

    if (!password || !confirm || !error) return;

    confirm.addEventListener('input', function () {
        var mismatch = confirm.value.length > 0 && confirm.value !== password.value;
        error.classList.toggle('hidden', !mismatch);
    });
}

/* ─── Inputs individuales para el código 2FA ──────────────────────────────── */

/**
 * 6 cajas de un dígito para introducir el código de verificación.
 * Al completar los 6 dígitos el formulario se envía automáticamente.
 * Soporta pegar el código completo desde el portapapeles.
 *
 * @param {string} containerId - ID del contenedor de los 6 inputs
 * @param {string} hiddenId    - ID del input hidden que recibe el código completo
 * @param {string} submitId    - ID del botón de envío (se activa al completar)
 * @param {string} formId      - ID del formulario
 */
function initTwoFactorInputs(containerId, hiddenId, submitId, formId) {
    var container = document.getElementById(containerId);
    var hidden    = document.getElementById(hiddenId);
    var submitBtn = document.getElementById(submitId);
    var form      = document.getElementById(formId);

    if (!container || !hidden || !submitBtn || !form) return;

    var inputs = Array.from(container.querySelectorAll('input'));

    function updateHidden() {
        var code = inputs.map(function (i) { return i.value; }).join('');
        hidden.value = code;
        submitBtn.disabled = code.length < 6;
    }

    function focusInput(index) {
        if (index >= 0 && index < inputs.length) {
            inputs[index].focus();
            inputs[index].select();
        }
    }

    inputs.forEach(function (input, index) {

        // Solo permite dígitos numéricos
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace') {
                if (input.value) {
                    input.value = '';
                } else {
                    focusInput(index - 1);
                }
                updateHidden();
                e.preventDefault();
                return;
            }

            if (e.key === 'ArrowLeft')  { focusInput(index - 1); e.preventDefault(); return; }
            if (e.key === 'ArrowRight') { focusInput(index + 1); e.preventDefault(); return; }

            // Permite: dígitos, Tab, Enter, y combinaciones con Ctrl/Cmd (para pegar)
            if (!/^\d$/.test(e.key) && !['Tab', 'Enter'].includes(e.key) && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function () {
            var val = input.value.replace(/\D/g, '');

            // Si pegan varios dígitos de una vez, los distribuye
            if (val.length > 1) {
                val.split('').forEach(function (digit, i) {
                    if (inputs[index + i]) inputs[index + i].value = digit;
                });
                focusInput(Math.min(index + val.length, inputs.length - 1));
            } else {
                input.value = val;
                if (val) focusInput(index + 1);
            }

            updateHidden();

            // Envío automático al completar
            if (hidden.value.length === 6) {
                form.submit();
            }
        });

        // Soporte para pegar con Ctrl+V en cualquier caja
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData).getData('text');
            var digits = pasted.replace(/\D/g, '').slice(0, 6);
            digits.split('').forEach(function (digit, i) {
                if (inputs[i]) inputs[i].value = digit;
            });
            focusInput(Math.min(digits.length, inputs.length - 1));
            updateHidden();
            if (digits.length === 6) form.submit();
        });
    });

    // Foco en el primer input al cargar
    focusInput(0);
}
