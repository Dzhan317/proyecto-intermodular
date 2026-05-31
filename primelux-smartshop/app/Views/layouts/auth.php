<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'PrimeLux SmartShop', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="PrimeLux SmartShop — Tu supermercado digital de confianza. Venta online de productos premium.">

    <link rel="icon" type="image/x-icon"            href="<?= APP_URL ?>/assets/img/favicon/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_URL ?>/assets/img/favicon/favicon-16x16.png">
    <link rel="apple-touch-icon"                    href="<?= APP_URL ?>/assets/img/favicon/apple-touch-icon.png">
    <link rel="manifest"                            href="<?= APP_URL ?>/assets/img/favicon/site.webmanifest">

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
                    colors: {
                        brand:   { DEFAULT: 'var(--color-brand)', hover: 'var(--color-brand-hover)', active: 'var(--color-brand-active)' },
                        accent:  { DEFAULT: 'var(--color-accent)', hover: 'var(--color-accent-hover)' },
                        surface: {
                            main:      'var(--color-bg-main)',
                            secondary: 'var(--color-bg-secondary)',
                            card:      'var(--color-bg-card)',
                            hover:     'var(--color-bg-hover)',
                        },
                        'req-ok':      'var(--color-success)',
                        'req-pending': 'var(--color-text-muted)',
                    },
                }
            },
            safelist: ['text-req-ok','text-req-pending','bg-req-ok','bg-req-pending']
        }
    </script>

    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
    <script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</head>
<body class="bg-[var(--color-bg-surface)] min-h-screen flex flex-col items-center justify-center px-4">

    <?php if (!empty($_SESSION['csrf_error'])): ?>
        <div class="fixed top-0 left-0 right-0 z-50 bg-[var(--color-warning)] text-[var(--color-bg-main)]">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2
                                 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['csrf_error']) ?></p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        class="flex-shrink-0 text-[var(--color-text-primary)] hover:opacity-70 transition-opacity"
                        aria-label="Cerrar aviso">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="h-12"></div>
        <?php unset($_SESSION['csrf_error']); ?>
    <?php endif; ?>

    <div class="w-full max-w-md">

        <div class="flex justify-center mb-8">
            <a href="<?= APP_URL ?>">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_principal_auth.webp"
                     alt="PrimeLux SmartShop" class="h-56 w-auto">
            </a>
        </div>

        <div class="auth-card rounded-2xl p-8">

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 alert-error rounded-lg text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 alert-success rounded-lg text-sm">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </div>

        <p class="text-center text-[var(--color-text-muted)] text-xs mt-6">
            Al continuar, aceptas nuestros
            <a href="<?= APP_URL ?>/legal/terms"   class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]">Términos de uso</a>
            y la
            <a href="<?= APP_URL ?>/legal/privacy" class="text-[var(--color-link)] hover:text-[var(--color-link-hover)]">Política de privacidad</a>.
        </p>

    </div>

</body>
</html>
