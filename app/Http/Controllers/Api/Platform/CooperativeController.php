<?php

namespace App\Http\Controllers\Api\Platform;

use App\Actions\OnboardCooperative;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCooperativeRequest;
use App\Http\Resources\CooperativeResource;
use App\Models\Cooperative;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CooperativeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CooperativeResource::collection(Cooperative::query()->latest()->paginate(config('platform.pagination.default')));
    }

    public function store(StoreCooperativeRequest $request, OnboardCooperative $onboard): CooperativeResource
    {
        return new CooperativeResource($onboard->execute($request->validated()));
    }

    public function show(Cooperative $cooperative): CooperativeResource
    {
        return new CooperativeResource($cooperative->loadCount('branches', 'users'));
    }
}

