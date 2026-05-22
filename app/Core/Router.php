<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private array $middleware = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[a-zA-Z0-9_\-]+)', $path);
        $pattern = '#^' . $pattern . '$#';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $pattern,
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function addMiddleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function dispatch(string $method, string $uri): void
    {
        if (str_contains($uri, '://')) {
            $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        }
        $uri = '/' . trim((string)$uri, '/');
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $params = array_map(fn($v) => is_numeric($v) ? (int)$v : $v, $params);

                foreach ($this->middleware as $mw) {
                    $mw();
                }

                foreach ($route['middleware'] as $mw) {
                    $mw();
                }

                if ($route['handler'] instanceof \Closure) {
                    ($route['handler'])(...$params);
                } else {
                    [$controllerClass, $methodName] = $route['handler'];
                    $controller = new $controllerClass();
                    $controller->$methodName(...$params);
                }
                return;
            }
        }

        http_response_code(404);
        $this->renderView('errors/404');
    }

    private function renderView(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . "/Views/{$view}.php";
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            echo '<h1>404 - Página no encontrada</h1>';
        }
    }
}
