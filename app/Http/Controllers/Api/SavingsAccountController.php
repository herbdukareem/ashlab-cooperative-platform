<?php
namespace App\Http\Controllers\Api;
use App\Domain\Savings\Actions\OpenSavingsAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\OpenSavingsAccountRequest;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class SavingsAccountController extends Controller
{
    public function index(Member $member): JsonResponse { return response()->json($member->savingsAccounts()->with('product')->get()); }
    public function store(OpenSavingsAccountRequest $request, Member $member, OpenSavingsAccount $action): JsonResponse { $product = SavingsProduct::query()->findOrFail($request->validated('savings_product_id')); return response()->json($action->execute($member, $product, $request->validated()), 201); }
    public function show(SavingsAccount $savingsAccount): JsonResponse { return response()->json($savingsAccount->load(['member','product'])); }
    public function statement(Request $request, SavingsAccount $savingsAccount): JsonResponse { $data = $request->validate(['from' => ['nullable','date'], 'to' => ['nullable','date','after_or_equal:from']]); $transactions = $savingsAccount->transactions()->when($data['from'] ?? null, fn ($q, $from) => $q->where('effective_at','>=',$from))->when($data['to'] ?? null, fn ($q, $to) => $q->where('effective_at','<=',$to))->latest('effective_at')->paginate(); return response()->json(['account' => $savingsAccount->load('product'), 'transactions' => $transactions]); }
}
