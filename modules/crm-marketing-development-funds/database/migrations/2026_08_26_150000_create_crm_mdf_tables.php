<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_mdf_funds', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->decimal('budget', 14, 2);
            $t->decimal('committed', 14, 2)->default(0);
            $t->decimal('spent', 14, 2)->default(0);
            $t->string('currency', 3)->default('USD');
            $t->date('starts_on');
            $t->date('ends_on')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_mdf_requests', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('fund_id')->constrained('crm_mdf_funds')->cascadeOnDelete();
            $t->unsignedBigInteger('partner_id')->nullable();
            $t->string('title');
            $t->string('status')->default('draft');
            $t->decimal('amount', 14, 2);
            $t->decimal('reimbursed', 14, 2)->default(0);
            $t->decimal('attributed_revenue', 14, 2)->default(0);
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'partner_id']);
        });
        Schema::create('crm_mdf_request_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('request_id')->constrained('crm_mdf_requests')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->decimal('amount', 14, 2)->nullable();
            $t->text('evidence')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'request_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_mdf_request_events');
        Schema::dropIfExists('crm_mdf_requests');
        Schema::dropIfExists('crm_mdf_funds');
    }
};
