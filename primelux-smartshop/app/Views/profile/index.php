<?php
/*
 * Perfil — pestaña Inicio.
 * Muestra datos personales. Nombre y apellidos son editables (toggle JS).
 * Email es solo lectura por diseño (cambio requeriría re-verificación).
 */
ob_start();

$user = is_array($user ?? null) ? $user : [];
$name = (string) ($user['name'] ?? '');
$lastName = (string) ($user['last_name'] ?? '');
$email = (string) ($user['email'] ?? '');
$createdAt = !empty($user['created_at']) ? date('d/m/Y', strtotime((string) $user['created_at'])) : 'No disponible';
$csrfToken = (string) ($csrfToken ?? '');
$activeTab = (string) ($activeTab ?? 'profile');
?>


<div class="flex flex-col md:flex-row gap-8">

    <?php require_once APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-1">

        <h1 class="text-2xl font-bold text-[var(--color-text-primary)] mb-6">Datos personales</h1>

        <!-- Mensajes de feedback -->
        <?php if (!empty($success)): ?>
            <div class="mb-6 p-3 bg-[var(--color-success-bg)] border border-[var(--color-success-border)] rounded-xl text-[var(--color-success)] text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-3 bg-[var(--color-error-bg)] border border-[var(--color-error-border)] rounded-xl text-[var(--color-error)] text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] overflow-hidden">

            <!-- Modo vista (por defecto) -->
            <div id="viewMode" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-base font-semibold text-[var(--color-text-primary)]">Tus datos</h2>
                    <button id="editBtn" type="button"
                            class="text-[var(--color-link)] hover:text-[var(--color-link-hover)] text-sm font-medium transition-colors">
                        Editar datos
                    </button>
                </div>

                <dl class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[var(--color-text-muted)] text-sm w-40 flex-shrink-0">Nombre</dt>
                        <dd class="text-[var(--color-text-primary)] text-sm font-medium" id="displayName">
                            <?= htmlspecialchars($name) ?>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[var(--color-text-muted)] text-sm w-40 flex-shrink-0">Apellidos</dt>
                        <dd class="text-[var(--color-text-primary)] text-sm font-medium" id="displayLastName">
                            <?= htmlspecialchars($lastName) ?>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 pt-4 border-t border-[var(--color-border)]">
                        <dt class="text-[var(--color-text-muted)] text-sm w-40 flex-shrink-0">Correo electrónico</dt>
                        <dd class="text-[var(--color-text-secondary)] text-sm">
                            <?= htmlspecialchars($email) ?>
                            <span class="ml-2 text-xs text-[var(--color-text-disabled)]">(no editable)</span>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[var(--color-text-muted)] text-sm w-40 flex-shrink-0">Miembro desde</dt>
                        <dd class="text-[var(--color-text-secondary)] text-sm">
                            <?= $createdAt ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Modo edición (oculto por defecto) -->
            <div id="editMode" class="p-6 hidden">
                <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-6">Editar datos</h2>

                <form method="POST" action="<?= APP_URL ?>/profile" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="name" class="block text-sm text-[var(--color-text-secondary)] mb-2">Nombre</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($name) ?>"
                                   data-original
                                   autocomplete="given-name" required
                                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                          border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                                          focus:ring-[var(--color-brand)] transition-colors">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm text-[var(--color-text-secondary)] mb-2">Apellidos</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($lastName) ?>"
                                   data-original
                                   autocomplete="family-name" required
                                   class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)]
                                          border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                          focus:outline-none focus:border-[var(--color-brand)] focus:ring-1
                                          focus:ring-[var(--color-brand)] transition-colors">
                        </div>

                        <!-- Email solo lectura -->
                        <div>
                            <label class="block text-sm text-[var(--color-text-secondary)] mb-2">
                                Correo electrónico <span class="text-[var(--color-text-disabled)] text-xs">(no editable)</span>
                            </label>
                            <input type="text"
                                   value="<?= htmlspecialchars($email) ?>"
                                   disabled
                                   class="w-full bg-[var(--color-bg-main)] text-[var(--color-text-muted)] border border-[var(--color-border)]
                                          rounded-xl px-4 py-3 text-sm cursor-not-allowed opacity-60">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-[var(--color-text-primary)] font-semibold
                                       px-6 py-2.5 rounded-xl text-sm transition-colors">
                            Guardar cambios
                        </button>
                        <button type="button" id="cancelBtn"
                                class="text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)] text-sm transition-colors">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    initEditToggle('viewMode', 'editMode', 'editBtn', 'cancelBtn');
});
</script>

<?php
$content   = ob_get_clean();
$csrfToken = $csrfToken ?? '';
require_once APP_PATH . '/Views/layouts/main.php';
