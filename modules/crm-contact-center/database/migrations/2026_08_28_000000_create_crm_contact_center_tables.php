<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_contact_center_agents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('user_id');
            $t->string('presence')->default('offline');
            $t->unsignedInteger('capacity')->default(1);
            $t->json('skills')->nullable();
            $t->json('policy')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'user_id']);
        });
        Schema::create('crm_contact_center_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('agent_id');
            $t->string('type');
            $t->string('queue_key')->nullable();
            $t->string('status')->default('open');
            $t->unsignedInteger('sla_seconds')->nullable();
            $t->unsignedInteger('wait_seconds')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_contact_center_events');
        Schema::dropIfExists('crm_contact_center_agents');
    }
};
