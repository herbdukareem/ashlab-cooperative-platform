<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContributionObligation;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberPortalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $member = $this->member($request);

        return response()->json([
            'member' => $member->only(['id', 'membership_number', 'first_name', 'last_name', 'status', 'kyc_status', 'date_joined']),
            'savings' => $member->savingsAccounts()->with('product:id,name')->get()
                ->map->only(['id', 'account_number', 'name', 'balance_minor', 'available_balance_minor', 'status', 'product']),
            'loans' => $member->loans()->with('product:id,name')->latest()->get()
                ->map->only(['id', 'loan_number', 'principal_minor', 'outstanding_minor', 'status', 'maturity_date', 'product']),
            'contributions' => [
                'overdue_minor' => (int) ContributionObligation::query()->where('member_id', $member->id)
                    ->whereIn('status', ['overdue', 'partial'])
                    ->selectRaw('COALESCE(SUM(amount_due_minor - amount_paid_minor), 0) as aggregate')->value('aggregate'),
                'next' => ContributionObligation::query()->where('member_id', $member->id)
                    ->whereIn('status', ['upcoming', 'due', 'partial'])->orderBy('due_date')->first(),
            ],
            'recent_activity' => $member->payments()->latest('paid_at')->limit(5)
                ->get(['id', 'reference', 'amount_minor', 'channel', 'status', 'paid_at']),
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $member = $this->member($request)->load(['category:id,name', 'branch:id,name', 'bankAccounts', 'beneficiaries']);

        return response()->json($member);
    }

    public function loans(Request $request): JsonResponse
    {
        return response()->json($this->member($request)->loans()
            ->with(['product:id,name', 'installments'])->latest()->paginate(15));
    }

    public function statements(Request $request): JsonResponse
    {
        $member = $this->member($request);

        return response()->json([
            'savings_accounts' => $member->savingsAccounts()->with(['product:id,name', 'transactions' => fn ($query) => $query->latest('effective_at')->limit(50)])->get(),
            'contribution_obligations' => $member->contributionObligations()->latest('due_date')->limit(50)->get(),
            'loan_repayments' => Loan::query()->where('member_id', $member->id)->with(['repayments' => fn ($query) => $query->latest('paid_at')->limit(50)])->get(),
        ]);
    }

    private function member(Request $request)
    {
        abort_unless($request->user()->member_id, 403, 'This account is not linked to a member profile.');

        return $request->user()->member()->firstOrFail();
    }
}
