<?php

declare(strict_types=1);

$requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestedFile = is_string($requestedPath) ? realpath(__DIR__ . $requestedPath) : false;
$projectRoot = realpath(__DIR__);

if (
    is_string($requestedPath)
    && str_starts_with($requestedPath, '/assets/')
    && $requestedFile !== false
    && $projectRoot !== false
    && str_starts_with($requestedFile, $projectRoot . DIRECTORY_SEPARATOR)
    && is_file($requestedFile)
) {
    return false;
}

require __DIR__ . '/index.php';
