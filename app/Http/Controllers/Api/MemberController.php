<?php

namespace App\Http\Controllers\Api;

use App\Domain\Members\Actions\ChangeMemberStatus;
use App\Domain\Members\Actions\RegisterMember;
use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeMemberStatusRequest;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', 'string'],
            'kyc_status' => ['nullable', 'string'], 'branch_id' => ['nullable', 'string'],
            'member_category_id' => ['nullable', 'string'], 'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $members = Member::query()->with('branch', 'category')
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('membership_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($q, $value) => $q->where('status', $value))
            ->when($filters['kyc_status'] ?? null, fn ($q, $value) => $q->where('kyc_status', $value))
            ->when($filters['branch_id'] ?? null, fn ($q, $value) => $q->where('branch_id', $value))
            ->when($filters['member_category_id'] ?? null, fn ($q, $value) => $q->where('member_category_id', $value))
            ->orderBy('last_name')->orderBy('first_name')
            ->paginate($filters['per_page'] ?? config('platform.pagination.default'));

        return MemberResource::collection($members);
    }

    public function store(StoreMemberRequest $request, RegisterMember $register): MemberResource
    {
        return new MemberResource($register->execute($request->validated())->load('branch', 'category'));
    }

    public function show(Member $member): MemberResource
    {
        return new MemberResource($member->load(['branch', 'category', 'identifications', 'documents', 'bankAccounts', 'beneficiaries', 'guarantors.guarantorMember', 'statusHistory.actor']));
    }

    public function update(StoreMemberRequest $request, Member $member): MemberResource
    {
        $member->update($request->validated());
        return new MemberResource($member->refresh()->load('branch', 'category'));
    }

    public function changeStatus(ChangeMemberStatusRequest $request, Member $member, ChangeMemberStatus $change): MemberResource
    {
        $data = $request->validated();
        $member = $change->execute($member, MemberStatus::from($data['status']), $data['reason'] ?? null);
        return new MemberResource($member->load('category', 'statusHistory.actor'));
    }
}

