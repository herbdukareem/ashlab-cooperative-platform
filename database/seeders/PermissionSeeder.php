<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'platform' => ['platform.cooperatives.manage'],
            'administration' => ['branches.manage', 'users.manage', 'roles.manage', 'settings.view', 'settings.manage', 'audit.view'],
            'members' => ['members.view', 'members.create', 'members.update', 'members.approve', 'members.categories.manage', 'members.beneficiaries.manage', 'members.guarantors.manage'],
            'kyc' => ['kyc.view', 'kyc.manage', 'kyc.verify'],
            'contributions' => ['contributions.view', 'contributions.configure', 'contributions.enroll', 'contributions.generate', 'contributions.collect'],
            'savings' => ['savings.view', 'savings.configure', 'savings.accounts.manage', 'savings.deposit', 'savings.withdraw.request', 'savings.withdraw.approve', 'savings.withdraw.complete'],
            'payments' => ['payments.view'],
            'loans' => ['loans.view', 'loans.create', 'loans.review', 'loans.approve', 'loans.disburse'],
            'charges' => ['charges.view', 'charges.configure', 'charges.waive'],
            'repayments' => ['repayments.view', 'repayments.collect', 'repayments.reverse'],
            'payouts' => ['payouts.view', 'payouts.create', 'payouts.review', 'payouts.approve', 'payouts.release', 'payouts.reverse', 'payouts.reconcile'],
            'accounting' => ['accounting.view', 'accounting.post', 'accounting.reverse', 'accounting.reconcile'],
            'reports' => ['reports.view', 'reports.export'],
        ];

        foreach ($groups as $group => $permissions) {
            foreach ($permissions as $name) {
                Permission::query()->firstOrCreate(['name' => $name], ['group' => $group, 'description' => str($name)->replace('.', ' ')->headline()]);
            }
        }

        $cooperativePermissionIds = Permission::query()->where('name', 'not like', 'platform.%')->pluck('id');
        Role::withoutGlobalScopes()
            ->where('slug', 'cooperative-administrator')
            ->where('is_system', true)
            ->each(fn (Role $role) => $role->permissions()->syncWithoutDetaching($cooperativePermissionIds));
    }
}
