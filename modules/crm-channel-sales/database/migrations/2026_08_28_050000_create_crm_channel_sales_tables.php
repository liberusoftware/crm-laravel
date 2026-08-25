<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_channel_opportunities', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('partner_key');
            $t->string('opportunity_key');
            $t->string('stage')->default('registered');
            $t->decimal('amount', 15, 2)->default(0);
            $t->decimal('commission_rate', 5, 2)->default(0);
            $t->string('handoff_status')->default('pending');
            $t->json('pricing_reference')->nullable();
            $t->json('forecast')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'opportunity_key']);
        });
        Schema::create('crm_channel_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('opportunity_id');
            $t->unsignedBigInteger('actor_id');
            $t->string('type');
            $t->string('status')->default('recorded');
            $t->decimal('commission', 15, 2)->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_channel_events');
        Schema::dropIfExists('crm_channel_opportunities');
    }
};
