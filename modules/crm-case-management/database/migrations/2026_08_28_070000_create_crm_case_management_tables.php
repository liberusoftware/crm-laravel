<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_cases', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->string('case_key');
            $t->string('type')->default('support');
            $t->string('pipeline')->default('default');
            $t->string('status')->default('open');
            $t->string('priority')->default('normal');
            $t->string('subject');
            $t->text('description')->nullable();
            $t->json('related_refs')->nullable();
            $t->json('entitlement')->nullable();
            $t->unsignedTinyInteger('escalation_level')->default(0);
            $t->timestamps();
            $t->index(['team_id', 'status', 'priority']);
            $t->unique(['team_id', 'case_key']);
        });
        Schema::create('crm_case_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('case_id');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('before')->nullable();
            $t->json('after')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_case_audits');
        Schema::dropIfExists('crm_cases');
    }
};
