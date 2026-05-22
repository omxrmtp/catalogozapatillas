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
