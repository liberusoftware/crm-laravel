<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_enrichment_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('entity_type');
            $table->string('entity_key');
            $table->string('status')->default('pending');
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->json('firmographic')->nullable();
            $table->json('demographic')->nullable();
            $table->json('technographic')->nullable();
            $table->json('social')->nullable();
            $table->json('verification')->nullable();
            $table->string('provider')->nullable();
            $table->timestamp('enriched_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'entity_type', 'entity_key']);
        });
        Schema::create('crm_enrichment_fields', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_enrichment_profiles');
            $table->string('field');
            $table->text('value')->nullable();
            $table->string('source');
            $table->unsignedTinyInteger('confidence')->default(0);
            $table->timestamp('observed_at');
            $table->timestamps();
            $table->index(['profile_id', 'field']);
        });
        Schema::create('crm_enrichment_changes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_enrichment_profiles');
            $table->string('field');
            $table->text('before')->nullable();
            $table->text('after')->nullable();
            $table->string('status')->default('detected');
            $table->timestamp('detected_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_enrichment_changes');
        Schema::dropIfExists('crm_enrichment_fields');
        Schema::dropIfExists('crm_enrichment_profiles');
    }
};
