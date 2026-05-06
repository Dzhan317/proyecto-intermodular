<?php
/*
 * Perfil — pestaña Inicio.
 * Muestra datos personales. Nombre y apellidos son editables (toggle JS).
 * Email es solo lectura por diseño (cambio requeriría re-verificación).
 */
ob_start();
?>

<div class="flex flex-col md:flex-row gap-8">

    <?php require_once APP_PATH . '/Views/layouts/partials/profile-sidebar.php'; ?>

    <!-- Contenido principal -->
    <div class="flex-1">

        <h1 class="text-2xl font-bold text-white mb-6">Datos personales</h1>

        <!-- Mensajes de feedback -->
        <?php if (!empty($success)): ?>
            <div class="mb-6 p-3 bg-green-500/10 border border-green-500/30 rounded-xl text-green-400 text-sm">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="bg-[#1F2937] rounded-2xl border border-[#374151] overflow-hidden">

            <!-- Modo vista (por defecto) -->
            <div id="viewMode" class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-base font-semibold text-white">Tus datos</h2>
                    <button id="editBtn" type="button"
                            class="text-[#60A5FA] hover:text-[#93C5FD] text-sm font-medium transition-colors">
                        Editar datos
                    </button>
                </div>

                <dl class="space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[#6B7280] text-sm w-40 flex-shrink-0">Nombre</dt>
                        <dd class="text-white text-sm font-medium" id="displayName">
                            <?= htmlspecialchars($user['name']) ?>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[#6B7280] text-sm w-40 flex-shrink-0">Apellidos</dt>
                        <dd class="text-white text-sm font-medium" id="displayLastName">
                            <?= htmlspecialchars($user['last_name']) ?>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1 pt-4 border-t border-[#374151]">
                        <dt class="text-[#6B7280] text-sm w-40 flex-shrink-0">Correo electrónico</dt>
                        <dd class="text-[#9CA3AF] text-sm">
                            <?= htmlspecialchars($user['email']) ?>
                            <span class="ml-2 text-xs text-[#4B5563]">(no editable)</span>
                        </dd>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <dt class="text-[#6B7280] text-sm w-40 flex-shrink-0">Miembro desde</dt>
                        <dd class="text-[#9CA3AF] text-sm">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Modo edición (oculto por defecto) -->
            <div id="editMode" class="p-6 hidden">
                <h2 class="text-base font-semibold text-white mb-6">Editar datos</h2>

                <form method="POST" action="<?= APP_URL ?>/profile" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="name" class="block text-sm text-[#9CA3AF] mb-2">Nombre</label>
                            <input type="text" id="name" name="name"
                                   value="<?= htmlspecialchars($user['name']) ?>"
                                   data-original
                                   autocomplete="given-name" required
                                   class="w-full bg-[#111827] text-white placeholder-[#6B7280]
                                          border border-[#374151] rounded-xl px-4 py-3 text-sm
                                          focus:outline-none focus:border-[#2563EB] focus:ring-1
                                          focus:ring-[#2563EB] transition-colors">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm text-[#9CA3AF] mb-2">Apellidos</label>
                            <input type="text" id="last_name" name="last_name"
                                   value="<?= htmlspecialchars($user['last_name']) ?>"
                                   data-original
                                   autocomplete="family-name" required
                                   class="w-full bg-[#111827] text-white placeholder-[#6B7280]
                                          border border-[#374151] rounded-xl px-4 py-3 text-sm
                                          focus:outline-none focus:border-[#2563EB] focus:ring-1
                                          focus:ring-[#2563EB] transition-colors">
                        </div>

                        <!-- Email solo lectura -->
                        <div>
                            <label class="block text-sm text-[#9CA3AF] mb-2">
                                Correo electrónico <span class="text-[#4B5563] text-xs">(no editable)</span>
                            </label>
                            <input type="text"
                                   value="<?= htmlspecialchars($user['email']) ?>"
                                   disabled
                                   class="w-full bg-[#0F172A] text-[#6B7280] border border-[#374151]
                                          rounded-xl px-4 py-3 text-sm cursor-not-allowed opacity-60">
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold
                                       px-6 py-2.5 rounded-xl text-sm transition-colors">
                            Guardar cambios
                        </button>
                        <button type="button" id="cancelBtn"
                                class="text-[#9CA3AF] hover:text-white text-sm transition-colors">
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
