<?php

namespace Tests\Support;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

trait InteractsWithPermissions
{
    protected function actingAsCooperativeUser(User $user, array $permissions): void
    {
        $role = Role::query()->create([
            'cooperative_id' => $user->cooperative_id,
            'name' => 'Test Role '.fake()->unique()->numberBetween(1, 99999),
            'slug' => 'test-role-'.fake()->unique()->numberBetween(1, 99999),
        ]);

        $ids = collect($permissions)->map(function (string $name) {
            return Permission::query()->firstOrCreate(['name' => $name], ['group' => str($name)->before('.')->toString()])->id;
        });
        $role->permissions()->sync($ids);
        $user->roles()->syncWithoutDetaching([$role->id]);
        Sanctum::actingAs($user);
    }
}

