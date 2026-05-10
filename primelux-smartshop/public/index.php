<?php

/*
 * Punto de entrada de la aplicación.
 * Carga la configuración, arranca la sesión y lanza el router.
 */

define('CONFIG_PATH', realpath(__DIR__ . '/../') . '/config');

if (!file_exists(CONFIG_PATH . '/config.php')) {
    http_response_code(500);
    die('Error crítico: archivo de configuración no encontrado.');
}

require_once CONFIG_PATH . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'secure'   => (APP_ENV === 'production'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once APP_PATH . '/Core/Database.php';
require_once APP_PATH . '/Core/Controller.php';
require_once APP_PATH . '/Core/Router.php';

try {
    $router = new Router();
    require_once APP_PATH . '/routes.php';
    $router->dispatch();
} catch (Throwable $e) {
    error_log('[PrimeLux] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:20px;font-size:13px;">';
        echo htmlspecialchars($e->getMessage()) . "\n\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        // Renderizado defensivo sin depender de APP_PATH ni constantes que puedan no estar definidas
        $view500 = defined('APP_PATH') ? APP_PATH . '/Views/errors/500.php' : null;
        if ($view500 && file_exists($view500)) {
            require_once $view500;
        } else {
            echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
                  <title>Error — PrimeLux SmartShop</title></head>
                  <body style="background:#0D1B2A;color:#fff;font-family:sans-serif;
                               display:flex;align-items:center;justify-content:center;
                               min-height:100vh;margin:0;">
                  <div style="text-align:center;">
                    <h1 style="font-size:3rem;color:#EF4444;margin-bottom:1rem;">500</h1>
                    <p style="color:#9CA3AF;">Ha ocurrido un error. Inténtalo de nuevo más tarde.</p>
                  </div></body></html>';
        }
    }
}
