<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cooperatives', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->date('registration_date')->nullable();
            $table->string('type')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->string('local_government_area')->nullable();
            $table->char('currency', 3)->default('NGN');
            $table->unsignedTinyInteger('financial_year_start_month')->default(1);
            $table->string('status')->default('pending')->index();
            $table->string('logo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('cooperatives'); }
};

