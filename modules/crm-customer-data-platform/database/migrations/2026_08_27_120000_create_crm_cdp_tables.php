<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_cdp_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('profile_key');
            $table->string('status')->default('active');
            $table->json('attributes')->nullable();
            $table->json('consent')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'profile_key']);
        });
        Schema::create('crm_cdp_identities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_cdp_profiles');
            $table->string('source');
            $table->string('identifier');
            $table->unsignedTinyInteger('confidence')->default(100);
            $table->timestamps();
            $table->unique(['team_id', 'source', 'identifier']);
        });
        Schema::create('crm_cdp_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_cdp_profiles');
            $table->string('name');
            $table->json('payload')->nullable();
            $table->boolean('consented')->default(false);
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
        Schema::create('crm_cdp_audiences', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('status')->default('draft');
            $table->json('definition');
            $table->json('activations')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_cdp_lineage', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_cdp_profiles');
            $table->string('source');
            $table->string('field');
            $table->timestamp('observed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_cdp_lineage');
        Schema::dropIfExists('crm_cdp_audiences');
        Schema::dropIfExists('crm_cdp_events');
        Schema::dropIfExists('crm_cdp_identities');
        Schema::dropIfExists('crm_cdp_profiles');
    }
};
