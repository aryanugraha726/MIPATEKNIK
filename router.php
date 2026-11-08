<?php
// router.php - Fallback router for Wasmer PHP built-in web server

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If a static file exists in public/, serve it directly (CSS, JS, images)
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Otherwise, route all requests through Laravel's front controller
require_once __DIR__ . '/public/index.php';