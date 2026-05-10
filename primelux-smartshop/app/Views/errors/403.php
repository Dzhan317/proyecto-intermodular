<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso denegado | PrimeLux</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="bg-[var(--color-bg-main)] text-[var(--color-text-primary)] min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-[var(--color-warning)] mb-4">403</h1>
        <p class="text-xl text-[var(--color-text-secondary)] mb-8">No tienes permiso para acceder a esta página.</p>
        <a href="<?= APP_URL ?>" class="bg-[var(--color-brand)] hover:bg-[var(--color-brand-hover)] text-[var(--color-text-primary)] px-6 py-3 rounded-lg transition">
            Volver al inicio
        </a>
    </div>
</body>
</html>
