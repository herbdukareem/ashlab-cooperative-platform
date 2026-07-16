<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(TenantContext $context): AnonymousResourceCollection
    {
        return UserResource::collection(User::query()->where('cooperative_id', $context->id())->with('branch', 'roles')->orderBy('last_name')->paginate());
    }

    public function store(StoreUserRequest $request, TenantContext $context): UserResource
    {
        return DB::transaction(function () use ($request, $context): UserResource {
            $data = $request->validated(); $roleIds = $data['role_ids']; unset($data['role_ids']);
            $user = User::query()->create([...$data, 'email' => mb_strtolower($data['email']), 'cooperative_id' => $context->id(), 'status' => $data['status'] ?? UserStatus::Active]);
            $user->roles()->sync($roleIds);
            return new UserResource($user->load('branch', 'roles'));
        });
    }
}

