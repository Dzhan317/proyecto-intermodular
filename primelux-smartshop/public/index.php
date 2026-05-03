<?php
declare(strict_types=1);

/*
 * Punto de entrada de la aplicación.
 * Carga la configuración, arranca la sesión y lanza el router.
 */

define('CONFIG_PATH', dirname(__DIR__) . '/config');

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

$router = new Router();
require_once APP_PATH . '/routes.php';
$router->dispatch();
