<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContributionObligation;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\Member;
use App\Models\Payout;
use App\Models\SavingsAccount;
use App\Models\SavingsWithdrawalRequest;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'members' => [
                'total' => Member::query()->count(),
                'active' => Member::query()->where('status', 'active')->count(),
                'pending' => Member::query()->where('status', 'pending')->count(),
                'kyc_pending' => Member::query()->whereNot('kyc_status', 'verified')->count(),
            ],
            'money' => [
                'savings_balance_minor' => (int) SavingsAccount::query()->sum('balance_minor'),
                'loan_portfolio_minor' => (int) Loan::query()->whereIn('status', ['active', 'in_arrears'])->sum('outstanding_minor'),
                'contribution_arrears_minor' => (int) ContributionObligation::query()
                    ->whereIn('status', ['overdue', 'partial'])
                    ->selectRaw('COALESCE(SUM(amount_due_minor - amount_paid_minor), 0) as aggregate')->value('aggregate'),
                'pending_payouts_minor' => (int) Payout::query()->whereIn('status', ['pending_approval', 'approved', 'processing'])->sum('amount_minor'),
            ],
            'work_queue' => [
                'loan_applications' => LoanApplication::query()->whereIn('status', ['submitted', 'under_review'])->count(),
                'withdrawals' => SavingsWithdrawalRequest::query()->where('status', 'pending')->count(),
                'payouts' => Payout::query()->whereIn('status', ['pending_approval', 'approved'])->count(),
            ],
            'portfolio' => Loan::query()
                ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(outstanding_minor), 0) as amount_minor')
                ->groupBy('status')->orderBy('status')->get(),
            'recent_members' => Member::query()->latest()->limit(5)
                ->get(['id', 'membership_number', 'first_name', 'last_name', 'status', 'created_at']),
        ]);
    }
}
