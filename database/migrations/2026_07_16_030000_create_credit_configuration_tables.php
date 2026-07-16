<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('loan_products', function (Blueprint $table): void {
            $table->ulid('id')->primary(); $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('code', 40); $table->text('description')->nullable();
            $table->unsignedBigInteger('minimum_principal_minor'); $table->unsignedBigInteger('maximum_principal_minor');
            $table->unsignedSmallInteger('minimum_tenure'); $table->unsignedSmallInteger('maximum_tenure');
            $table->string('interest_method', 30); $table->unsignedInteger('annual_interest_rate_basis_points')->default(0); $table->string('repayment_frequency', 20);
            $table->unsignedSmallInteger('grace_period_days')->default(0); $table->unsignedSmallInteger('moratorium_periods')->default(0);
            $table->boolean('requires_guarantors')->default(false); $table->unsignedTinyInteger('minimum_guarantors')->default(0); $table->unsignedTinyInteger('maximum_guarantors')->default(0);
            $table->unsignedInteger('maximum_debt_to_income_basis_points')->nullable(); $table->unsignedInteger('contribution_limit_multiplier_basis_points')->nullable(); $table->unsignedInteger('savings_limit_multiplier_basis_points')->nullable();
            $table->unsignedSmallInteger('minimum_membership_months')->default(0); $table->unsignedInteger('guarantor_maximum_exposure_basis_points')->default(10000); $table->unsignedTinyInteger('guarantor_maximum_active_loans')->default(3);
            $table->boolean('allows_early_settlement')->default(true); $table->boolean('is_active')->default(true); $table->json('configuration')->nullable(); $table->timestamps(); $table->softDeletes();
            $table->unique(['cooperative_id','code']); $table->index(['cooperative_id','is_active']);
        });
        Schema::create('charges', function (Blueprint $table): void {
            $table->ulid('id')->primary(); $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('code',40); $table->text('description')->nullable(); $table->string('calculation_type',20);
            $table->unsignedBigInteger('fixed_amount_minor')->nullable(); $table->unsignedInteger('rate_basis_points')->nullable(); $table->string('calculation_basis',30)->default('principal');
            $table->unsignedBigInteger('minimum_amount_minor')->nullable(); $table->unsignedBigInteger('maximum_amount_minor')->nullable();
            $table->string('application_timing',30); $table->string('treatment',30); $table->boolean('is_refundable')->default(false); $table->json('exempt_member_category_ids')->nullable(); $table->json('configuration')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); $table->softDeletes();
            $table->unique(['cooperative_id','code']);
        });
        Schema::create('loan_product_charges', function (Blueprint $table): void {
            $table->foreignUlid('loan_product_id')->constrained()->cascadeOnDelete(); $table->foreignUlid('charge_id')->constrained()->cascadeOnDelete(); $table->unsignedSmallInteger('sequence')->default(0); $table->boolean('is_mandatory')->default(true); $table->json('overrides')->nullable(); $table->timestamps(); $table->primary(['loan_product_id','charge_id']);
        });
        Schema::create('loan_product_eligibility_rules', function (Blueprint $table): void {
            $table->ulid('id')->primary(); $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete(); $table->foreignUlid('loan_product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->string('field',60); $table->string('operator',20); $table->json('comparison_value'); $table->string('failure_message'); $table->boolean('is_hard_rule')->default(true); $table->unsignedSmallInteger('sequence')->default(0); $table->boolean('is_active')->default(true); $table->timestamps();
            $table->index(['cooperative_id','loan_product_id','is_active'], 'loan_eligibility_product_active_idx');
        });
        Schema::create('approval_workflows', function (Blueprint $table): void {
            $table->ulid('id')->primary(); $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete(); $table->string('name'); $table->string('code',40); $table->string('entity_type',40)->default('loan_application'); $table->text('description')->nullable(); $table->boolean('is_active')->default(true); $table->timestamps(); $table->softDeletes(); $table->unique(['cooperative_id','code']);
        });
        Schema::create('approval_workflow_steps', function (Blueprint $table): void {
            $table->ulid('id')->primary(); $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete(); $table->foreignUlid('approval_workflow_id')->constrained()->cascadeOnDelete(); $table->unsignedSmallInteger('sequence'); $table->string('name'); $table->string('required_permission',100); $table->unsignedTinyInteger('minimum_approvals')->default(1); $table->unsignedBigInteger('minimum_amount_minor')->nullable(); $table->unsignedBigInteger('maximum_amount_minor')->nullable(); $table->boolean('requires_distinct_actor')->default(true); $table->json('configuration')->nullable(); $table->timestamps(); $table->unique(['approval_workflow_id','sequence']);
        });
        Schema::table('loan_products', fn (Blueprint $table) => $table->foreignUlid('approval_workflow_id')->nullable()->after('code')->constrained()->nullOnDelete());
    }
    public function down(): void { Schema::table('loan_products', fn (Blueprint $table) => $table->dropConstrainedForeignId('approval_workflow_id')); Schema::dropIfExists('approval_workflow_steps'); Schema::dropIfExists('approval_workflows'); Schema::dropIfExists('loan_product_eligibility_rules'); Schema::dropIfExists('loan_product_charges'); Schema::dropIfExists('charges'); Schema::dropIfExists('loan_products'); }
};
