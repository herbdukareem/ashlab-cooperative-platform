<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_identifications', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->text('identifier_encrypted');
            $table->char('identifier_hash', 64);
            $table->string('identifier_last_four', 4)->nullable();
            $table->string('country', 2)->default('NG');
            $table->string('verification_status')->default('pending');
            $table->foreignUlid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'type', 'identifier_hash']);
            $table->index(['cooperative_id', 'member_id', 'type']);
        });

        Schema::create('member_documents', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size_bytes');
            $table->char('checksum_sha256', 64);
            $table->string('verification_status')->default('pending');
            $table->foreignUlid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->index(['cooperative_id', 'member_id', 'type']);
            $table->index(['cooperative_id', 'checksum_sha256']);
        });

        Schema::create('member_bank_accounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->string('bank_code', 20);
            $table->string('bank_name');
            $table->text('account_number_encrypted');
            $table->char('account_number_hash', 64);
            $table->string('account_number_last_four', 4);
            $table->string('account_name');
            $table->boolean('is_primary')->default(false);
            $table->string('verification_status')->default('pending');
            $table->string('provider_reference')->nullable();
            $table->foreignUlid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'bank_code', 'account_number_hash']);
            $table->index(['cooperative_id', 'member_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_bank_accounts');
        Schema::dropIfExists('member_documents');
        Schema::dropIfExists('member_identifications');
    }
};
