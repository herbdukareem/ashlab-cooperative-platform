<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignUlid('member_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->unique(['cooperative_id', 'member_id']);
        });

        Schema::create('notification_templates', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->string('event_key', 100);
            $table->string('channel', 20);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->json('variables')->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'event_key', 'channel']);
        });

        Schema::create('notification_preferences', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('in_app_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->string('quiet_hours_start', 5)->nullable();
            $table->string('quiet_hours_end', 5)->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'user_id']);
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('event_key', 100);
            $table->string('channel', 20)->default('in_app');
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->json('data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->string('deduplication_key')->nullable();
            $table->timestamps();
            $table->unique(['cooperative_id', 'deduplication_key']);
            $table->index(['user_id', 'read_at', 'created_at']);
            $table->index(['status', 'scheduled_at']);
        });

        Schema::create('push_subscriptions', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('cooperative_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->text('public_key');
            $table->text('auth_token');
            $table->string('device_name')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'endpoint'], 'push_user_endpoint_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notification_templates');
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('member_id'));
    }
};
