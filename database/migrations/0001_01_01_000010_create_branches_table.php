<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('branches', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->ulid('manager_id')->nullable();
            $table->string('name');
            $table->string('code', 30);
            $table->string('type')->default('branch');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('local_government_area')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cooperative_id', 'code']);
            $table->index(['cooperative_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('branches'); }
};

