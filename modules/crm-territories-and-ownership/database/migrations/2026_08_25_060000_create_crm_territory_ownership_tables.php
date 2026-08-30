<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_territory_rules', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('name');
            $t->string('book_of_business')->nullable();
            $t->json('criteria')->nullable();
            $t->json('members')->nullable();
            $t->unsignedInteger('capacity')->nullable();
            $t->unsignedInteger('round_robin_cursor')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_territory_coverage', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('rule_id');
            $t->unsignedBigInteger('covered_user_id');
            $t->unsignedBigInteger('substitute_user_id');
            $t->timestamp('starts_at');
            $t->timestamp('ends_at');
            $t->timestamps();
        });
        Schema::create('crm_ownership_history', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('subject_type');
            $t->unsignedBigInteger('subject_id');
            $t->unsignedBigInteger('previous_owner_id')->nullable();
            $t->unsignedBigInteger('owner_id');
            $t->string('reason');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_ownership_history', 'crm_territory_coverage', 'crm_territory_rules'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
