<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_marketing_agent_requests', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('actor_id');
            $t->string('kind');
            $t->string('status')->default('draft');
            $t->text('brief');
            $t->json('audience')->nullable();
            $t->json('result')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_marketing_agent_checks', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('request_id')->constrained('crm_marketing_agent_requests')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status');
            $t->text('details')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'request_id', 'kind']);
        });
        Schema::create('crm_marketing_agent_experiments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('request_id')->constrained('crm_marketing_agent_requests')->cascadeOnDelete();
            $t->string('name');
            $t->string('status')->default('draft');
            $t->json('variants');
            $t->json('metrics')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_marketing_agent_experiments');
        Schema::dropIfExists('crm_marketing_agent_checks');
        Schema::dropIfExists('crm_marketing_agent_requests');
    }
};
