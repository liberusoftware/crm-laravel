<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_membership_plans', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->string('interval')->default('monthly');
            $t->decimal('price', 14, 2)->default(0);
            $t->string('currency', 3)->default('USD');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_membership_resources', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('key');
            $t->string('kind')->default('resource');
            $t->string('name');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'key']);
        });
        Schema::create('crm_membership_grants', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('plan_id')->constrained('crm_membership_plans')->cascadeOnDelete();
            $t->foreignId('resource_id')->constrained('crm_membership_resources')->cascadeOnDelete();
            $t->unsignedBigInteger('member_id');
            $t->string('status')->default('active');
            $t->timestamp('starts_at');
            $t->timestamp('ends_at')->nullable();
            $t->string('renewal_reference')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'member_id', 'status']);
        });
        Schema::create('crm_membership_bundles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('kind')->default('community');
            $t->json('plan_ids')->nullable();
            $t->json('resource_ids')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_membership_bundles');
        Schema::dropIfExists('crm_membership_grants');
        Schema::dropIfExists('crm_membership_resources');
        Schema::dropIfExists('crm_membership_plans');
    }
};
