<?php

namespace App\Integrations\Sandbox;

use App\Integrations\Contracts\TransferGateway;
use App\Models\Payout;
use Illuminate\Support\Str;

class SandboxTransferGateway implements TransferGateway
{
    public function initiate(Payout $payout): array
    {
        return [
            'reference' => 'sandbox_'.Str::lower(Str::ulid()->toBase32()),
            'status' => 'processing',
            'raw' => ['mode' => 'sandbox', 'payout_reference' => $payout->reference],
        ];
    }

    public function verifyWebhook(string $payload, ?string $signature): bool
    {
        $secret = (string) config('integrations.transfer.webhook_secret');

        return $secret !== '' && is_string($signature)
            && hash_equals(hash_hmac('sha256', $payload, $secret), $signature);
    }

    public function parseWebhook(array $payload): array
    {
        return [
            'event_id' => (string) ($payload['event_id'] ?? ''),
            'reference' => isset($payload['reference']) ? (string) $payload['reference'] : null,
            'event' => (string) ($payload['event'] ?? ''),
            'failure_reason' => isset($payload['failure_reason']) ? (string) $payload['failure_reason'] : null,
            'payload' => $payload,
        ];
    }
}
