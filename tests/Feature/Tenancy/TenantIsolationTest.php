<?php

namespace Tests\Feature\Tenancy;

use App\Models\Branch;
use App\Models\Cooperative;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_select_another_cooperative_with_header(): void
    {
        $cooperativeA = Cooperative::factory()->create();
        $cooperativeB = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperativeA->id]);
        Sanctum::actingAs($user);

        $this->withHeader('X-Cooperative-ID', $cooperativeB->id)
            ->getJson('/api/v1/settings')
            ->assertForbidden();
    }

    public function test_branch_route_binding_cannot_cross_tenant_boundary(): void
    {
        $cooperativeA = Cooperative::factory()->create();
        $cooperativeB = Cooperative::factory()->create();
        $user = User::factory()->create(['cooperative_id' => $cooperativeA->id]);
        $permission = Permission::query()->create(['name' => 'branches.manage', 'group' => 'administration']);
        $role = Role::query()->create(['cooperative_id' => $cooperativeA->id, 'name' => 'Administrator', 'slug' => 'administrator']);
        $role->permissions()->attach($permission); $user->roles()->attach($role);
        $otherBranch = Branch::factory()->create(['cooperative_id' => $cooperativeB->id]);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/branches/'.$otherBranch->id)->assertNotFound();
    }
}

