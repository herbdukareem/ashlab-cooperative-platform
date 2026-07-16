<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberCategoryRequest;
use App\Http\Resources\MemberCategoryResource;
use App\Models\MemberCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberCategoryController extends Controller
{
    public function index(): AnonymousResourceCollection { return MemberCategoryResource::collection(MemberCategory::query()->withCount('members')->orderBy('name')->get()); }
    public function store(StoreMemberCategoryRequest $request): MemberCategoryResource { return new MemberCategoryResource(MemberCategory::query()->create($request->validated())); }
    public function show(MemberCategory $memberCategory): MemberCategoryResource { return new MemberCategoryResource($memberCategory->loadCount('members')); }
    public function update(StoreMemberCategoryRequest $request, MemberCategory $memberCategory): MemberCategoryResource { $memberCategory->update($request->validated()); return new MemberCategoryResource($memberCategory->refresh()); }
    public function destroy(MemberCategory $memberCategory): mixed { abort_if($memberCategory->members()->exists(), 422, 'A category with members cannot be deleted.'); $memberCategory->delete(); return response()->noContent(); }
}

