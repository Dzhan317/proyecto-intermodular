<?php
/*
 * Perfil del usuario — placeholder para Fase 3.
 * La estructura HTML y las secciones están definidas aquí.
 * La lógica (actualizar datos, cambiar contraseña, gestionar direcciones)
 * se implementa en Fase 3.
 */
$pageTitle = 'Mi perfil — PrimeLux SmartShop';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script>window.APP_URL = '<?= APP_URL ?>';</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="bg-[#0F172A] min-h-screen text-white">

    <div class="max-w-2xl mx-auto px-4 py-12">

        <h1 class="text-3xl font-bold mb-2">Mi perfil</h1>
        <p class="text-[#9CA3AF] text-sm mb-8">
            Gestiona tus datos personales, contraseña y direcciones de envío.
        </p>

        <!-- Datos personales (Fase 3) -->
        <div class="bg-[#1F2937] rounded-2xl p-6 border border-[#374151] mb-4">
            <h2 class="text-lg font-semibold mb-1">Datos personales</h2>
            <p class="text-[#6B7280] text-sm">Disponible en la próxima actualización.</p>
        </div>

        <!-- Cambiar contraseña (Fase 3) -->
        <div class="bg-[#1F2937] rounded-2xl p-6 border border-[#374151] mb-4">
            <h2 class="text-lg font-semibold mb-1">Cambiar contraseña</h2>
            <p class="text-[#6B7280] text-sm">Disponible en la próxima actualización.</p>
        </div>

        <!-- Direcciones de envío (Fase 3) -->
        <div class="bg-[#1F2937] rounded-2xl p-6 border border-[#374151] mb-8">
            <h2 class="text-lg font-semibold mb-1">Direcciones de envío</h2>
            <p class="text-[#6B7280] text-sm">Disponible en la próxima actualización.</p>
        </div>

        <a href="<?= APP_URL ?>/"
           class="text-[#60A5FA] hover:text-[#93C5FD] text-sm transition-colors">
            ← Volver al inicio
        </a>

    </div>

</body>
</html>
