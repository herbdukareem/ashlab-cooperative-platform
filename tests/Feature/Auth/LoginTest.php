<?php

namespace Tests\Feature\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create(['password' => 'Password123!']);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'Password123!',
            'device_name' => 'test-suite',
        ]);

        $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'email']]);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create(['password' => 'Password123!', 'status' => UserStatus::Suspended]);
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'Password123!'])
            ->assertUnprocessable();
    }
}

