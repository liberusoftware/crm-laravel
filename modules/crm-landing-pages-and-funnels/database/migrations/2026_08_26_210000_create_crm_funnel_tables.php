<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_funnels', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->string('domain')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'slug']);
        });
        Schema::create('crm_funnel_pages', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('funnel_id')->constrained('crm_funnels')->cascadeOnDelete();
            $t->string('slug');
            $t->string('kind')->default('landing');
            $t->string('status')->default('draft');
            $t->unsignedInteger('position');
            $t->text('content')->nullable();
            $t->json('seo')->nullable();
            $t->json('personalization')->nullable();
            $t->json('form')->nullable();
            $t->string('order_link')->nullable();
            $t->timestamps();
            $t->unique(['funnel_id', 'slug']);
        });
        Schema::create('crm_funnel_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('funnel_id')->constrained('crm_funnels')->cascadeOnDelete();
            $t->foreignId('page_id')->nullable()->constrained('crm_funnel_pages')->nullOnDelete();
            $t->string('kind');
            $t->string('visitor_key')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'funnel_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_funnel_events');
        Schema::dropIfExists('crm_funnel_pages');
        Schema::dropIfExists('crm_funnels');
    }
};
