<?php

declare(strict_types=1);

namespace App\Core;

final class ImageProcessor
{
    private const THUMBNAIL_WIDTH = 400;
    private const MEDIUM_WIDTH = 800;
    private const LARGE_WIDTH = 1200;
    private const WEBP_QUALITY = 80;
    private const AVIF_QUALITY = 60;

    public function process(array $file, string $productSlug): array
    {
        $public = defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 2) . '/public';
        $uploadDir = $public . '/uploads/' . $productSlug;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

        if (!in_array($extension, $allowed, true)) {
            throw new \RuntimeException('Formato de imagen no permitido.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('Tipo MIME de imagen no permitido.');
        }

        $image = $this->createImage($file['tmp_name'], $mime);
        if (!$image) {
            throw new \RuntimeException('No se pudo procesar la imagen.');
        }

        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        $savedFiles = [];

        $sizes = [
            'thumbnail' => self::THUMBNAIL_WIDTH,
            'medium'    => self::MEDIUM_WIDTH,
            'large'     => self::LARGE_WIDTH,
        ];

        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $productSlug);

        foreach ($sizes as $type => $width) {
            if ($originalWidth <= $width) {
                $resized = $image;
            } else {
                $ratio = $width / $originalWidth;
                $newHeight = (int)round($originalHeight * $ratio);
                $resized = imagescale($image, $width, $newHeight);
            }

            $baseFilename = "{$slug}-{$type}";
            $jpgPath = "{$uploadDir}/{$baseFilename}.jpg";
            $webpPath = "{$uploadDir}/{$baseFilename}.webp";
            $avifPath = "{$uploadDir}/{$baseFilename}.avif";

            imagejpeg($resized, $jpgPath, 85);
            $savedFiles[$type]['jpg'] = "/uploads/{$productSlug}/{$baseFilename}.jpg";

            if (function_exists('imagewebp')) {
                imagewebp($resized, $webpPath, self::WEBP_QUALITY);
                $savedFiles[$type]['webp'] = "/uploads/{$productSlug}/{$baseFilename}.webp";
            }

            if (function_exists('imageavif')) {
                imageavif($resized, $avifPath, self::AVIF_QUALITY);
                $savedFiles[$type]['avif'] = "/uploads/{$productSlug}/{$baseFilename}.avif";
            }

            if ($resized !== $image) {
                imagedestroy($resized);
            }
        }

        imagedestroy($image);

        return $savedFiles;
    }

    private function createImage(string $path, string $mime): ?\GdImage
    {
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            'image/avif' => @imagecreatefromavif($path),
            default      => null,
        };
    }

    public function deleteProductImages(string $productSlug): void
    {
        $public = defined('PUBLIC_PATH') ? PUBLIC_PATH : dirname(__DIR__, 2) . '/public';
        $dir = $public . '/uploads/' . $productSlug;
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($dir);
        }
    }
}
