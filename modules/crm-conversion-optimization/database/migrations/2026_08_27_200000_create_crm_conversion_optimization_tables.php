<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_conversion_experiments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->unsignedTinyInteger('allocation')->default(100);
            $t->json('variants');
            $t->json('goals');
            $t->json('statistical_policy');
            $t->json('experience')->nullable();
            $t->timestamps();
        });
        Schema::create('crm_conversion_observations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('experiment_id');
            $t->string('subject_key');
            $t->string('variant');
            $t->string('event');
            $t->decimal('value', 15, 4)->default(1);
            $t->json('context')->nullable();
            $t->timestamps();
            $t->index(['experiment_id', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_conversion_observations');
        Schema::dropIfExists('crm_conversion_experiments');
    }
};
