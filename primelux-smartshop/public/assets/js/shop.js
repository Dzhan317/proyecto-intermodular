/**
 * shop.js — Comportamientos de la tienda.
 *
 * initQuantitySelector(): selector de cantidad en el detalle de producto.
 * initPriceSlider(): slider doble de rango de precio en el listado.
 */

/* ─── Selector de cantidad ────────────────────────────────────────────────── */

function initQuantitySelector(minusBtnId, plusBtnId, valueId, maxStock) {
    var minusBtn = document.getElementById(minusBtnId);
    var plusBtn  = document.getElementById(plusBtnId);
    var valueEl  = document.getElementById(valueId);

    if (!minusBtn || !plusBtn || !valueEl) return;

    var qty = 1;
    var max = maxStock || 99;

    function render() {
        valueEl.textContent = qty;
        minusBtn.disabled   = qty <= 1;
        plusBtn.disabled    = qty >= max;
        minusBtn.classList.toggle('opacity-40', qty <= 1);
        plusBtn.classList.toggle('opacity-40',  qty >= max);
    }

    minusBtn.addEventListener('click', function () { if (qty > 1)   { qty--; render(); } });
    plusBtn.addEventListener('click',  function () { if (qty < max) { qty++; render(); } });

    render();
}

/* ─── Slider doble de precio ──────────────────────────────────────────────── */

/**
 * Slider doble para filtrar por rango de precio.
 * Usa dos <input type="range"> superpuestos.
 * Actualiza el track activo (azul) según los valores seleccionados.
 *
 * @param {string} minId      - ID del input range mínimo
 * @param {string} maxId      - ID del input range máximo
 * @param {string} rangeId    - ID del div del track activo
 * @param {string} displayMinId - ID del span que muestra el valor mínimo
 * @param {string} displayMaxId - ID del span que muestra el valor máximo
 */
function initPriceSlider(minId, maxId, rangeId, displayMinId, displayMaxId) {
    var sliderMin   = document.getElementById(minId);
    var sliderMax   = document.getElementById(maxId);
    var rangeEl     = document.getElementById(rangeId);
    var displayMin  = document.getElementById(displayMinId);
    var displayMax  = document.getElementById(displayMaxId);

    if (!sliderMin || !sliderMax || !rangeEl) return;

    // Activa los eventos de pointer en los thumbs
    sliderMin.style.pointerEvents = 'auto';
    sliderMax.style.pointerEvents = 'auto';

    function updateSlider() {
        var min    = parseInt(sliderMin.value);
        var max    = parseInt(sliderMax.value);
        var rangeV = parseInt(sliderMin.max) - parseInt(sliderMin.min);

        // Evita cruce de thumbs — margen mínimo de 1
        if (min >= max) {
            if (this === sliderMin) { sliderMin.value = max - 1; min = max - 1; }
            else                   { sliderMax.value = min + 1; max = min + 1; }
        }

        // Posición del track activo en %
        var leftPct  = ((min - parseInt(sliderMin.min)) / rangeV) * 100;
        var rightPct = ((parseInt(sliderMax.max) - max) / rangeV) * 100;

        rangeEl.style.left  = leftPct  + '%';
        rangeEl.style.right = rightPct + '%';

        if (displayMin) displayMin.textContent = min + ' €';
        if (displayMax) displayMax.textContent = max + ' €';
    }

    sliderMin.addEventListener('input', updateSlider);
    sliderMax.addEventListener('input', updateSlider);

    // Estado inicial
    updateSlider.call(sliderMin);
}
