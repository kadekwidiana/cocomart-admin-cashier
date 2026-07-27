<?php

namespace App\Services\External\Paylabs;

use RuntimeException;

class PaylabsVerifier
{
    protected string $publicKey;

    public function __construct()
    {
        $path = base_path(config('paylabs.public_key_path'));
        $this->publicKey = file_get_contents($path);
    }

    public function verify(string $method, string $endpoint, array $body, string $timestamp, string $signature): bool
    {
        $cleanBody = array_filter($body, fn($value) => $value !== null);
        $minified = json_encode($cleanBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $bodyHash = strtolower(hash('sha256', $minified));
        $stringContent = "{$method}:{$endpoint}:{$bodyHash}:{$timestamp}";

        $publicKeyResource = openssl_pkey_get_public($this->publicKey);

        if ($publicKeyResource === false) {
            throw new RuntimeException('Invalid Paylabs public key. Check paylabs.public_key_path config.');
        }

        $result = openssl_verify(
            $stringContent,
            base64_decode($signature),
            $publicKeyResource,
            OPENSSL_ALGO_SHA256
        );

        return $result === 1;
    }
}
