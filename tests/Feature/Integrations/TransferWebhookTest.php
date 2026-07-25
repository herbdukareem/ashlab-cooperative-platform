<?php

namespace Tests\Feature\Integrations;

use App\Enums\PayoutStatus;
use App\Enums\PayoutType;
use App\Models\Cooperative;
use App\Models\Payout;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_webhook_updates_the_correct_tenant_payout_once(): void
    {
        config(['integrations.transfer.webhook_secret' => 'test-webhook-secret']);
        $cooperative = Cooperative::factory()->create();
        app(TenantContext::class)->set($cooperative);
        $payout = Payout::query()->create([
            'reference' => 'PAY-TEST-001',
            'idempotency_key' => 'test-payout-001',
            'type' => PayoutType::MemberRefund,
            'amount_minor' => 25000,
            'currency' => 'NGN',
            'beneficiary_name' => 'Pilot Member',
            'status' => PayoutStatus::Processing,
            'provider' => 'sandbox',
            'provider_reference' => 'sandbox-transfer-001',
        ]);
        app(TenantContext::class)->clear();

        $payload = json_encode([
            'event_id' => 'sandbox-event-001',
            'reference' => 'sandbox-transfer-001',
            'event' => 'paid',
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $payload, 'test-webhook-secret');

        $this->call('POST', '/api/v1/webhooks/transfers', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
        ], $payload)->assertOk()->assertJson(['received' => true]);

        $this->call('POST', '/api/v1/webhooks/transfers', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
        ], $payload)->assertOk();

        $this->assertSame(PayoutStatus::Paid, $payout->refresh()->status);
        $this->assertDatabaseCount('payout_events', 1);
    }

    public function test_unsigned_webhook_is_rejected(): void
    {
        config(['integrations.transfer.webhook_secret' => 'test-webhook-secret']);
        $this->postJson('/api/v1/webhooks/transfers', [])->assertUnauthorized();
    }
}
