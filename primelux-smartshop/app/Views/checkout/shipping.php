<?php
/*
 * Checkout — Paso 1: Datos de envío y método.
 * Diseño basado en docs/designs/checkout/checkout_direccion.png
 * y checkout_envio.png (unidos en un solo paso para simplicidad).
 */
ob_start();
$checkoutStep = 'shipping';

// Precarga la dirección guardada si existe
$pre = $savedAddress ?? [];
$street     = htmlspecialchars($_POST['street']      ?? $pre['street']      ?? '');
$city       = htmlspecialchars($_POST['city']        ?? $pre['city']        ?? '');
$province   = htmlspecialchars($_POST['province']    ?? $pre['province']    ?? '');
$postalCode = htmlspecialchars($_POST['postal_code'] ?? $pre['postal_code'] ?? '');
$phone      = htmlspecialchars($_POST['phone']       ?? $pre['phone']       ?? '');
$email      = htmlspecialchars($user['email']        ?? '');
$name       = htmlspecialchars($user['name']         ?? '');
$lastName   = htmlspecialchars($user['last_name']    ?? '');
?>

<?php if (!empty($error)): ?>
    <div class="mb-4 p-3 alert-error rounded-xl text-sm">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- ── Formulario ─────────────────────────────────────────────── -->
    <div class="lg:col-span-2 space-y-6">

        <form method="POST" action="<?= APP_URL ?>/checkout/shipping" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- Opciones de entrega -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6">
                <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-6">
                    Opciones de entrega
                </h2>

                <!-- Email — autocompletado, solo lectura -->
                <div class="mb-5">
                    <label class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Correo electrónico
                    </label>
                    <input type="text" value="<?= $email ?>" disabled
                           class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-muted)]
                                  border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                  cursor-not-allowed opacity-70">
                </div>

                <!-- Nombre + Apellidos -->
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Nombre
                        </label>
                        <input type="text" value="<?= $name ?>" disabled
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                      cursor-not-allowed opacity-70">
                    </div>
                    <div>
                        <label class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Apellidos
                        </label>
                        <input type="text" value="<?= $lastName ?>" disabled
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                      cursor-not-allowed opacity-70">
                    </div>
                </div>

                <!-- Dirección -->
                <div class="mb-5">
                    <label for="street" class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Dirección *
                    </label>
                    <input type="text" id="street" name="street"
                           value="<?= $street ?>"
                           placeholder="Ej.: C/ Tejedores, 5"
                           autocomplete="street-address"
                           class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                  placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                  rounded-xl px-4 py-3 text-sm focus:outline-none
                                  focus:border-[var(--color-brand)] focus:ring-1
                                  focus:ring-[var(--color-brand)] transition-colors">
                </div>

                <!-- CP + Ciudad + País -->
                <div class="grid grid-cols-3 gap-4 mb-5">
                    <div>
                        <label for="postal_code"
                               class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Código postal *
                        </label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="<?= $postalCode ?>"
                               placeholder="28001"
                               maxlength="5"
                               inputmode="numeric"
                               autocomplete="postal-code"
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1
                                      focus:ring-[var(--color-brand)] transition-colors">
                    </div>
                    <div>
                        <label for="city"
                               class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            Ciudad *
                        </label>
                        <input type="text" id="city" name="city"
                               value="<?= $city ?>"
                               placeholder="Madrid"
                               autocomplete="address-level2"
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                      placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                      rounded-xl px-4 py-3 text-sm focus:outline-none
                                      focus:border-[var(--color-brand)] focus:ring-1
                                      focus:ring-[var(--color-brand)] transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm text-[var(--color-text-secondary)] mb-2">
                            País
                        </label>
                        <input type="text" value="España" disabled
                               class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-muted)]
                                      border border-[var(--color-border)] rounded-xl px-4 py-3 text-sm
                                      cursor-not-allowed opacity-70">
                    </div>
                </div>

                <!-- Provincia -->
                <div class="mb-5">
                    <label for="province"
                           class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Provincia *
                    </label>
                    <input type="text" id="province" name="province"
                           value="<?= $province ?>"
                           placeholder="Madrid"
                           class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                  placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                  rounded-xl px-4 py-3 text-sm focus:outline-none
                                  focus:border-[var(--color-brand)] focus:ring-1
                                  focus:ring-[var(--color-brand)] transition-colors">
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone"
                           class="block text-sm text-[var(--color-text-secondary)] mb-2">
                        Número de teléfono *
                    </label>
                    <input type="text" id="phone" name="phone"
                           value="<?= $phone ?>"
                           placeholder="600 000 000"
                           maxlength="9"
                           inputmode="numeric"
                           autocomplete="tel-national"
                           class="w-full bg-[var(--color-bg-secondary)] text-[var(--color-text-primary)]
                                  placeholder-[var(--color-text-muted)] border border-[var(--color-border)]
                                  rounded-xl px-4 py-3 text-sm focus:outline-none
                                  focus:border-[var(--color-brand)] focus:ring-1
                                  focus:ring-[var(--color-brand)] transition-colors">
                </div>
            </div>

            <!-- Método de envío -->
            <div class="bg-[var(--color-bg-card)] rounded-2xl border border-[var(--color-border)] p-6 mt-6">
                <h2 class="text-base font-semibold text-[var(--color-text-primary)] mb-6">
                    Método de envío
                </h2>

                <!-- id para que checkout.js lo encuentre -->
                <div id="shipping-options" class="space-y-4">
                    <?php foreach ($shippingOptions as $key => $option): ?>
                        <label data-shipping="<?= $key ?>"
                               class="flex items-center gap-4 p-5 rounded-xl border cursor-pointer transition-all duration-150
                                      <?= $selectedShipping === $key
                                          ? 'border-[var(--color-brand)] bg-[var(--color-brand)]/5'
                                          : 'border-[var(--color-border)] hover:border-[var(--color-text-muted)]' ?>">

                            <!-- Radio más grande con accent-color -->
                            <input type="radio" name="shipping_type" value="<?= $key ?>"
                                   <?= $selectedShipping === $key ? 'checked' : '' ?>
                                   class="w-5 h-5 accent-[var(--color-brand)] flex-shrink-0 cursor-pointer">

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-[var(--color-text-primary)]">
                                        <?= htmlspecialchars($option['label']) ?>
                                    </span>
                                    <span class="text-sm font-semibold text-[var(--color-warning)]">
                                        <?= $option['cost'] > 0
                                            ? number_format($option['cost'], 2, ',', '.') . ' €'
                                            : 'Gratis' ?>
                                    </span>
                                </div>
                                <p class="text-xs text-[var(--color-text-muted)] mt-1">
                                    <?= htmlspecialchars($option['description']) ?>
                                </p>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Botón -->
            <button type="submit"
                    class="w-full bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)]
                           text-white font-semibold py-3.5 rounded-xl text-sm
                           transition-colors mt-6">
                Continuar al pago
            </button>

        </form>
    </div>

    <!-- ── Resumen del carrito ─────────────────────────────────────── -->
    <?php require APP_PATH . '/Views/checkout/partials/cart-summary.php'; ?>

</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/checkout.php';
