<?php

namespace App\Http\Controllers\Api;

use App\Enums\ConsentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuarantorRequest;
use App\Http\Resources\MemberGuarantorResource;
use App\Models\Member;
use App\Models\MemberGuarantor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MemberGuarantorController extends Controller
{
    public function index(Member $member): AnonymousResourceCollection { return MemberGuarantorResource::collection($member->guarantors()->with('guarantorMember')->latest()->get()); }

    public function store(StoreGuarantorRequest $request, Member $member): MemberGuarantorResource
    {
        $data = $request->validated();
        if (($data['guarantor_member_id'] ?? null) === $member->id) throw ValidationException::withMessages(['guarantor_member_id' => ['A member cannot guarantee themself.']]);
        return new MemberGuarantorResource(MemberGuarantor::query()->create([...$data, 'member_id' => $member->id, 'consent_status' => ConsentStatus::Pending]));
    }

    public function update(StoreGuarantorRequest $request, Member $member, MemberGuarantor $guarantor): MemberGuarantorResource
    {
        $this->owns($member, $guarantor); abort_if($guarantor->consent_status === ConsentStatus::Accepted, 422, 'An accepted guarantor must revoke consent before their details can change.');
        $data = $request->validated(); if (($data['guarantor_member_id'] ?? null) === $member->id) throw ValidationException::withMessages(['guarantor_member_id' => ['A member cannot guarantee themself.']]);
        $guarantor->update($data); return new MemberGuarantorResource($guarantor->refresh());
    }

    public function consent(Request $request, Member $member, MemberGuarantor $guarantor): MemberGuarantorResource
    {
        $this->owns($member, $guarantor);
        $data = $request->validate(['status' => ['required', Rule::enum(ConsentStatus::class)]]);
        $status = ConsentStatus::from($data['status']);
        $guarantor->update(['consent_status' => $status, 'consented_at' => $status === ConsentStatus::Accepted ? now() : null]);
        return new MemberGuarantorResource($guarantor->refresh());
    }

    public function destroy(Member $member, MemberGuarantor $guarantor): mixed
    {
        $this->owns($member, $guarantor); abort_if($guarantor->guaranteed_amount_minor > 0, 422, 'A guarantor with active exposure cannot be removed.');
        $guarantor->delete(); return response()->noContent();
    }

    private function owns(Member $member, MemberGuarantor $record): void { abort_unless($record->member_id === $member->id, 404); }
}

