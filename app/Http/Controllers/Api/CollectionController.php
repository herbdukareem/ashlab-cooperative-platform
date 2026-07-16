<?php
namespace App\Http\Controllers\Api;
use App\Domain\Payments\Actions\RecordMemberCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\RecordCollectionRequest;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
class CollectionController extends Controller
{
    public function index(): JsonResponse { return response()->json(Payment::query()->with('member')->latest('received_at')->paginate()); }
    public function store(RecordCollectionRequest $request, Member $member, RecordMemberCollection $action): JsonResponse { return response()->json($action->execute($member, $request->validated()), 201); }
    public function show(Payment $payment): JsonResponse { return response()->json($payment->load(['member','allocations'])); }
}
