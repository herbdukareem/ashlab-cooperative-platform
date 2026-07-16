<?php

namespace App\Http\Controllers\Api;

use App\Domain\Members\Actions\SaveBeneficiary;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Resources\MemberBeneficiaryResource;
use App\Models\Member;
use App\Models\MemberBeneficiary;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberBeneficiaryController extends Controller
{
    public function index(Member $member): AnonymousResourceCollection { return MemberBeneficiaryResource::collection($member->beneficiaries()->orderBy('full_name')->get()); }
    public function store(StoreBeneficiaryRequest $request, Member $member, SaveBeneficiary $save): MemberBeneficiaryResource { return new MemberBeneficiaryResource($save->execute($member, $request->validated())); }
    public function update(StoreBeneficiaryRequest $request, Member $member, MemberBeneficiary $beneficiary, SaveBeneficiary $save): MemberBeneficiaryResource { $this->owns($member, $beneficiary); return new MemberBeneficiaryResource($save->execute($member, $request->validated(), $beneficiary)); }
    public function destroy(Member $member, MemberBeneficiary $beneficiary): mixed { $this->owns($member, $beneficiary); $beneficiary->delete(); return response()->noContent(); }
    private function owns(Member $member, MemberBeneficiary $record): void { abort_unless($record->member_id === $member->id, 404); }
}

