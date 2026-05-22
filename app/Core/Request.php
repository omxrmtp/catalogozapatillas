<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Normaliza la URI para hosting compartido (InfinityFree, etc.).
 * Corrige prefijos /public/ y subcarpetas en REQUEST_URI.
 */
final class Request
{
    public static function path(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) && $path !== '' ? $path : '/';

        // Rewrite interno: /public/catalogo → /catalogo
        if (str_starts_with($path, '/public/')) {
            $path = substr($path, 7) ?: '/';
        } elseif ($path === '/public') {
            $path = '/';
        }

        // Subcarpeta opcional (ej. htdocs/mitienda/)
        $subfolder = trim((string)(getenv('APP_SUBFOLDER') ?: ''), '/');
        if ($subfolder !== '') {
            $prefix = '/' . $subfolder;
            if (str_starts_with($path, $prefix . '/')) {
                $path = substr($path, strlen($prefix)) ?: '/';
            } elseif ($path === $prefix) {
                $path = '/';
            }
        }

        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
