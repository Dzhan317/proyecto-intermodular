/**
 * header-dropdown.js — Desplegables del header.
 *
 * 1. initCategoriesDropdown() — desplegable de categorías en la nav.
 * 2. initUserDropdown()       — desplegable de cuenta de usuario.
 *
 * Ambos siguen el mismo patrón: toggle al clic, cierre al clic fuera
 * o al pulsar Escape.
 */

/* ─── Helper genérico de desplegable ─────────────────────────────────────── */

function initDropdown(wrapperId, buttonId, menuId, iconId) {
    var wrapper = document.getElementById(wrapperId);
    var button  = document.getElementById(buttonId);
    var menu    = document.getElementById(menuId);
    var icon    = iconId ? document.getElementById(iconId) : null;

    if (!wrapper || !button || !menu) return;

    function openDropdown() {
        menu.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
        if (icon) icon.classList.add('rotate-180');
    }

    function closeDropdown() {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
        if (icon) icon.classList.remove('rotate-180');
    }

    function toggleDropdown(e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.contains('hidden') ? openDropdown() : closeDropdown();
    }

    button.addEventListener('click', toggleDropdown);

    // Cierra al hacer clic fuera
    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) closeDropdown();
    });

    // Cierra con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDropdown();
    });
}

/* ─── Inicialización ─────────────────────────────────────────────────────── */

document.addEventListener('DOMContentLoaded', function () {
    // Desplegable de categorías
    initDropdown(
        'categoriesDropdownWrapper',
        'categoriesDropdownButton',
        'categoriesDropdownMenu',
        'categoriesDropdownIcon'
    );

    // Desplegable de usuario
    initDropdown(
        'userDropdownWrapper',
        'userDropdownButton',
        'userDropdownMenu',
        null  // sin icono rotatorio en el botón de usuario
    );
});
