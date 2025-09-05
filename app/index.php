<?php
// Get the requested URI
$request = $_SERVER['REQUEST_URI'];

// Remove query string and trailing slash
$path = parse_url($request, PHP_URL_PATH);
$path = rtrim($path, '/');

// Handle root
if ($path === '') {
    include __DIR__ . '/index.html';
    exit;
}

// Build file path
$filePath = __DIR__ . $path;

// Serve HTML if it exists 
if (is_file($filePath . '.html')) {
    include $filePath . '.html';
    exit;
}

// If no file found, show 404
http_response_code(404);
include __DIR__ . '/404.html';
exit;
