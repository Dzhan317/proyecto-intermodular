document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('categoriesDropdownWrapper');
    const button = document.getElementById('categoriesDropdownButton');
    const menu = document.getElementById('categoriesDropdownMenu');
    const icon = document.getElementById('categoriesDropdownIcon');

    if (!wrapper || !button || !menu) {
        return;
    }

    function openDropdown() {
        menu.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');

        if (icon) {
            icon.classList.add('rotate-180');
        }
    }

    function closeDropdown() {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');

        if (icon) {
            icon.classList.remove('rotate-180');
        }
    }

    function toggleDropdown(event) {
        event.preventDefault();
        event.stopPropagation();

        if (menu.classList.contains('hidden')) {
            openDropdown();
        } else {
            closeDropdown();
        }
    }

    button.addEventListener('click', toggleDropdown);

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target)) {
            closeDropdown();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeDropdown();
        }
    });
});