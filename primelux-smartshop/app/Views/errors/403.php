<!-- Página mostrada cuando el usuario no tiene permisos para acceder. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso denegado | PrimeLux</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0F172A] text-white min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-[#F59E0B] mb-4">403</h1>
        <p class="text-xl text-gray-400 mb-8">No tienes permiso para acceder a esta página.</p>
        <a href="<?= APP_URL ?>" class="bg-[#2563EB] hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition">
            Volver al inicio
        </a>
    </div>
</body>
</html>
