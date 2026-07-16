<?php

namespace App\Actions;

use App\Enums\CooperativeStatus;
use App\Enums\UserStatus;
use App\Models\Branch;
use App\Models\Cooperative;
use App\Models\CooperativeSetting;
use App\Models\Permission;
use App\Models\MemberCategory;
use App\Models\Role;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OnboardCooperative
{
    public function __construct(private readonly TenantContext $context) {}

    public function execute(array $data): Cooperative
    {
        return DB::transaction(function () use ($data): Cooperative {
            $cooperative = Cooperative::query()->create([
                ...Arr::except($data, ['admin']),
                'status' => CooperativeStatus::Active,
            ]);

            $this->context->set($cooperative);

            try {
                $branch = Branch::query()->create([
                    'name' => 'Head Office',
                    'code' => 'HQ',
                    'type' => 'head_office',
                    'email' => $cooperative->email,
                    'phone' => $cooperative->phone,
                    'address' => $cooperative->address,
                    'state' => $cooperative->state,
                    'local_government_area' => $cooperative->local_government_area,
                ]);

                $admin = User::query()->create([
                    ...Arr::only($data['admin'], ['first_name', 'last_name', 'email', 'phone', 'password']),
                    'email' => mb_strtolower($data['admin']['email']),
                    'cooperative_id' => $cooperative->id,
                    'branch_id' => $branch->id,
                    'status' => UserStatus::Active,
                ]);

                $role = Role::query()->create([
                    'name' => 'Cooperative Administrator',
                    'slug' => 'cooperative-administrator',
                    'description' => 'Full administrative access within this cooperative.',
                    'is_system' => true,
                ]);
                $role->permissions()->sync(Permission::query()->where('name', 'not like', 'platform.%')->pluck('id'));
                $admin->roles()->attach($role);

                CooperativeSetting::query()->create(['group' => 'general', 'key' => 'currency', 'value' => ['code' => $cooperative->currency]]);
                CooperativeSetting::query()->create(['group' => 'general', 'key' => 'financial_year', 'value' => ['start_month' => $cooperative->financial_year_start_month]]);
                CooperativeSetting::query()->create(['group' => 'membership', 'key' => 'number_format', 'value' => ['prefix' => 'MBR', 'padding' => 6]]);
                MemberCategory::query()->create([
                    'name' => 'Regular Member', 'code' => 'REGULAR',
                    'description' => 'Default membership category.',
                    'registration_fee_minor' => 0, 'minimum_contribution_minor' => 0,
                    'requires_guarantor' => false, 'required_guarantors' => 0,
                    'requires_kyc' => true, 'is_active' => true,
                ]);
            } finally {
                $this->context->clear();
            }

            return $cooperative->load('branches', 'users.roles');
        });
    }
}
