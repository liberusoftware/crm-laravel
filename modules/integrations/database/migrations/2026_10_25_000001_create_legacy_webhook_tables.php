<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('url');
            $table->json('events');
            $table->text('secret')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_webhook_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->boolean('success')->default(false);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_webhook_deliveries');
        Schema::dropIfExists('webhooks');
    }
};
