<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contribution_plans', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 40);
            $table->text('description')->nullable();
            $table->string('frequency', 30);
            $table->unsignedBigInteger('minimum_amount_minor')->default(0);
            $table->unsignedBigInteger('maximum_amount_minor')->nullable();
            $table->unsignedBigInteger('fixed_amount_minor')->nullable();
            $table->boolean('is_fixed_amount')->default(true);
            $table->boolean('is_mandatory')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('grace_period_days')->default(0);
            $table->json('eligible_member_category_ids')->nullable();
            $table->json('withdrawal_rules')->nullable();
            $table->json('penalty_rules')->nullable();
            $table->json('schedule_configuration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cooperative_id', 'code']);
            $table->index(['cooperative_id', 'is_active', 'frequency']);
        });

        Schema::create('member_contribution_plans', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('contribution_plan_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('contribution_amount_minor');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['member_id', 'contribution_plan_id']);
            $table->index(['cooperative_id', 'status', 'next_due_date']);
        });

        Schema::create('contribution_obligations', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('contribution_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('member_contribution_plan_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');
            $table->unsignedBigInteger('amount_due_minor');
            $table->unsignedBigInteger('amount_paid_minor')->default(0);
            $table->string('status')->default('upcoming');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(['member_contribution_plan_id', 'due_date']);
            $table->index(['cooperative_id', 'member_id', 'status', 'due_date'], 'contrib_obligation_member_status_due_idx');
            $table->index(['cooperative_id', 'contribution_plan_id', 'due_date'], 'contrib_obligation_plan_due_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_obligations');
        Schema::dropIfExists('member_contribution_plans');
        Schema::dropIfExists('contribution_plans');
    }
};
