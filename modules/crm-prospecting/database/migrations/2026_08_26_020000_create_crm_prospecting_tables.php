<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_ideal_customer_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->json('criteria');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_prospect_searches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('profile_id')->nullable();
            $table->string('provider');
            $table->json('filters');
            $table->string('status')->default('queued');
            $table->unsignedInteger('result_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_prospects', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('search_id')->nullable();
            $table->string('provider');
            $table->string('provider_id')->nullable();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('new');
            $table->json('provenance')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'provider', 'provider_id']);
        });
        Schema::create('crm_prospect_research_queue', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('prospect_id');
            $table->string('status')->default('queued');
            $table->string('priority')->default('normal');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_prospect_credits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('prospect_id');
            $table->unsignedBigInteger('user_id');
            $table->string('kind');
            $table->string('idempotency_key')->unique();
            $table->timestamp('revealed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_prospect_exports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('format')->default('csv');
            $table->string('status')->default('queued');
            $table->string('purpose');
            $table->text('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_prospect_exports');
        Schema::dropIfExists('crm_prospect_credits');
        Schema::dropIfExists('crm_prospect_research_queue');
        Schema::dropIfExists('crm_prospects');
        Schema::dropIfExists('crm_prospect_searches');
        Schema::dropIfExists('crm_ideal_customer_profiles');
    }
};
