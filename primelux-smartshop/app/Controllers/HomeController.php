<?php
declare(strict_types=1);

/*
 * Controlador de la página de inicio.
 * Contenido completo se implementa en Fase 4.
 */

class HomeController extends Controller
{
    public function index(array $params): void
    {
        // Placeholder hasta la Fase 4
        echo '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrimeLux SmartShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: "Sora", sans-serif; }</style>
</head>
<body class="bg-[#0F172A] min-h-screen flex items-center justify-center">
    <div class="text-center">
        <img src="' . APP_URL . '/assets/img/logos/logo_principal.png"
             alt="PrimeLux SmartShop" class="h-20 mx-auto mb-8">
        <h1 class="text-3xl font-bold text-white mb-3">Próximamente</h1>
        <p class="text-[#9CA3AF] mb-8">Estamos preparando algo increíble.</p>
        <a href="' . APP_URL . '/login"
           class="bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-semibold
                  px-6 py-3 rounded-xl text-sm transition-colors">
            Iniciar sesión
        </a>
    </div>
</body>
</html>';
    }
}
