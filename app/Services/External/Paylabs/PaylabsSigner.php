<?php

namespace App\Services\External\Paylabs;

use RuntimeException;

class PaylabsSigner
{
    protected string $privateKey;

    public function __construct()
    {
        $path = base_path(config('paylabs.private_key_path'));
        $this->privateKey = file_get_contents($path);
    }

    public function sign(string $method, string $endpoint, array $body, string $timestamp): string
    {
        $cleanBody = $this->removeNulls($body);
        $minified = json_encode($cleanBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $bodyHash = strtolower(hash('sha256', $minified));
        $stringContent = "{$method}:{$endpoint}:{$bodyHash}:{$timestamp}";

        $success = openssl_sign($stringContent, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        if (! $success) {
            throw new RuntimeException('Failed to generate Paylabs signature. Check private key format.');
        }

        return base64_encode($signature);
    }

    protected function removeNulls(array $data): array
    {
        return array_filter($data, fn($value) => $value !== null);
    }
}
