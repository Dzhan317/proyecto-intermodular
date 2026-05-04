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

    if (APP_ENV === 'development') {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . "\n" . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        $view = APP_PATH . '/Views/errors/500.php';
        if (file_exists($view)) require_once $view;
        else echo '<h1>Error interno del servidor</h1>';
    }
}
