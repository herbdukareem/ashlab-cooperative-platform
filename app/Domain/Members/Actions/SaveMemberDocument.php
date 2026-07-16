<?php

namespace App\Domain\Members\Actions;

use App\Enums\VerificationStatus;
use App\Models\Member;
use App\Models\MemberDocument;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class SaveMemberDocument
{
    public function execute(Member $member, string $type, UploadedFile $file): MemberDocument
    {
        $disk = 'local';
        $name = Str::ulid().'.'.$file->guessExtension();
        $directory = "cooperatives/{$member->cooperative_id}/members/{$member->id}/documents";
        $checksum = hash_file('sha256', $file->getRealPath());
        $path = $file->storeAs($directory, $name, $disk);

        try {
            return MemberDocument::query()->create([
                'member_id' => $member->id,
                'type' => $type,
                'disk' => $disk,
                'path' => $path,
                'original_name' => basename($file->getClientOriginalName()),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size_bytes' => $file->getSize(),
                'checksum_sha256' => $checksum,
                'verification_status' => VerificationStatus::Pending,
            ]);
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($path);
            throw $exception;
        }
    }
}
