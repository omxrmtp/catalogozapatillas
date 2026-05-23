<?php

declare(strict_types=1);

namespace App\Core;

final class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->cloudName = CLOUDINARY_CLOUD_NAME;
        $this->apiKey = CLOUDINARY_API_KEY;
        $this->apiSecret = CLOUDINARY_API_SECRET;
        $this->baseUrl = "https://res.cloudinary.com/{$this->cloudName}/image/upload";
    }

    public function upload(string $filePath, string $publicId, array $options = []): array
    {
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'public_id' => $publicId,
            'overwrite' => true,
        ];

        if (!empty($options['transformation'])) {
            $params['transformation'] = $options['transformation'];
        }

        if (!empty($options['folder'])) {
            $params['folder'] = $options['folder'];
        }

        $params['signature'] = $this->generateSignature($params);
        $params['api_key'] = $this->apiKey;

        if (class_exists('CURLFile')) {
            $params['file'] = new \CURLFile($filePath);
        } else {
            $params['file'] = '@' . $filePath;
        }

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            throw new \RuntimeException('Cloudinary upload error: ' . ($error['error']['message'] ?? 'Unknown error'));
        }

        $result = json_decode($response, true);
        return [
            'public_id' => $result['public_id'],
            'url' => $result['secure_url'],
            'width' => $result['width'] ?? 0,
            'height' => $result['height'] ?? 0,
            'format' => $result['format'] ?? '',
        ];
    }

    public function delete(string $publicId): bool
    {
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'public_id' => $publicId,
        ];

        $params['signature'] = $this->generateSignature($params);
        $params['api_key'] = $this->apiKey;

        $ch = curl_init("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        $result = json_decode($response, true);
        return ($result['result'] ?? '') === 'ok';
    }

    public function buildUrl(string $publicId, array $transform = []): string
    {
        $parts = [];

        if (!empty($transform['width'])) {
            $parts[] = 'w_' . (int)$transform['width'];
        }
        if (!empty($transform['height'])) {
            $parts[] = 'h_' . (int)$transform['height'];
        }
        if (!empty($transform['crop'])) {
            $parts[] = 'c_' . $transform['crop'];
        }
        if (!empty($transform['quality'])) {
            $parts[] = 'q_' . $transform['quality'];
        }
        if (!empty($transform['format'])) {
            $parts[] = 'f_' . $transform['format'];
        }
        if (!empty($transform['fetch_format'])) {
            $parts[] = 'f_' . $transform['fetch_format'];
        }
        if (!empty($transform['flags'])) {
            $parts[] = 'fl_' . $transform['flags'];
        }

        $transStr = !empty($parts) ? implode(',', $parts) . '/' : '';

        return "{$this->baseUrl}/{$transStr}{$publicId}";
    }

    public function isConfigured(): bool
    {
        return $this->cloudName !== '' && $this->apiKey !== '' && $this->apiSecret !== '';
    }

    private function generateSignature(array $params): string
    {
        ksort($params);
        $signString = '';
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }
            $signString .= "{$key}={$value}&";
        }
        $signString = rtrim($signString, '&');
        return sha1($signString . $this->apiSecret);
    }
}
