<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_knowledge_articles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('visibility')->default('internal');
            $t->string('status')->default('draft');
            $t->string('category')->nullable();
            $t->string('locale')->default('en');
            $t->string('title');
            $t->text('body');
            $t->timestamp('reviewed_at')->nullable();
            $t->timestamp('stale_at')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'slug', 'locale']);
            $t->index(['team_id', 'visibility', 'status']);
        });
        Schema::create('crm_knowledge_versions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('article_id')->constrained('crm_knowledge_articles')->cascadeOnDelete();
            $t->unsignedInteger('version');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('status')->default('draft');
            $t->text('body');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['article_id', 'version']);
        });
        Schema::create('crm_knowledge_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('article_id')->constrained('crm_knowledge_articles')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->text('details')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'article_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_knowledge_events');
        Schema::dropIfExists('crm_knowledge_versions');
        Schema::dropIfExists('crm_knowledge_articles');
    }
};
