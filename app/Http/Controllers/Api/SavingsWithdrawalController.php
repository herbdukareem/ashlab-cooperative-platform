<?php
namespace App\Http\Controllers\Api;
use App\Domain\Savings\Actions\ProcessSavingsWithdrawal;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequestSavingsWithdrawalRequest;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsWithdrawalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class SavingsWithdrawalController extends Controller
{
    public function index(): JsonResponse { return response()->json(SavingsWithdrawalRequest::query()->with(['member','account'])->latest('requested_at')->paginate()); }
    public function store(RequestSavingsWithdrawalRequest $request, Member $member, SavingsAccount $savingsAccount, ProcessSavingsWithdrawal $action): JsonResponse { return response()->json($action->request($member, $savingsAccount, $request->validated()), 201); }
    public function approve(Request $request, SavingsWithdrawalRequest $withdrawal, ProcessSavingsWithdrawal $action): JsonResponse { $data = $request->validate(['reason' => ['nullable','string','max:2000']]); return response()->json($action->approve($withdrawal, $data['reason'] ?? null)); }
    public function reject(Request $request, SavingsWithdrawalRequest $withdrawal, ProcessSavingsWithdrawal $action): JsonResponse { $data = $request->validate(['reason' => ['required','string','max:2000']]); return response()->json($action->reject($withdrawal, $data['reason'])); }
    public function complete(SavingsWithdrawalRequest $withdrawal, ProcessSavingsWithdrawal $action): JsonResponse { return response()->json($action->complete($withdrawal)); }
}
