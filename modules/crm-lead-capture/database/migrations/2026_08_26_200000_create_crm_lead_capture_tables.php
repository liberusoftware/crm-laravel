<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_capture_leads', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('external_key');
            $t->string('channel');
            $t->string('status')->default('new');
            $t->string('name')->nullable();
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->string('source')->nullable();
            $t->json('source_metadata')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'external_key']);
            $t->index(['team_id', 'channel', 'status']);
        });
        Schema::create('crm_lead_capture_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('lead_id')->constrained('crm_lead_capture_leads')->cascadeOnDelete();
            $t->string('kind');
            $t->string('reference')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'lead_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_capture_events');
        Schema::dropIfExists('crm_lead_capture_leads');
    }
};
