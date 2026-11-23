<?php

// Vercel Serverless PHP Entry Point for Laravel
// This file routes all requests through Laravel's HTTP kernel.

define('LARAVEL_START', microtime(true));

// Set the base path to the project root (one level up from /api/)
$basePath = __DIR__ . '/..';

// Ensure storage directories exist (Vercel's filesystem is ephemeral)
$storageDirs = [
    $basePath . '/storage/framework/cache',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Register the Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
/** @var \Illuminate\Foundation\Application $app */
$app = require_once $basePath . '/bootstrap/app.php';

// Handle the incoming request
$app->handleRequest(
    \Illuminate\Http\Request::capture()
);
