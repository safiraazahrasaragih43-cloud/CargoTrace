<?php

define('LARAVEL_START', microtime(true));

$basePath = __DIR__ . '/..';

// On Vercel serverless, ONLY /tmp is writable.
$tmpStorage = '/tmp/storage';
$storageDirs = [
    $tmpStorage . '/framework/cache',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
    $tmpStorage . '/app/public',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Redirect Blade view compilations, caches & storage to /tmp
putenv('VIEW_COMPILED_PATH=' . $tmpStorage . '/framework/views');
putenv('APP_STORAGE=' . $tmpStorage);
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';
$_ENV['APP_STORAGE'] = $tmpStorage;
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';

// Safe serverless defaults for Vercel
if (empty($_ENV['APP_KEY']) && empty(getenv('APP_KEY'))) {
    // Generate secure dynamic runtime key if not explicitly set in Vercel environment variables
    $runtimeKey = 'base64:' . base64_encode(hash('sha256', (getenv('VERCEL_URL') ?: 'CargoTrace-Production-Salt'), true));
    putenv("APP_KEY={$runtimeKey}");
    $_ENV['APP_KEY'] = $runtimeKey;
}

if (empty($_ENV['SESSION_DRIVER']) && empty(getenv('SESSION_DRIVER'))) {
    putenv('SESSION_DRIVER=cookie');
    $_ENV['SESSION_DRIVER'] = 'cookie';
}

if (empty($_ENV['CACHE_STORE']) && empty(getenv('CACHE_STORE'))) {
    putenv('CACHE_STORE=array');
    $_ENV['CACHE_STORE'] = 'array';
}

if (empty($_ENV['LOG_CHANNEL']) && empty(getenv('LOG_CHANNEL'))) {
    putenv('LOG_CHANNEL=stderr');
    $_ENV['LOG_CHANNEL'] = 'stderr';
}

// Prevent Symfony from treating /api as script folder and stripping it from PathInfo
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Auto-fallback to bundled SQLite in /tmp if no external MySQL database is configured
$hasExternalDb = !empty(getenv('DB_HOST')) && getenv('DB_HOST') !== '127.0.0.1' && getenv('DB_HOST') !== 'localhost';
if (!$hasExternalDb) {
    $sqliteSource = $basePath . '/database/database.sqlite';
    $sqliteTarget = '/tmp/database.sqlite';

    if (file_exists($sqliteSource) && (!file_exists($sqliteTarget) || filesize($sqliteTarget) === 0)) {
        @copy($sqliteSource, $sqliteTarget);
        @chmod($sqliteTarget, 0666);
    }

    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $sqliteTarget);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteTarget;
}

// Register Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

if (method_exists($app, 'useStoragePath')) {
    $app->useStoragePath($tmpStorage);
}

// Handle the incoming request
$app->handleRequest(
    \Illuminate\Http\Request::capture()
);
