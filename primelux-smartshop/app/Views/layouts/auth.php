<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'PrimeLux SmartShop') ?></title>
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
                        brand:   { DEFAULT: '#2563EB', hover: '#1D4ED8', active: '#1E40AF' },
                        accent:  { DEFAULT: '#F59E0B', hover: '#D97706' },
                        surface: { main: '#0F172A', secondary: '#111827', card: '#1F2937', hover: '#374151' },
                        'req-ok':      '#10B981',
                        'req-pending': '#6B7280',
                    },
                }
            },
            safelist: ['text-req-ok','text-req-pending','bg-req-ok','bg-req-pending']
        }
    </script>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
    <script src="<?= APP_URL ?>/assets/js/auth.js"></script>
</head>
<body class="bg-[#0F172A] min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="flex justify-center mb-8">
            <a href="<?= APP_URL ?>">
                <img src="<?= APP_URL ?>/assets/img/logos/logo_principal.png" alt="PrimeLux SmartShop" class="h-16 w-auto">
            </a>
        </div>
        <div class="bg-[#1F2937] rounded-2xl p-8 border border-[#374151]">
            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/30 rounded-lg text-red-400 text-sm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 bg-green-500/10 border border-green-500/30 rounded-lg text-green-400 text-sm"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?= $content ?>
        </div>
        <p class="text-center text-[#6B7280] text-xs mt-6">
            Al continuar, aceptas nuestros
            <a href="<?= APP_URL ?>/terms"   class="text-[#60A5FA] hover:text-[#93C5FD]">Términos de uso</a>
            y la
            <a href="<?= APP_URL ?>/privacy" class="text-[#60A5FA] hover:text-[#93C5FD]">Política de privacidad</a>.
        </p>
    </div>
</body>
</html>
