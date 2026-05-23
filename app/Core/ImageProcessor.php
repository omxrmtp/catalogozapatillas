<?php

declare(strict_types=1);

namespace App\Core;

final class ImageProcessor
{
    private const MEDIUM_WIDTH = 800;

    private CloudinaryService $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new CloudinaryService();
    }

    public function process(array $file, string $productSlug): array
    {
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

        if (!$this->cloudinary->isConfigured()) {
            throw new \RuntimeException('Cloudinary no está configurado. Revisa tus credenciales.');
        }

        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $productSlug);
        $publicId = "catalogozapatillas/{$slug}";

        $result = $this->cloudinary->upload($file['tmp_name'], $publicId, [
            'transformation' => 'w_' . self::MEDIUM_WIDTH . ',c_fit,q_auto,f_auto',
        ]);

        return [
            'medium'    => [
                'jpg'  => $result['url'],
            ],
            'public_id' => $result['public_id'],
            'url'       => $result['url'],
        ];
    }

    public function deleteProductImages(string $productSlug): void
    {
        if (!$this->cloudinary->isConfigured()) {
            return;
        }

        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $productSlug);
        $publicId = "catalogozapatillas/{$slug}";

        try {
            $this->cloudinary->delete($publicId);
        } catch (\Throwable) {
        }
    }
}
