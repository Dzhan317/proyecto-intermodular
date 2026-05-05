<?php

/*
 * Plantilla de configuración del proyecto.
 * Copia como config.php y rellena los valores reales.
 * Nunca subas config.php al repositorio.
 */

define('ROOT_PATH',   dirname(__DIR__));
define('APP_PATH',    ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH',   APP_PATH  . '/Views/');

define('APP_ENV',  'production');
define('APP_NAME', 'PrimeLux SmartShop');
define('APP_URL',  'https://primeluxshop.es');

define('DB_HOST',    'your-ionos-db-host');
define('DB_PORT',    '3306');
define('DB_NAME',    'your-database-name');
define('DB_USER',    'your-database-user');
define('DB_PASS',    'your-database-password');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME',     'primelux_session');
define('SESSION_LIFETIME', 3600);

define('CSRF_TOKEN_NAME',        'csrf_token');
define('TWO_FA_EXPIRY_MINUTES',  10);
define('TWO_FA_MAX_ATTEMPTS',     5);
define('TWO_FA_RESEND_COOLDOWN', 60);

define('MAIL_SMTP_HOST', 'smtp.ionos.es');
define('MAIL_SMTP_PORT', 465);
define('MAIL_SMTP_USER', 'admin@primeluxshop.es');
define('MAIL_SMTP_PASS', 'your-smtp-password');

define('MAIL_NOREPLY_ADDRESS', 'no-reply@primeluxshop.es');
define('MAIL_NOREPLY_NAME',    'PrimeLux SmartShop');
define('MAIL_SUPPORT_ADDRESS', 'soporte@primeluxshop.es');
define('MAIL_SUPPORT_NAME',    'PrimeLux Support');

define('STRIPE_PUBLIC_KEY',     'your-stripe-public-key-here');
define('STRIPE_SECRET_KEY',     'your-stripe-secret-key-here');
define('STRIPE_WEBHOOK_SECRET', 'your-stripe-webhook-secret-here');

$localConfig = __DIR__ . '/config.local.php';
if (file_exists($localConfig)) require_once $localConfig;

if (APP_ENV === 'development') {
    ini_set('display_errors', 1); error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0); error_reporting(0);
    ini_set('log_errors', 1); ini_set('error_log', ROOT_PATH . '/logs/error.log');
}
