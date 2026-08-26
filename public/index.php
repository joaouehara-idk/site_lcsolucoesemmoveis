<?php
/**
 * Professional Front Controller
 */

define('ROOT_PATH', dirname(__DIR__));

// Force OPcache to invalidate cached PHP files after deploy
if (function_exists('opcache_reset') && !isset($_SERVER['APP_OPCACHE_CLEARED'])) {
    @opcache_reset();
    @opcache_invalidate(__DIR__ . '/index.php', true);
}

// Session já é iniciada em includes/config.php com flags seguras

require_once ROOT_PATH . '/vendor/autoload.php';

// Exception Handler
set_exception_handler([\App\Core\ExceptionHandler::class, 'handle']);

// Load Environment Variables
$dotenv = \Dotenv\Dotenv::createImmutable(ROOT_PATH);
$dotenv->safeLoad();

// Load Global Configuration and Constants
require_once ROOT_PATH . '/includes/config.php';

// Security Headers
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");

// Initialize Router
$router = new \App\Core\Router();

// Load Routes
require_once ROOT_PATH . '/routes/web.php';

// Resolve Route
$router->resolve();
