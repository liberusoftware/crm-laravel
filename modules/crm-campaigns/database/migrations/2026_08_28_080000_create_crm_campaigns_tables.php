<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_campaigns', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->string('name');
            $t->string('status')->default('draft');
            $t->text('brief')->nullable();
            $t->json('objectives')->nullable();
            $t->json('audience')->nullable();
            $t->json('channels')->nullable();
            $t->json('assets')->nullable();
            $t->decimal('budget', 15, 2)->default(0);
            $t->decimal('cost', 15, 2)->default(0);
            $t->decimal('influence', 15, 2)->default(0);
            $t->decimal('revenue', 15, 2)->default(0);
            $t->date('starts_on')->nullable();
            $t->date('ends_on')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'starts_on']);
        });
        Schema::create('crm_campaign_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('campaign_id');
            $t->unsignedBigInteger('actor_id');
            $t->string('type');
            $t->decimal('value', 15, 2)->default(0);
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_campaign_events');
        Schema::dropIfExists('crm_campaigns');
    }
};
