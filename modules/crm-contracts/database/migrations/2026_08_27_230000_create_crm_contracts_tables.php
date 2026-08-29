<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contracts', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->unsignedInteger('version')->default(1);
            $t->json('parties');
            $t->json('terms');
            $t->json('clauses')->nullable();
            $t->json('obligations')->nullable();
            $t->json('repository_links')->nullable();
            $t->date('starts_on')->nullable();
            $t->date('ends_on')->nullable();
            $t->date('renewal_on')->nullable();
            $t->date('next_notice_on')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'ends_on']);
        });
        Schema::create('crm_contract_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('contract_id');
            $t->unsignedBigInteger('actor_id');
            $t->string('type');
            $t->string('status')->default('completed');
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['contract_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contract_events');
        Schema::dropIfExists('crm_contracts');
    }
};
