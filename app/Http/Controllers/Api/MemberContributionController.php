<?php
namespace App\Http\Controllers\Api;
use App\Domain\Contributions\Actions\EnrollMemberInContributionPlan;
use App\Domain\Contributions\Actions\GenerateContributionObligations;
use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollContributionPlanRequest;
use App\Models\ContributionPlan;
use App\Models\Member;
use App\Models\MemberContributionPlan;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class MemberContributionController extends Controller
{
    public function index(Member $member): JsonResponse { return response()->json(['enrollments' => $member->contributionPlans()->with('plan')->get(), 'obligations' => $member->contributionObligations()->with('plan')->orderBy('due_date')->paginate()]); }
    public function store(EnrollContributionPlanRequest $request, Member $member, EnrollMemberInContributionPlan $action): JsonResponse { $plan = ContributionPlan::query()->findOrFail($request->validated('contribution_plan_id')); return response()->json($action->execute($member, $plan, $request->validated()), 201); }
    public function generate(Request $request, MemberContributionPlan $enrollment, GenerateContributionObligations $action): JsonResponse { $data = $request->validate(['through' => ['required','date']]); return response()->json(['created' => $action->execute($enrollment, CarbonImmutable::parse($data['through']))]); }
}
