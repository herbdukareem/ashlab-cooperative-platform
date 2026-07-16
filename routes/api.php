<?php

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\MemberBankAccountController;
use App\Http\Controllers\Api\MemberBeneficiaryController;
use App\Http\Controllers\Api\MemberCategoryController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MemberDocumentController;
use App\Http\Controllers\Api\MemberGuarantorController;
use App\Http\Controllers\Api\MemberIdentificationController;
use App\Http\Controllers\Api\Platform\CooperativeController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ContributionPlanController;
use App\Http\Controllers\Api\MemberContributionController;
use App\Http\Controllers\Api\SavingsAccountController;
use App\Http\Controllers\Api\SavingsProductController;
use App\Http\Controllers\Api\SavingsWithdrawalController;
use App\Http\Controllers\Api\ApprovalWorkflowController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\Api\LoanProductController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\PayoutController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::prefix('platform')->middleware('permission:platform.cooperatives.manage')->group(function (): void {
        Route::apiResource('cooperatives', CooperativeController::class)->only(['index', 'store', 'show']);
    });

    Route::middleware('tenant')->group(function (): void {
        Route::apiResource('branches', BranchController::class)->middleware('permission:branches.manage');
        Route::apiResource('roles', RoleController::class)->only(['index', 'store', 'update'])->middleware('permission:roles.manage');
        Route::get('permissions', PermissionController::class)->middleware('permission:roles.manage');
        Route::apiResource('users', UserController::class)->only(['index', 'store'])->middleware('permission:users.manage');
        Route::get('settings', [SettingController::class, 'index'])->middleware('permission:settings.view');
        Route::put('settings', [SettingController::class, 'update'])->middleware('permission:settings.manage');
        Route::get('audit-logs', AuditLogController::class)->middleware('permission:audit.view');

        Route::apiResource('member-categories', MemberCategoryController::class)->middleware('permission:members.categories.manage');
        Route::get('members', [MemberController::class, 'index'])->middleware('permission:members.view');
        Route::post('members', [MemberController::class, 'store'])->middleware('permission:members.create');
        Route::get('members/{member}', [MemberController::class, 'show'])->middleware('permission:members.view');
        Route::put('members/{member}', [MemberController::class, 'update'])->middleware('permission:members.update');
        Route::patch('members/{member}/status', [MemberController::class, 'changeStatus'])->middleware('permission:members.approve');

        Route::post('members/{member}/identifications', [MemberIdentificationController::class, 'store'])->middleware('permission:kyc.manage');
        Route::patch('members/{member}/identifications/{identification}/verify', [MemberIdentificationController::class, 'verify'])->middleware('permission:kyc.verify');
        Route::delete('members/{member}/identifications/{identification}', [MemberIdentificationController::class, 'destroy'])->middleware('permission:kyc.manage');

        Route::post('members/{member}/documents', [MemberDocumentController::class, 'store'])->middleware('permission:kyc.manage');
        Route::get('members/{member}/documents/{document}/download', [MemberDocumentController::class, 'download'])->middleware('permission:kyc.view');
        Route::patch('members/{member}/documents/{document}/verify', [MemberDocumentController::class, 'verify'])->middleware('permission:kyc.verify');
        Route::delete('members/{member}/documents/{document}', [MemberDocumentController::class, 'destroy'])->middleware('permission:kyc.manage');

        Route::post('members/{member}/bank-accounts', [MemberBankAccountController::class, 'store'])->middleware('permission:kyc.manage');
        Route::patch('members/{member}/bank-accounts/{bankAccount}/verify', [MemberBankAccountController::class, 'verify'])->middleware('permission:kyc.verify');
        Route::patch('members/{member}/bank-accounts/{bankAccount}/primary', [MemberBankAccountController::class, 'makePrimary'])->middleware('permission:members.update');
        Route::delete('members/{member}/bank-accounts/{bankAccount}', [MemberBankAccountController::class, 'destroy'])->middleware('permission:kyc.manage');

        Route::get('members/{member}/beneficiaries', [MemberBeneficiaryController::class, 'index'])->middleware('permission:members.view');
        Route::post('members/{member}/beneficiaries', [MemberBeneficiaryController::class, 'store'])->middleware('permission:members.beneficiaries.manage');
        Route::put('members/{member}/beneficiaries/{beneficiary}', [MemberBeneficiaryController::class, 'update'])->middleware('permission:members.beneficiaries.manage');
        Route::delete('members/{member}/beneficiaries/{beneficiary}', [MemberBeneficiaryController::class, 'destroy'])->middleware('permission:members.beneficiaries.manage');

        Route::get('members/{member}/guarantors', [MemberGuarantorController::class, 'index'])->middleware('permission:members.view');
        Route::post('members/{member}/guarantors', [MemberGuarantorController::class, 'store'])->middleware('permission:members.guarantors.manage');
        Route::put('members/{member}/guarantors/{guarantor}', [MemberGuarantorController::class, 'update'])->middleware('permission:members.guarantors.manage');
        Route::patch('members/{member}/guarantors/{guarantor}/consent', [MemberGuarantorController::class, 'consent'])->middleware('permission:members.approve');
        Route::delete('members/{member}/guarantors/{guarantor}', [MemberGuarantorController::class, 'destroy'])->middleware('permission:members.guarantors.manage');

        Route::apiResource('contribution-plans', ContributionPlanController::class)->middleware('permission:contributions.configure');
        Route::get('members/{member}/contributions', [MemberContributionController::class, 'index'])->middleware('permission:contributions.view');
        Route::post('members/{member}/contribution-enrollments', [MemberContributionController::class, 'store'])->middleware('permission:contributions.enroll');
        Route::post('contribution-enrollments/{enrollment}/generate', [MemberContributionController::class, 'generate'])->middleware('permission:contributions.generate');

        Route::get('payments', [CollectionController::class, 'index'])->middleware('permission:payments.view');
        Route::get('payments/{payment}', [CollectionController::class, 'show'])->middleware('permission:payments.view');
        Route::post('members/{member}/collections', [CollectionController::class, 'store'])->middleware('permission:contributions.collect');

        Route::apiResource('savings-products', SavingsProductController::class)->middleware('permission:savings.configure');
        Route::get('members/{member}/savings-accounts', [SavingsAccountController::class, 'index'])->middleware('permission:savings.view');
        Route::post('members/{member}/savings-accounts', [SavingsAccountController::class, 'store'])->middleware('permission:savings.accounts.manage');
        Route::get('savings-accounts/{savingsAccount}', [SavingsAccountController::class, 'show'])->middleware('permission:savings.view');
        Route::get('savings-accounts/{savingsAccount}/statement', [SavingsAccountController::class, 'statement'])->middleware('permission:savings.view');
        Route::get('savings-withdrawals', [SavingsWithdrawalController::class, 'index'])->middleware('permission:savings.view');
        Route::post('members/{member}/savings-accounts/{savingsAccount}/withdrawals', [SavingsWithdrawalController::class, 'store'])->middleware('permission:savings.withdraw.request');
        Route::patch('savings-withdrawals/{withdrawal}/approve', [SavingsWithdrawalController::class, 'approve'])->middleware('permission:savings.withdraw.approve');
        Route::patch('savings-withdrawals/{withdrawal}/reject', [SavingsWithdrawalController::class, 'reject'])->middleware('permission:savings.withdraw.approve');
        Route::patch('savings-withdrawals/{withdrawal}/complete', [SavingsWithdrawalController::class, 'complete'])->middleware('permission:savings.withdraw.complete');

        Route::apiResource('charges', ChargeController::class)->middleware('permission:charges.configure');
        Route::apiResource('approval-workflows', ApprovalWorkflowController::class)->middleware('permission:workflows.configure');
        Route::apiResource('loan-products', LoanProductController::class)->middleware('permission:loans.configure');
        Route::post('loan-products/{loanProduct}/preview', [LoanProductController::class, 'preview'])->middleware('permission:loans.view');
        Route::post('loan-products/{loanProduct}/evaluate-eligibility', [LoanProductController::class, 'evaluate'])->middleware('permission:loans.review');
        Route::post('loan-products/{loanProduct}/evaluate-policy', [LoanProductController::class, 'policy'])->middleware('permission:loans.review');
        Route::post('loan-products/{loanProduct}/guarantor-capacity', [LoanProductController::class, 'guarantorCapacity'])->middleware('permission:loans.review');
        Route::get('loan-applications', [LoanApplicationController::class, 'index'])->middleware('permission:loans.view');
        Route::get('loan-applications/{loanApplication}', [LoanApplicationController::class, 'show'])->middleware('permission:loans.view');
        Route::post('members/{member}/loan-applications', [LoanApplicationController::class, 'store'])->middleware('permission:loans.create');
        Route::post('loan-applications/{loanApplication}/assess', [LoanApplicationController::class, 'assess'])->middleware('permission:loans.review');
        Route::post('loan-applications/{loanApplication}/guarantors', [LoanApplicationController::class, 'addGuarantor'])->middleware('permission:loans.review');
        Route::patch('loan-guarantors/{guarantor}/consent', [LoanApplicationController::class, 'consent'])->middleware('permission:loans.review');
        Route::patch('loan-applications/{loanApplication}/submit', [LoanApplicationController::class, 'submit'])->middleware('permission:loans.review');
        Route::patch('loan-applications/{loanApplication}/decision', [LoanApplicationController::class, 'decide'])->middleware('permission:loans.approve');
        Route::post('loan-applications/{loanApplication}/disburse', [LoanApplicationController::class, 'disburse'])->middleware('permission:loans.disburse');
        Route::get('payouts', [PayoutController::class, 'index'])->middleware('permission:payouts.view');
        Route::post('payouts', [PayoutController::class, 'store'])->middleware('permission:payouts.create');
        Route::post('payout-batches', [PayoutController::class, 'bulk'])->middleware('permission:payouts.create');
        Route::get('payouts/{payout}', [PayoutController::class, 'show'])->middleware('permission:payouts.view');
        Route::patch('payouts/{payout}/approve', [PayoutController::class, 'approve'])->middleware('permission:payouts.approve');
        Route::patch('payouts/{payout}/release', [PayoutController::class, 'release'])->middleware('permission:payouts.release');
        Route::post('payouts/{payout}/events', [PayoutController::class, 'event'])->middleware('permission:payouts.reconcile');
    });
});
