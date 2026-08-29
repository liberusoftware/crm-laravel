<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_loyalty_programs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->string('currency', 3)->default('PTS');
            $t->json('tiers')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_loyalty_members', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('program_id')->constrained('crm_loyalty_programs')->cascadeOnDelete();
            $t->unsignedBigInteger('member_id');
            $t->string('tier')->default('standard');
            $t->decimal('balance', 14, 2)->default(0);
            $t->decimal('lifetime_earned', 14, 2)->default(0);
            $t->boolean('fraud_locked')->default(false);
            $t->timestamps();
            $t->unique(['team_id', 'program_id', 'member_id']);
        });
        Schema::create('crm_loyalty_ledger', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('member_id')->constrained('crm_loyalty_members')->cascadeOnDelete();
            $t->string('kind');
            $t->decimal('points', 14, 2);
            $t->string('reference')->nullable();
            $t->date('expires_on')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'member_id', 'kind']);
            $t->unique(['team_id', 'reference']);
        });
        Schema::create('crm_loyalty_rules_rewards', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('program_id')->constrained('crm_loyalty_programs')->cascadeOnDelete();
            $t->string('kind');
            $t->string('name');
            $t->decimal('points', 14, 2)->default(0);
            $t->json('conditions')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'program_id', 'name']);
        });
        Schema::create('crm_loyalty_controls', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('member_id')->constrained('crm_loyalty_members')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('open');
            $t->text('reason')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'member_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_loyalty_controls');
        Schema::dropIfExists('crm_loyalty_rules_rewards');
        Schema::dropIfExists('crm_loyalty_ledger');
        Schema::dropIfExists('crm_loyalty_members');
        Schema::dropIfExists('crm_loyalty_programs');
    }
};
