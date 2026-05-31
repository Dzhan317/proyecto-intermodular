<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Checkout — PrimeLux SmartShop') ?></title>
    <meta name="robots" content="noindex">

    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon/favicon.ico">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>window.APP_URL = '<?= APP_URL ?>';</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sora: ['Sora', 'sans-serif'] },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script src="<?= APP_URL ?>/assets/js/checkout.js"></script>
</head>
<body class="bg-[var(--color-bg-main)] text-[var(--color-text-primary)] min-h-screen flex flex-col font-sora">

    <!-- Header simplificado — solo logo e icono carrito -->
    <header class="bg-[var(--color-bg-surface)] border-b border-[var(--color-divider)]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= APP_URL ?>/">
                    <img src="<?= APP_URL ?>/assets/img/logos/logo_principal_header.webp"
                         alt="PrimeLux SmartShop" class="h-14 w-auto">
                </a>
                <a href="<?= APP_URL ?>/cart"
                   class="p-2 rounded-xl text-[var(--color-text-secondary)]
                          hover:text-[var(--color-text-primary)] hover:bg-[var(--color-bg-card)]
                          transition-colors" title="Volver al carrito">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184
                                 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- ── Stepper ───────────────────────────────────────────────────────────── -->
    <div class="bg-[var(--color-bg-surface)] border-b border-[var(--color-divider)]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            <?php
            $steps = [
                'cart'     => 'Carrito',
                'shipping' => 'Envío',
                'payment'  => 'Pago',
                'success'  => 'Confirmación',
            ];
            $currentStep = $checkoutStep ?? 'shipping';
            $stepOrder   = array_keys($steps);
            $currentIdx  = array_search($currentStep, $stepOrder);
            ?>

            <div class="flex items-center justify-center">
                <?php foreach ($steps as $key => $label):
                    $idx       = array_search($key, $stepOrder);
                    $isDone    = $idx < $currentIdx;
                    $isCurrent = $key === $currentStep;
                    $isPending = $idx > $currentIdx;
                    $number    = $idx + 1;
                ?>

                    <?php if ($idx > 0): ?>
                        <!-- Línea conectora -->
                        <div class="flex-1 max-w-[80px] mx-1 sm:mx-2">
                            <div class="h-px <?= $isDone ? 'bg-[var(--color-success)]' : 'bg-[var(--color-border)]' ?> transition-colors"></div>
                        </div>
                    <?php endif; ?>

                    <!-- Paso -->
                    <div class="flex flex-col items-center gap-2">

                        <!-- Círculo -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 transition-all
                            <?php if ($isDone): ?>
                                bg-[var(--color-success)] text-white
                            <?php elseif ($isCurrent): ?>
                                bg-[var(--color-brand)] text-white ring-4 ring-[var(--color-brand)]/20
                            <?php else: ?>
                                bg-[var(--color-bg-card)] text-[var(--color-text-disabled)] border border-[var(--color-border)]
                            <?php endif; ?>">

                            <?php if ($isDone): ?>
                                <!-- Check -->
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414
                                             0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1
                                             1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            <?php else: ?>
                                <span class="text-sm font-semibold"><?= $number ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Label -->
                        <span class="text-xs sm:text-sm font-medium whitespace-nowrap
                            <?php if ($isCurrent): ?>
                                text-[var(--color-text-primary)]
                            <?php elseif ($isDone): ?>
                                text-[var(--color-success)]
                            <?php else: ?>
                                text-[var(--color-text-disabled)]
                            <?php endif; ?>">
                            <?= $label ?>
                        </span>
                    </div>

                <?php endforeach; ?>
            </div>

        </div>
    </div>

    <!-- Contenido -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?= $content ?>
    </main>

</body>
</html>
