<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_beneficiaries', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('relationship', 80);
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('entitlement_percentage', 5, 2);
            $table->string('identification_type', 40)->nullable();
            $table->text('identification_encrypted')->nullable();
            $table->boolean('is_minor')->default(false);
            $table->timestamps();
            $table->index(['cooperative_id', 'member_id']);
        });

        Schema::create('member_guarantors', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('guarantor_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('external_name')->nullable();
            $table->string('relationship', 80);
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('employer')->nullable();
            $table->unsignedBigInteger('guarantee_limit_minor')->default(0);
            $table->unsignedBigInteger('guaranteed_amount_minor')->default(0);
            $table->string('consent_status')->default('pending');
            $table->timestamp('consented_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['cooperative_id', 'member_id', 'is_active']);
            $table->index(['cooperative_id', 'guarantor_member_id']);
            $table->unique(['cooperative_id', 'member_id', 'guarantor_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_guarantors');
        Schema::dropIfExists('member_beneficiaries');
    }
};
