<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('savings_products', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 40);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('minimum_opening_balance_minor')->default(0);
            $table->unsignedBigInteger('minimum_balance_minor')->default(0);
            $table->unsignedBigInteger('minimum_withdrawal_minor')->default(0);
            $table->unsignedBigInteger('maximum_withdrawal_minor')->nullable();
            $table->unsignedSmallInteger('lock_in_days')->default(0);
            $table->unsignedInteger('interest_rate_basis_points')->default(0);
            $table->boolean('allow_multiple_accounts')->default(false);
            $table->boolean('allows_withdrawal')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('rules')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cooperative_id', 'code']);
            $table->index(['cooperative_id', 'is_active']);
        });

        Schema::create('savings_accounts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('savings_product_id')->constrained()->cascadeOnDelete();
            $table->string('account_number', 80);
            $table->string('name')->nullable();
            $table->unsignedBigInteger('balance_minor')->default(0);
            $table->unsignedBigInteger('available_balance_minor')->default(0);
            $table->unsignedBigInteger('goal_amount_minor')->nullable();
            $table->date('maturity_date')->nullable();
            $table->timestamp('opened_at');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['cooperative_id', 'account_number']);
            $table->index(['cooperative_id', 'member_id', 'status']);
            $table->index(['cooperative_id', 'savings_product_id']);
        });

        Schema::create('savings_transactions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('savings_account_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('related_transaction_id')->nullable()->constrained('savings_transactions')->nullOnDelete();
            $table->string('reference', 100);
            $table->string('type', 40);
            $table->unsignedBigInteger('amount_minor');
            $table->unsignedBigInteger('balance_after_minor');
            $table->timestamp('effective_at');
            $table->foreignUlid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['cooperative_id', 'reference']);
            $table->index(['cooperative_id', 'savings_account_id', 'effective_at'], 'savings_tx_account_effective_idx');
        });

        Schema::create('savings_withdrawal_requests', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('savings_account_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 100);
            $table->unsignedBigInteger('amount_minor');
            $table->unsignedBigInteger('fee_minor')->default(0);
            $table->unsignedBigInteger('total_debit_minor');
            $table->string('status')->default('pending');
            $table->text('reason')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamp('requested_at');
            $table->foreignUlid('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignUlid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->foreignUlid('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['cooperative_id', 'reference']);
            $table->index(['cooperative_id', 'member_id', 'status']);
            $table->index(['cooperative_id', 'status', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_withdrawal_requests');
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('savings_accounts');
        Schema::dropIfExists('savings_products');
    }
};
