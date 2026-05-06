/**
 * profile.js — Comportamientos de las páginas de perfil.
 *
 * initEditToggle(): alterna entre modo vista y modo edición en datos personales.
 * Usa window.APP_URL definido en el layout.
 */

/* ─── Toggle modo edición de datos personales ─────────────────────────────── */

/**
 * Muestra el formulario de edición al pulsar "Editar"
 * y lo oculta al pulsar "Cancelar", restaurando los valores originales.
 *
 * @param {string} viewId   - ID del bloque de solo lectura
 * @param {string} editId   - ID del formulario de edición
 * @param {string} editBtnId   - ID del botón "Editar"
 * @param {string} cancelBtnId - ID del botón "Cancelar"
 */
function initEditToggle(viewId, editId, editBtnId, cancelBtnId) {
    var viewBlock  = document.getElementById(viewId);
    var editBlock  = document.getElementById(editId);
    var editBtn    = document.getElementById(editBtnId);
    var cancelBtn  = document.getElementById(cancelBtnId);

    if (!viewBlock || !editBlock || !editBtn || !cancelBtn) return;

    // Guarda los valores originales para poder restaurarlos al cancelar
    var originalValues = {};
    var inputs = editBlock.querySelectorAll('input[data-original]');

    inputs.forEach(function (input) {
        originalValues[input.name] = input.value;
    });

    editBtn.addEventListener('click', function () {
        viewBlock.classList.add('hidden');
        editBlock.classList.remove('hidden');
        // Foco en el primer campo editable
        var firstInput = editBlock.querySelector('input');
        if (firstInput) firstInput.focus();
    });

    cancelBtn.addEventListener('click', function () {
        // Restaura valores originales
        inputs.forEach(function (input) {
            input.value = originalValues[input.name];
        });
        editBlock.classList.add('hidden');
        viewBlock.classList.remove('hidden');
    });
}
