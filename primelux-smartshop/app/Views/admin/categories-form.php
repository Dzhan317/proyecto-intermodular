<?php
/*
 * Admin — Formulario de categoría (crear y editar comparten esta vista).
 * $category → null en crear, array en editar.
 */
ob_start();

$isEdit     = !empty($category);
$formAction = $isEdit
    ? APP_URL . '/admin/categories/' . (int) $category['id'] . '/edit'
    : APP_URL . '/admin/categories/create';

$name        = htmlspecialchars($_POST['name']        ?? $category['name']        ?? '');
$description = htmlspecialchars($_POST['description'] ?? $category['description'] ?? '');
$featured    = (bool) ($_POST['featured'] ?? $category['featured'] ?? false);
$status      = $_POST['status'] ?? $category['status'] ?? 'active';
?>

<div class="pt-2 max-w-md">

    <nav class="text-xs text-[var(--color-text-muted)] mb-6">
        <a href="<?= APP_URL ?>/admin/categories"
           class="hover:text-[var(--color-text-primary)] transition-colors">Categorías</a>
        <span class="mx-1">›</span>
        <span class="text-[var(--color-text-secondary)]">
            <?= $isEdit ? 'Editar categoría' : 'Añadir categoría' ?>
        </span>
    </nav>

    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
        <form method="POST" action="<?= $formAction ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Nombre -->
            <div class="mb-5">
                <label for="name" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                    Nombre *
                </label>
                <input type="text" id="name" name="name"
                       value="<?= $name ?>" required autofocus
                       class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                              placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                              rounded-xl px-4 py-3 text-sm focus:outline-none
                              focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                              transition-colors">
                <p class="text-xs text-[var(--color-text-muted)] mt-1.5">
                    El slug se genera automáticamente a partir del nombre.
                </p>
            </div>

            <!-- Descripción -->
            <div class="mb-5">
                <label for="description" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                    Descripción
                </label>
                <textarea id="description" name="description" rows="3" maxlength="500"
                          placeholder="Describe brevemente el contenido de esta categoría..."
                          class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                 placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                 rounded-xl px-4 py-3 text-sm focus:outline-none resize-none
                                 focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                 transition-colors"><?= $description ?></textarea>
                <p class="text-xs text-[var(--color-text-muted)] mt-1.5">
                    Opcional. Máximo 500 caracteres.
                </p>
            </div>

            <!-- Estado -->
            <div class="mb-5">
                <label for="status" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                    Estado *
                </label>
                <select id="status" name="status"
                        class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                               border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                               focus:outline-none focus:border-[var(--color-brand)]
                               focus:ring-1 focus:ring-[var(--color-brand)] transition-colors cursor-pointer">
                    <option value="active"   <?= $status === 'active'   ? 'selected' : '' ?>>Activa</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactiva</option>
                </select>
                <p class="text-xs text-[var(--color-text-muted)] mt-1.5">
                    Las categorías inactivas no se muestran en la tienda ni en los filtros.
                </p>
            </div>

            <!-- Destacada en home -->
            <div class="mb-5">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="featured" value="1"
                           <?= $featured ? 'checked' : '' ?>
                           class="w-4 h-4 accent-[var(--color-brand)] cursor-pointer">
                    <div>
                        <span class="text-sm font-medium text-[var(--color-text-primary)]">
                            Mostrar en la home
                        </span>
                        <p class="text-xs text-[var(--color-text-muted)] mt-0.5">
                            Si está marcada, esta categoría aparecerá en la portada de la tienda.
                        </p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-[var(--color-border)]">
                <a href="<?= APP_URL ?>/admin/categories"
                   class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">
                    ← Volver
                </a>
                <button type="submit"
                        class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                               text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
                    <?= $isEdit ? 'Guardar cambios' : 'Crear categoría' ?>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var statusSelect  = document.getElementById('status');
    var featuredCheck = document.querySelector('input[name="featured"]');
    var featuredWrap  = featuredCheck ? featuredCheck.closest('label') : null;

    if (!statusSelect || !featuredCheck) return;

    function syncFeatured() {
        var isInactive = statusSelect.value === 'inactive';
        featuredCheck.disabled = isInactive;
        if (isInactive) {
            featuredCheck.checked = false;
            if (featuredWrap) featuredWrap.classList.add('opacity-40', 'cursor-not-allowed');
        } else {
            if (featuredWrap) featuredWrap.classList.remove('opacity-40', 'cursor-not-allowed');
        }
    }

    statusSelect.addEventListener('change', syncFeatured);
    syncFeatured(); // estado inicial al cargar la página
});
</script>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/admin.php';
