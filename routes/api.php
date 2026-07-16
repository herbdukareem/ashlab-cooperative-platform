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
    });
});
