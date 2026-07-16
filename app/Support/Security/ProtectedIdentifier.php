<?php

namespace App\Support\Security;

use Illuminate\Support\Facades\Crypt;

final class ProtectedIdentifier
{
    public function protect(string $value): array
    {
        $normalized = $this->normalize($value);

        return [
            'encrypted' => Crypt::encryptString($normalized),
            'hash' => hash_hmac('sha256', $normalized, (string) config('platform.identifier_hash_key')),
            'last_four' => mb_substr($normalized, -4),
        ];
    }

    public function normalize(string $value): string
    {
        return mb_strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim($value)));
    }
}
