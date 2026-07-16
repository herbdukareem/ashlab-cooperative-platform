<?php

namespace App\Domain\Members\Actions;

use App\Models\CooperativeSetting;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;

class GenerateMembershipNumber
{
    public function __construct(private readonly TenantContext $context) {}

    public function execute(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');
        $tenantId = $this->context->get()->id;

        DB::table('member_number_sequences')->insertOrIgnore([
            'cooperative_id' => $tenantId,
            'year' => $year,
            'next_number' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('member_number_sequences')
            ->where('cooperative_id', $tenantId)
            ->where('year', $year)
            ->lockForUpdate()
            ->first();

        throw_if(! $sequence, \RuntimeException::class, 'Unable to allocate a membership number.');

        DB::table('member_number_sequences')
            ->where('cooperative_id', $tenantId)
            ->where('year', $year)
            ->update(['next_number' => $sequence->next_number + 1, 'updated_at' => now()]);

        $setting = CooperativeSetting::query()->where('group', 'membership')->where('key', 'number_format')->first()?->value;
        $prefix = strtoupper((string) ($setting['prefix'] ?? 'MBR'));
        $prefix = preg_replace('/[^A-Z0-9]/', '', $prefix) ?: 'MBR';
        $padding = min(max((int) ($setting['padding'] ?? 6), 4), 10);

        return sprintf('%s-%d-%0'.$padding.'d', $prefix, $year, $sequence->next_number);
    }
}
