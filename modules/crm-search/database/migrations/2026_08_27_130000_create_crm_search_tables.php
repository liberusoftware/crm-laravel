<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_search_documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('record_type');
            $table->string('record_key');
            $table->string('title');
            $table->text('content')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamp('indexed_at')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'record_type', 'record_key']);
            $table->index(['team_id', 'title']);
        });
        Schema::create('crm_search_views', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('record_type');
            $table->json('filters');
            $table->boolean('shared')->default(false);
            $table->timestamps();
        });
        Schema::create('crm_search_recents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('record_type');
            $table->string('record_key');
            $table->string('title');
            $table->timestamp('viewed_at');
            $table->timestamps();
            $table->index(['team_id', 'user_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_search_recents');
        Schema::dropIfExists('crm_search_views');
        Schema::dropIfExists('crm_search_documents');
    }
};
