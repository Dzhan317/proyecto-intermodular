/**
 * auth.js — Comportamientos de las pantallas de autenticación.
 *
 * Usa window.APP_URL definido en el layout para construir rutas
 * sin mezclar PHP en este archivo.
 */

function initPasswordToggle(inputId, buttonId, iconId) {
    var input  = document.getElementById(inputId);
    var button = document.getElementById(buttonId);
    var icon   = document.getElementById(iconId);

    if (!input || !button || !icon) return;

    var iconShow = window.APP_URL + '/assets/img/icons/ojo.svg';
    var iconHide = window.APP_URL + '/assets/img/icons/ojos-cruzados.svg';

    button.addEventListener('click', function () {
        var isHidden = input.type === 'password';
        input.type   = isHidden ? 'text' : 'password';
        icon.src     = isHidden ? iconHide : iconShow;
        icon.alt     = isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña';
    });
}

var PASSWORD_RULES = {
    'req-length':  function (v) { return v.length >= 10; },
    'req-upper':   function (v) { return (v.match(/[A-Z]/g) || []).length >= 2; },
    'req-lower':   function (v) { return (v.match(/[a-z]/g) || []).length >= 2; },
    'req-number':  function (v) { return (v.match(/[0-9]/g) || []).length >= 2; },
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
