<?php

/*
 * Plantilla de configuración del proyecto.
 * Cópiala como config.php y rellena los valores reales.
 * Nunca subas config.php al repositorio.
 */

// Rutas
define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH',   APP_PATH  . '/Views/');

// Aplicación
define('APP_ENV',  'production');              // development | production
define('APP_NAME', 'PrimeLux SmartShop');
define('APP_URL',  'https://primeluxshop.es'); // Sin barra final

// Base de datos (IONOS)
define('DB_HOST',    'your-ionos-db-host');
define('DB_PORT',    '3306');
define('DB_NAME',    'your-database-name');
define('DB_USER',    'your-database-user');
define('DB_PASS',    'your-database-password');
define('DB_CHARSET', 'utf8mb4');

// Sesión
define('SESSION_NAME',     'primelux_session');
define('SESSION_LIFETIME', 3600); // 1 hora

// Seguridad
define('CSRF_TOKEN_NAME',       'csrf_token');
define('TWO_FA_EXPIRY_MINUTES', 15);
define('TWO_FA_MAX_ATTEMPTS',    5);

// SMTP (IONOS) — solo admin@ puede autenticar, no aparece en el FROM
define('MAIL_SMTP_HOST', 'smtp.ionos.es');
define('MAIL_SMTP_PORT', 587);
define('MAIL_SMTP_USER', 'admin@primeluxshop.es');
define('MAIL_SMTP_PASS', 'your-smtp-password');

// Identidades de correo (campo FROM, no son credenciales SMTP)
define('MAIL_NOREPLY_ADDRESS', 'no-reply@primeluxshop.es');
define('MAIL_NOREPLY_NAME',    'PrimeLux SmartShop');
define('MAIL_SUPPORT_ADDRESS', 'soporte@primeluxshop.es');
define('MAIL_SUPPORT_NAME',    'PrimeLux Support');

// Stripe (Fase 6)
define('STRIPE_PUBLIC_KEY',     'your-stripe-public-key-here');
define('STRIPE_SECRET_KEY',     'your-stripe-secret-key-here');
define('STRIPE_WEBHOOK_SECRET', 'your-stripe-webhook-secret-here');

// Override local opcional — crea config.local.php para desarrollo en local
$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
}

// Control de errores según el entorno
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
