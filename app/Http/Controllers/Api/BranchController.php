<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BranchController extends Controller
{
    public function index(): AnonymousResourceCollection { return BranchResource::collection(Branch::query()->orderBy('name')->paginate()); }
    public function store(StoreBranchRequest $request): BranchResource { return new BranchResource(Branch::query()->create($request->validated())); }
    public function show(Branch $branch): BranchResource { return new BranchResource($branch); }
    public function update(StoreBranchRequest $request, Branch $branch): BranchResource { $branch->update($request->validated()); return new BranchResource($branch->refresh()); }
    public function destroy(Branch $branch): mixed { abort_if($branch->type === 'head_office', 422, 'The head office cannot be deleted.'); $branch->delete(); return response()->noContent(); }
}

