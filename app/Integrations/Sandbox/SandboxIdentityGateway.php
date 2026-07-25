<?php

namespace App\Integrations\Sandbox;

use App\Integrations\Contracts\IdentityGateway;
use Illuminate\Support\Str;

class SandboxIdentityGateway implements IdentityGateway
{
    public function verify(string $type, string $identifier, array $subject = []): array
    {
        $normalized = preg_replace('/\D+/', '', $identifier) ?? '';
        $validLength = match (strtolower($type)) {
            'nin', 'bvn' => strlen($normalized) === 11,
            default => strlen($normalized) >= 4,
        };

        return [
            'verified' => $validLength && ! str_ends_with($normalized, '0000'),
            'reference' => 'sandbox_'.Str::lower(Str::ulid()->toBase32()),
            'reason' => $validLength ? null : 'Identifier failed sandbox format validation.',
            'attributes' => ['mode' => 'sandbox', 'type' => strtolower($type)],
        ];
    }
}
