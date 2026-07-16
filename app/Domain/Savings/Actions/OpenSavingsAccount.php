<?php

namespace App\Domain\Savings\Actions;

use App\Enums\SavingsAccountStatus;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsProduct;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OpenSavingsAccount
{
    public function execute(Member $member, SavingsProduct $product, array $data): SavingsAccount
    {
        abort_unless($member->cooperative_id === $product->cooperative_id, 404);
        if (! $product->is_active) throw ValidationException::withMessages(['savings_product_id' => ['This savings product is inactive.']]);
        if (! $product->allow_multiple_accounts && SavingsAccount::query()->where('member_id', $member->id)->where('savings_product_id', $product->id)->exists()) {
            throw ValidationException::withMessages(['savings_product_id' => ['This member already has an account for the selected product.']]);
        }

        return SavingsAccount::query()->create([
            'member_id' => $member->id,
            'savings_product_id' => $product->id,
            'account_number' => 'SAV-'.strtoupper($product->code).'-'.strtoupper(Str::random(10)),
            'name' => $data['name'] ?? null,
            'goal_amount_minor' => $data['goal_amount_minor'] ?? null,
            'maturity_date' => $data['maturity_date'] ?? null,
            'opened_at' => now(),
            'status' => SavingsAccountStatus::Active,
        ]);
    }
}
