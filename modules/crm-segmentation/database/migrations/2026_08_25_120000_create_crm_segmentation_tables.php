<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_segmentation_audiences', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->text('description')->nullable();
            $t->string('kind')->default('dynamic');
            $t->string('status')->default('draft');
            $t->json('conditions')->nullable();
            $t->json('exclusions')->nullable();
            $t->json('calculated_attributes')->nullable();
            $t->unsignedInteger('estimated_count')->default(0);
            $t->timestamp('refreshed_at')->nullable();
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
            $t->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_segmentation_members', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('audience_id')->constrained('crm_segmentation_audiences')->cascadeOnDelete();
            $t->unsignedBigInteger('contact_id');
            $t->json('attributes')->nullable();
            $t->timestamp('included_at');
            $t->timestamps();
            $t->unique(['audience_id', 'contact_id']);
        });
        Schema::create('crm_segmentation_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('contact_id');
            $t->string('event');
            $t->json('properties')->nullable();
            $t->timestamp('occurred_at');
            $t->timestamps();
            $t->index(['team_id', 'contact_id', 'event']);
        });
        Schema::create('crm_segmentation_lineage', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('audience_id')->constrained('crm_segmentation_audiences')->cascadeOnDelete();
            $t->string('source_type');
            $t->unsignedBigInteger('source_id')->nullable();
            $t->string('operation');
            $t->json('details')->nullable();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_segmentation_lineage', 'crm_segmentation_events', 'crm_segmentation_members', 'crm_segmentation_audiences'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
