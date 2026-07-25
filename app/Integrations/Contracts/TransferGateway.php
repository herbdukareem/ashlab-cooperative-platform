<?php

namespace App\Integrations\Contracts;

use App\Models\Payout;

interface TransferGateway
{
    /** @return array{reference:string,status:string,raw:array<string,mixed>} */
    public function initiate(Payout $payout): array;

    public function verifyWebhook(string $payload, ?string $signature): bool;

    /** @return array{event_id:string,reference:?string,event:string,failure_reason:?string,payload:array<string,mixed>} */
    public function parseWebhook(array $payload): array;
}
