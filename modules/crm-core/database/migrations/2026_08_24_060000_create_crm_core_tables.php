<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_core_records', function (Blueprint $table): void {
            $table->id();
            $table->string('record_type', 40);
            $table->foreignId('team_id')->index();
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->string('name');
            $table->string('status', 30)->default('active');
            $table->json('data')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'record_type', 'status']);
        });

        Schema::create('crm_core_tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['team_id', 'slug']);
        });

        Schema::create('crm_core_taggables', function (Blueprint $table): void {
            $table->foreignId('tag_id')->constrained('crm_core_tags')->cascadeOnDelete();
            $table->morphs('taggable');
            $table->primary(['tag_id', 'taggable_id', 'taggable_type']);
        });

        Schema::create('crm_core_notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->unsignedBigInteger('author_id')->nullable()->index();
            $table->morphs('recordable');
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('crm_core_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();
            $table->morphs('attachable');
            $table->string('disk', 50);
            $table->string('path');
            $table->string('name');
            $table->string('mime_type', 255);
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_core_timeline', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->morphs('recordable');
            $table->string('event_type', 80);
            $table->string('summary');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'event_type', 'created_at']);
        });

        Schema::create('crm_core_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->morphs('favoritable');
            $table->timestamps();
            $table->unique(
                ['team_id', 'user_id', 'favoritable_id', 'favoritable_type'],
                'crm_core_favorites_team_user_favoritable_unique',
            );
        });

        Schema::create('crm_core_relationships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->morphs('from');
            $table->morphs('to');
            $table->string('relationship_type', 80);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['from_id', 'from_type', 'to_id', 'to_type', 'relationship_type'], 'crm_core_relationship_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_core_relationships');
        Schema::dropIfExists('crm_core_favorites');
        Schema::dropIfExists('crm_core_timeline');
        Schema::dropIfExists('crm_core_attachments');
        Schema::dropIfExists('crm_core_notes');
        Schema::dropIfExists('crm_core_taggables');
        Schema::dropIfExists('crm_core_tags');
        Schema::dropIfExists('crm_core_records');
    }
};
