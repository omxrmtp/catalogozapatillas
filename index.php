<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

date_default_timezone_set(APP_TIMEZONE);

Session::start();

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

$cloudinaryHost = 'https://res.cloudinary.com';
$csp = "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.tailwindcss.com; "
    . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://fonts.googleapis.com; "
    . "img-src 'self' data: {$cloudinaryHost}; "
    . "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com; "
    . "connect-src 'self' https://cdn.jsdelivr.net {$cloudinaryHost};";

if (APP_ENV === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    $csp .= "; upgrade-insecure-requests";
}

header("Content-Security-Policy: $csp");

$router = new Router();

$router->get('/health', function (): void {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'time' => date('c')]);
});

$router->get('/', [HomeController::class, 'index']);
$router->get('/catalogo', [ProductController::class, 'catalog']);
$router->get('/producto/{slug}', [ProductController::class, 'detail']);
$router->get('/api/search', [ProductController::class, 'searchAjax']);

$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);

$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/productos', [AdminController::class, 'products']);
$router->get('/admin/productos/crear', [AdminController::class, 'productCreate']);
$router->post('/admin/productos/crear', [AdminController::class, 'productCreate']);
$router->get('/admin/productos/editar/{id}', [AdminController::class, 'productEdit']);
$router->post('/admin/productos/editar/{id}', [AdminController::class, 'productEdit']);
$router->post('/admin/productos/toggle/{id}', [AdminController::class, 'productToggle']);
$router->post('/admin/productos/eliminar/{id}', [AdminController::class, 'productDelete']);
$router->post('/admin/productos/imagen/eliminar/{id}', [AdminController::class, 'imageDelete']);
$router->get('/admin/categorias', [AdminController::class, 'categories']);
$router->post('/admin/categorias/crear', [AdminController::class, 'categoryCreate']);
$router->post('/admin/categorias/editar/{id}', [AdminController::class, 'categoryEdit']);
$router->get('/admin/marcas', [AdminController::class, 'brands']);
$router->post('/admin/marcas/crear', [AdminController::class, 'brandCreate']);
$router->post('/admin/marcas/editar/{id}', [AdminController::class, 'brandEdit']);
$router->get('/admin/usuarios', [AdminController::class, 'users']);
$router->get('/admin/usuarios/crear', [AdminController::class, 'userCreate']);
$router->post('/admin/usuarios/crear', [AdminController::class, 'userCreate']);
$router->get('/admin/usuarios/editar/{id}', [AdminController::class, 'userEdit']);
$router->post('/admin/usuarios/editar/{id}', [AdminController::class, 'userEdit']);
$router->get('/admin/auditoria', [AdminController::class, 'auditLogView']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = Request::path();

try {
    $router->dispatch($method, $uri);
} catch (\Throwable $e) {
    if (APP_ENV === 'development') {
        throw $e;
    }
    http_response_code(500);
    require ROOT_PATH . '/app/Views/errors/500.php';
}
