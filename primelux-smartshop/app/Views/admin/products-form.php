<?php
/*
 * Admin — Formulario de producto (crear y editar comparten esta vista).
 * $product  → null en crear, array en editar.
 * $isEdit   → bool.
 */
ob_start();

$isEdit     = !empty($product);
$formAction = $isEdit
    ? APP_URL . '/admin/products/' . (int) $product['id'] . '/edit'
    : APP_URL . '/admin/products/create';

$val = [
    'name'        => htmlspecialchars($_POST['name']        ?? $product['name']        ?? ''),
    'description' => htmlspecialchars($_POST['description'] ?? $product['description'] ?? ''),
    'brand'       => htmlspecialchars($_POST['brand']       ?? $product['brand']       ?? ''),
    'base_price'  => $_POST['base_price'] ?? $product['base_price'] ?? '',
    'cost_price'  => $_POST['cost_price'] ?? $product['cost_price'] ?? '',
    'stock'       => $_POST['stock']      ?? $product['stock']      ?? 0,
    'category_id' => (int) ($_POST['category_id'] ?? $product['category_id'] ?? 0),
    'status'      => $_POST['status']     ?? $product['status']     ?? 'active',
];
?>

<div class="pt-2 max-w-2xl">

    <!-- Breadcrumb -->
    <nav class="text-xs text-[var(--color-text-muted)] mb-6">
        <a href="<?= APP_URL ?>/admin/products"
           class="hover:text-[var(--color-text-primary)] transition-colors">Productos</a>
        <span class="mx-1">›</span>
        <span class="text-[var(--color-text-secondary)]">
            <?= $isEdit ? 'Editar producto' : 'Añadir producto' ?>
        </span>
    </nav>

    <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">

        <form method="POST" action="<?= $formAction ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="space-y-5">

                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Nombre *
                    </label>
                    <input type="text" id="name" name="name"
                           value="<?= $val['name'] ?>" required
                           class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                  placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                  rounded-xl px-4 py-3 text-sm focus:outline-none
                                  focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                  transition-colors">
                </div>

                <!-- Descripción -->
                <div>
                    <label for="description" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Descripción
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                     placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                     rounded-xl px-4 py-3 text-sm focus:outline-none
                                     focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                     transition-colors resize-none"><?= $val['description'] ?></textarea>
                </div>

                <!-- Marca + Precio de venta -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="brand" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Marca
                        </label>
                        <input type="text" id="brand" name="brand"
                               value="<?= $val['brand'] ?>"
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                      transition-colors">
                    </div>
                    <div>
                        <label for="base_price" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Precio de venta (€) *
                        </label>
                        <input type="number" id="base_price" name="base_price"
                               value="<?= $val['base_price'] ?>"
                               min="0.01" step="0.01" required
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                      transition-colors">
                    </div>
                </div>

                <!-- Precio de coste + Stock -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="cost_price" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Precio de coste (€)
                        </label>
                        <input type="number" id="cost_price" name="cost_price"
                               value="<?= $val['cost_price'] ?>"
                               min="0" step="0.01"
                               placeholder="0.00"
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                      transition-colors">
                        <p class="text-xs text-[var(--color-text-muted)] mt-1.5">
                            Solo visible para administradores. Se usa para calcular el margen bruto.
                        </p>
                    </div>
                    <div>
                        <label for="stock" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Stock
                        </label>
                        <input type="number" id="stock" name="stock"
                               value="<?= (int) $val['stock'] ?>"
                               min="0" step="1"
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1 focus:ring-[var(--color-brand)]
                                      transition-colors">
                    </div>
                </div>

                <!-- Categoría + Estado -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Categoría *
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                       border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                       focus:outline-none focus:border-[var(--color-brand)]
                                       focus:ring-1 focus:ring-[var(--color-brand)] transition-colors">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>"
                                        <?= $val['category_id'] === (int) $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Estado
                        </label>
                        <select id="status" name="status"
                                class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                       border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                       focus:outline-none focus:border-[var(--color-brand)]
                                       focus:ring-1 focus:ring-[var(--color-brand)] transition-colors">
                            <option value="active"   <?= $val['status'] === 'active'   ? 'selected' : '' ?>>Activo</option>
                            <option value="inactive" <?= $val['status'] === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- Botones -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-[var(--color-border)]">
                <a href="<?= APP_URL ?>/admin/products"
                   class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">
                    ← Volver
                </a>
                <button type="submit"
                        class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                               text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-colors">
                    <?= $isEdit ? 'Guardar cambios' : 'Crear producto' ?>
                </button>
            </div>

        </form>
    </div>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/admin.php';
