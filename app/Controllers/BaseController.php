<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;

abstract class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        $data['csrf_token'] = Session::getCsrfToken();
        $data['isLoggedIn'] = Session::isLoggedIn();
        $data['userRole'] = Session::getUserRole();

        extract($data);
        $viewPath = dirname(__DIR__) . "/Views/{$view}.php";
        $layoutPath = dirname(__DIR__) . "/Views/layouts/{$layout}.php";

        if (file_exists($layoutPath)) {
            ob_start();
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo "<p>Vista no encontrada: {$view}</p>";
            }
            $content = ob_get_clean();
            require $layoutPath;
        } else {
            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                echo "<h1>Error</h1><p>Vista no encontrada: {$view}</p>";
            }
        }
    }

    protected function renderAdmin(string $view, array $data = []): void
    {
        $this->render($view, $data, 'admin');
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function requireAuth(): void
    {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Debes iniciar sesión para acceder.');
            $this->redirect('/admin/login');
        }
    }

    protected function requireSuperAdmin(): void
    {
        $this->requireAuth();
        if (!Session::isSuperAdmin()) {
            Session::setFlash('error', 'No tienes permisos para realizar esta acción.');
            $this->redirect('/admin/dashboard');
        }
    }

    protected function getPostData(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return $data ?: $_POST;
    }

    protected function getQueryData(): array
    {
        return $_GET;
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['_csrf_token'] ?? '';
        if (!Session::validateCsrfToken($token)) {
            Session::setFlash('error', 'Token CSRF inválido.');
            return false;
        }
        return true;
    }

    protected function slugify(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
}
