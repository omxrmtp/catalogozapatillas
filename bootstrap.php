<?php

declare(strict_types=1);

/**
 * Rutas del proyecto (local con /public o despliegue en htdocs).
 */
if (!defined('ROOT_PATH')) {
    $root = __DIR__;
    if (!is_dir($root . '/app') && is_dir(dirname($root) . '/app')) {
        $root = dirname($root);
    }
    define('ROOT_PATH', $root);
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', is_dir(ROOT_PATH . '/public') ? ROOT_PATH . '/public' : ROOT_PATH);
}

require_once ROOT_PATH . '/app/Core/Env.php';

\App\Core\Env::load(ROOT_PATH);

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';

if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

/**
 * Genera una URL de Cloudinary con transformaciones.
 * - Si la ruta es local (/uploads/), la devuelve sin cambios.
 * - Si es una URL de Cloudinary completa, extrae el public_id y reconstruye con transformaciones.
 * - Si es solo un public_id, construye la URL desde cero.
 */
function cloudinary_url(?string $path, array $transform = []): string
{
    if (empty($path)) {
        return '';
    }

    if (str_starts_with($path, '/uploads/')) {
        return $path;
    }

    static $service = null;
    if ($service === null) {
        $service = new \App\Core\CloudinaryService();
    }
    if (!$service->isConfigured()) {
        return $path;
    }

    $publicId = $path;

    if (str_contains($path, 'res.cloudinary.com')) {
        $parts = parse_url($path);
        $pathParts = explode('/', $parts['path'] ?? '');
        $uploadIndex = array_search('upload', $pathParts, true);
        if ($uploadIndex !== false && isset($pathParts[$uploadIndex + 2])) {
            $publicId = implode('/', array_slice($pathParts, $uploadIndex + 2));
            $publicId = preg_replace('/\.[^.]+$/', '', $publicId);
        }
    }

    return $service->buildUrl($publicId, $transform);
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
