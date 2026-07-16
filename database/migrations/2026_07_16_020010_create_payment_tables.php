<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 80);
            $table->string('idempotency_key', 100);
            $table->string('type', 40)->default('collection');
            $table->string('channel', 40);
            $table->char('currency', 3)->default('NGN');
            $table->unsignedBigInteger('amount_minor');
            $table->unsignedBigInteger('allocated_minor')->default(0);
            $table->unsignedBigInteger('unallocated_minor')->default(0);
            $table->string('status')->default('successful');
            $table->string('external_reference')->nullable();
            $table->timestamp('received_at');
            $table->foreignUlid('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'reference']);
            $table->unique(['cooperative_id', 'idempotency_key']);
            $table->index(['cooperative_id', 'member_id', 'received_at']);
            $table->index(['cooperative_id', 'status', 'channel']);
        });

        Schema::create('payment_allocations', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('payment_id')->constrained()->cascadeOnDelete();
            $table->string('allocation_type', 50);
            $table->ulid('allocation_id');
            $table->unsignedBigInteger('amount_minor');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('reversed_at')->nullable();
            $table->foreignUlid('reversed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reversal_reason')->nullable();
            $table->index(['cooperative_id', 'allocation_type', 'allocation_id'], 'payment_allocation_target_idx');
            $table->index(['payment_id', 'reversed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
    }
};
