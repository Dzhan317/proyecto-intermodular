/**
 * checkout.js — Comportamientos del proceso de checkout.
 *
 * 1. initShippingSelector() — borde activo dinámico en opciones de envío.
 * 2. initPostalCodeInput()  — solo permite dígitos en el código postal (5 dígitos).
 * 3. initPhoneInput()       — solo permite dígitos en el teléfono (9 dígitos).
 */

/* ─── Selector de método de envío ────────────────────────────────────────── */

/**
 * Actualiza el estilo del borde de cada opción de envío
 * en tiempo real al cambiar el radio seleccionado.
 *
 * @param {string} containerId - ID del contenedor que agrupa los <label> de envío
 */
function initShippingSelector(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var labels = container.querySelectorAll('label[data-shipping]');
    var radios = container.querySelectorAll('input[type="radio"][name="shipping_type"]');

    function updateStyles() {
        var selected = container.querySelector('input[type="radio"]:checked');
        var selectedValue = selected ? selected.value : null;

        labels.forEach(function (label) {
            var isSelected = label.dataset.shipping === selectedValue;
            // Activo: borde brand + fondo sutil
            label.classList.toggle('border-[var(--color-brand)]', isSelected);
            label.classList.toggle('bg-[var(--color-brand)]/5',   isSelected);
            // Inactivo: borde normal
            label.classList.toggle('border-[var(--color-border)]', !isSelected);
            label.classList.toggle('hover:border-[var(--color-text-muted)]', !isSelected);
        });
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', updateStyles);
    });

    // Estado inicial
    updateStyles();
}

/* ─── Input solo numérico ─────────────────────────────────────────────────── */

/**
 * Restringe un input a solo dígitos numéricos.
 * Bloquea cualquier tecla no numérica salvo las de control habituales.
 *
 * @param {string} inputId  - ID del input
 * @param {number} maxLen   - Longitud máxima permitida
 */
function initNumericInput(inputId, maxLen) {
    var input = document.getElementById(inputId);
    if (!input) return;

    input.setAttribute('inputmode', 'numeric');
    input.setAttribute('maxlength', maxLen);

    input.addEventListener('keydown', function (e) {
        var allowed = [
            'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
            'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
            'Home', 'End',
        ];
        // Permite combinaciones con Ctrl/Cmd (copiar, pegar, seleccionar todo)
        if (e.ctrlKey || e.metaKey) return;
        if (allowed.includes(e.key)) return;
        // Bloquea todo lo que no sea dígito
        if (!/^\d$/.test(e.key)) e.preventDefault();
    });

    // Limpia pegados no numéricos
    input.addEventListener('paste', function (e) {
        e.preventDefault();
        var pasted = (e.clipboardData || window.clipboardData).getData('text');
        var digits = pasted.replace(/\D/g, '').slice(0, maxLen);
        var start  = input.selectionStart;
        var end    = input.selectionEnd;
        var current = input.value;
        input.value = (current.slice(0, start) + digits + current.slice(end)).slice(0, maxLen);
    });
}

/* ─── Inicialización del checkout ─────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    // Borde dinámico en opciones de envío
    initShippingSelector('shipping-options');

    // Solo dígitos: CP (5) y teléfono (9)
    initNumericInput('postal_code', 5);
    initNumericInput('phone', 9);
});
