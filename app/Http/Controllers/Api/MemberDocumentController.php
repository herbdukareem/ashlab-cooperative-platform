<?php

namespace App\Http\Controllers\Api;

use App\Domain\Members\Actions\SaveMemberDocument;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMemberDocumentRequest;
use App\Http\Requests\VerifyRecordRequest;
use App\Http\Resources\MemberDocumentResource;
use App\Models\Member;
use App\Models\MemberDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberDocumentController extends Controller
{
    public function store(UploadMemberDocumentRequest $request, Member $member, SaveMemberDocument $save): MemberDocumentResource
    {
        return new MemberDocumentResource($save->execute($member, $request->validated('type'), $request->file('file')));
    }

    public function download(Member $member, MemberDocument $document): mixed
    {
        $this->owns($member, $document); abort_unless(Storage::disk($document->disk)->exists($document->path), 404, 'Document file not found.');
        return Storage::disk($document->disk)->download($document->path, $document->original_name);
    }

    public function verify(VerifyRecordRequest $request, Member $member, MemberDocument $document): MemberDocumentResource
    {
        $this->owns($member, $document); $data = $request->validated(); $status = VerificationStatus::from($data['status']);
        $document->update(['verification_status' => $status, 'verified_by' => Auth::id(), 'verified_at' => $status === VerificationStatus::Verified ? now() : null, 'rejection_reason' => $data['reason'] ?? null]);
        return new MemberDocumentResource($document->refresh());
    }

    public function destroy(Member $member, MemberDocument $document): mixed
    {
        $this->owns($member, $document); abort_if($document->verification_status === VerificationStatus::Verified, 422, 'A verified document cannot be deleted.');
        Storage::disk($document->disk)->delete($document->path); $document->delete(); return response()->noContent();
    }

    private function owns(Member $member, MemberDocument $record): void { abort_unless($record->member_id === $member->id, 404); }
}

