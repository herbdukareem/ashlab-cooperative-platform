<?php

namespace App\Http\Controllers\Api;

use App\Domain\Payouts\Actions\ProcessPayout;
use App\Http\Controllers\Controller;
use App\Integrations\Contracts\TransferGateway;
use App\Models\Payout;
use App\Models\Cooperative;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferWebhookController extends Controller
{
    public function __invoke(Request $request, TransferGateway $gateway, ProcessPayout $process, TenantContext $tenant): JsonResponse
    {
        $raw = $request->getContent();
        abort_unless($gateway->verifyWebhook($raw, $request->header('X-Webhook-Signature')), 401, 'Invalid webhook signature.');

        $event = $gateway->parseWebhook($request->json()->all());
        abort_if($event['event_id'] === '' || $event['reference'] === null, 422, 'Webhook event and reference are required.');
        abort_unless(in_array($event['event'], ['paid', 'failed', 'reversed', 'processing'], true), 422, 'Unsupported payout event.');

        $payout = Payout::query()->withoutGlobalScopes()
            ->where('provider', config('integrations.transfer.driver'))
            ->where('provider_reference', $event['reference'])
            ->firstOrFail();
        $tenant->set(Cooperative::query()->findOrFail($payout->cooperative_id));
        $process->providerEvent($payout, [
            'provider_event_id' => $event['event_id'],
            'event_type' => $event['event'],
            'provider_reference' => $event['reference'],
            'failure_reason' => $event['failure_reason'],
            'payload' => $event['payload'],
        ]);

        return response()->json(['received' => true]);
    }
}
