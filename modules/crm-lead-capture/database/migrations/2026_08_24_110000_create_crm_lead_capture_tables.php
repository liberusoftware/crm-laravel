<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_captures', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('kind', 40);
            $table->string('status', 32)->default('received');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source', 120)->nullable();
            $table->string('source_medium', 120)->nullable();
            $table->string('source_campaign', 160)->nullable();
            $table->string('external_id', 180)->nullable();
            $table->string('dedupe_key', 255)->nullable();
            $table->json('source_metadata')->nullable();
            $table->json('payload')->nullable();
            $table->json('provenance')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'kind', 'status']);
            $table->index(['team_id', 'dedupe_key']);
            $table->index(['team_id', 'source', 'source_campaign']);
        });
        Schema::create('crm_lead_capture_forms', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('kind', 24);
            $table->string('name');
            $table->string('slug', 180);
            $table->string('status', 24)->default('draft');
            $table->json('schema');
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('submissions_count')->default(0);
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });
        Schema::create('crm_lead_capture_qr_codes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('name');
            $table->string('code', 100);
            $table->string('destination', 2048);
            $table->string('status', 24)->default('active');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('scan_count')->default(0);
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });
        Schema::create('crm_lead_capture_referrals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('code', 100);
            $table->string('referrer_type', 120)->nullable();
            $table->unsignedBigInteger('referrer_id')->nullable();
            $table->string('referred_type', 120)->nullable();
            $table->unsignedBigInteger('referred_id')->nullable();
            $table->string('status', 24)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'code']);
        });
        Schema::create('crm_lead_capture_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('capture_id')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event', 80);
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_capture_audits');
        Schema::dropIfExists('crm_lead_capture_referrals');
        Schema::dropIfExists('crm_lead_capture_qr_codes');
        Schema::dropIfExists('crm_lead_capture_forms');
        Schema::dropIfExists('crm_lead_captures');
    }
};
