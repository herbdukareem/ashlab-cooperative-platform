<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::query()->where('email', mb_strtolower($credentials['email']))->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        if ($user->status !== UserStatus::Active) {
            throw ValidationException::withMessages(['email' => ['This user account is not active.']]);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken($credentials['device_name'] ?? 'api-client');

        return response()->json([
            'message' => 'Login successful.',
            'token' => $token->plainTextToken,
            'user' => new UserResource($user->load('cooperative', 'branch', 'roles')),
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('cooperative', 'branch', 'roles.permissions'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logout successful.']);
    }
}

