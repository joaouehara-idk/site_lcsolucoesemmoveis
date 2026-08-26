<?php
/**
 * Global Configuration
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!isset($_SERVER['DB_HOST']) && file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
    if (file_exists(ROOT_PATH . '/.env')) {
        $dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
        $dotenv->safeLoad();
    }
}

require_once ROOT_PATH . '/app/Helpers/functions.php';

define('SITE_NAME', 'LC Soluções em Móveis');
define('CONTACT_EMAIL', 'lcmovel.planejadocg@gmail.com');

date_default_timezone_set('America/Campo_Grande');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . "://" . $host . str_replace('\\', '/', $scriptName);
$baseUrl = rtrim($baseUrl, '/public');
$baseUrl = rtrim($baseUrl, '/');

define('SITE_URL', $baseUrl);
define('BASE_URL', $baseUrl);

define('RECAPTCHA_SITE_KEY', $_SERVER['RECAPTCHA_SITE_KEY'] ?? '');
define('RECAPTCHA_SECRET_KEY', $_SERVER['RECAPTCHA_SECRET_KEY'] ?? '');
define('GOOGLE_MAPS_LINK', $_SERVER['GOOGLE_MAPS_LINK'] ?? '');
define('GEMINI_API_KEY', $_SERVER['GEMINI_API_KEY'] ?? '');

if (file_exists(ROOT_PATH . '/migrations/migrate.php')) {
    require_once ROOT_PATH . '/migrations/migrate.php';
    runMigration();
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
