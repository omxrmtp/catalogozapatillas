<?php

declare(strict_types=1);

/**
 * Router para el servidor embebido de PHP (php -S localhost:8000 router.php).
 * Emula las reglas del .htaccess para desarrollo local.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicDir = __DIR__ . '/public';
$docRoot = $publicDir;

// Si el archivo existe en public/, servirlo directamente
$filePath = $publicDir . $uri;
if ($uri !== '/' && is_file($filePath)) {
    return false;
}

// Si existe en la raíz (index.php)
if (is_file(__DIR__ . $uri)) {
    return false;
}

// Front controller
require __DIR__ . '/index.php';
