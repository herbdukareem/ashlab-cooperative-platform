<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_categories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 30);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('registration_fee_minor')->default(0);
            $table->unsignedBigInteger('minimum_contribution_minor')->default(0);
            $table->boolean('requires_guarantor')->default(false);
            $table->unsignedTinyInteger('required_guarantors')->default(0);
            $table->boolean('requires_kyc')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cooperative_id', 'code']);
            $table->index(['cooperative_id', 'is_active']);
        });

        Schema::create('member_number_sequences', function (Blueprint $table): void {
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('next_number')->default(1);
            $table->timestamps();
            $table->primary(['cooperative_id', 'year']);
        });

        Schema::create('members', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('member_category_id')->nullable()->constrained('member_categories')->nullOnDelete();
            $table->string('membership_number', 80);
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('residential_address')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('local_government_area')->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('staff_number', 80)->nullable();
            $table->string('department')->nullable();
            $table->date('date_joined');
            $table->string('status')->default('pending')->index();
            $table->string('kyc_status')->default('not_started')->index();
            $table->string('passport_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('status_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cooperative_id', 'membership_number']);
            $table->unique(['cooperative_id', 'email']);
            $table->unique(['cooperative_id', 'phone']);
            $table->index(['cooperative_id', 'branch_id', 'status']);
            $table->index(['cooperative_id', 'member_category_id']);
            $table->index(['cooperative_id', 'last_name', 'first_name']);
        });

        Schema::create('member_status_histories', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['cooperative_id', 'member_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_status_histories');
        Schema::dropIfExists('members');
        Schema::dropIfExists('member_number_sequences');
        Schema::dropIfExists('member_categories');
    }
};
