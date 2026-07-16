<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(): AnonymousResourceCollection { return RoleResource::collection(Role::query()->with('permissions')->orderBy('name')->get()); }
    public function store(StoreRoleRequest $request): RoleResource { return $this->persist(new Role, $request->validated()); }
    public function update(StoreRoleRequest $request, Role $role): RoleResource { abort_if($role->is_system, 422, 'System roles cannot be modified.'); return $this->persist($role, $request->validated()); }

    private function persist(Role $role, array $data): RoleResource
    {
        return DB::transaction(function () use ($role, $data): RoleResource {
            $permissions = $data['permissions']; unset($data['permissions']);
            $role->fill($data)->save();
            $role->permissions()->sync(Permission::query()->whereIn('name', $permissions)->pluck('id'));
            return new RoleResource($role->load('permissions'));
        });
    }
}

