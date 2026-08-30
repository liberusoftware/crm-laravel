<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_copilot_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('kind');
            $table->text('input');
            $table->json('context')->nullable();
            $table->json('result')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
        Schema::create('crm_copilot_actions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('request_id')->constrained('crm_copilot_requests');
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->json('payload');
            $table->string('status')->default('pending_confirmation');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_copilot_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->foreignId('request_id')->constrained('crm_copilot_requests');
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_copilot_audits');
        Schema::dropIfExists('crm_copilot_actions');
        Schema::dropIfExists('crm_copilot_requests');
    }
};
