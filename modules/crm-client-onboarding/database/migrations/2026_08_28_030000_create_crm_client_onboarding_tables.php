<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_client_onboardings', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('client_key');
            $t->string('status')->default('intake');
            $t->unsignedTinyInteger('health')->default(0);
            $t->json('intake')->nullable();
            $t->json('connections')->nullable();
            $t->json('snapshot')->nullable();
            $t->date('target_launch_on')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'client_key']);
        });
        Schema::create('crm_client_onboarding_steps', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('onboarding_id');
            $t->string('kind');
            $t->string('label');
            $t->string('status')->default('pending');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->json('evidence')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['onboarding_id', 'kind', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_client_onboarding_steps');
        Schema::dropIfExists('crm_client_onboardings');
    }
};
