<?php

namespace Tests\Feature\Experience;

use App\Models\Cooperative;
use App\Models\Member;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_tenant_scoped(): void
    {
        $a = Cooperative::factory()->create();
        $b = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $a->id]);
        $permission = Permission::query()->create(['name' => 'dashboard.view', 'group' => 'experience']);
        $role = Role::query()->create(['cooperative_id' => $a->id, 'name' => 'Manager', 'slug' => 'manager']);
        $role->permissions()->attach($permission);
        $user->roles()->attach($role);
        Member::factory()->count(2)->create(['cooperative_id' => $a->id]);
        Member::factory()->count(3)->create(['cooperative_id' => $b->id]);

        $this->actingAs($user)->withHeader('X-Cooperative-ID', $a->id)
            ->getJson('/api/v1/dashboard')
            ->assertOk()->assertJsonPath('members.total', 2);
    }
}
