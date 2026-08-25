<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_gateway_channels', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('key');
            $t->string('kind');
            $t->string('provider');
            $t->string('status')->default('active');
            $t->json('configuration')->nullable();
            $t->json('health')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'key']);
        });
        Schema::create('crm_gateway_deliveries', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('channel_id');
            $t->string('idempotency_key');
            $t->string('address');
            $t->text('body');
            $t->string('status')->default('queued');
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->string('external_key')->nullable();
            $t->text('failure')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_gateway_deliveries');
        Schema::dropIfExists('crm_gateway_channels');
    }
};
