<?php

namespace Tests\Feature\Platform;

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CooperativeOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_onboard_a_cooperative_atomically(): void
    {
        $this->seed(PermissionSeeder::class);
        $admin = User::factory()->platformAdmin()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/platform/cooperatives', [
            'name' => 'Transformers Staff Cooperative Society',
            'slug' => 'transformers-staff-cooperative',
            'registration_number' => 'NIG/CS/00001',
            'currency' => 'NGN',
            'financial_year_start_month' => 1,
            'admin' => [
                'first_name' => 'Amina',
                'last_name' => 'Bello',
                'email' => 'amina@example.test',
                'password' => 'VerySecurePassword123!',
                'password_confirmation' => 'VerySecurePassword123!',
            ],
        ]);

        $response->assertCreated()->assertJsonPath('data.slug', 'transformers-staff-cooperative');
        $this->assertDatabaseHas('branches', ['code' => 'HQ', 'type' => 'head_office']);
        $this->assertDatabaseHas('roles', ['slug' => 'cooperative-administrator']);
        $this->assertDatabaseHas('users', ['email' => 'amina@example.test']);
        $this->assertGreaterThan(0, Permission::query()->count());
    }
}

