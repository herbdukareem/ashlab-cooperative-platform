<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContributionPlanRequest;
use App\Models\ContributionPlan;
use Illuminate\Http\JsonResponse;
class ContributionPlanController extends Controller
{
    public function index(): JsonResponse { return response()->json(ContributionPlan::query()->withCount('enrollments')->orderBy('name')->paginate()); }
    public function store(StoreContributionPlanRequest $request): JsonResponse { return response()->json(ContributionPlan::query()->create($request->validated()), 201); }
    public function show(ContributionPlan $contributionPlan): JsonResponse { return response()->json($contributionPlan->loadCount(['enrollments','obligations'])); }
    public function update(StoreContributionPlanRequest $request, ContributionPlan $contributionPlan): JsonResponse { $contributionPlan->update($request->validated()); return response()->json($contributionPlan->refresh()); }
    public function destroy(ContributionPlan $contributionPlan): mixed { abort_if($contributionPlan->enrollments()->exists(), 422, 'An enrolled plan cannot be deleted.'); $contributionPlan->delete(); return response()->noContent(); }
}
