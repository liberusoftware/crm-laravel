<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_analytics_assets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('owner_id');
            $table->string('name');
            $table->string('kind');
            $table->string('status')->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->json('definition');
            $table->json('lineage')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_analytics_executions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('kind')->default('preview');
            $table->string('status')->default('queued');
            $table->json('parameters')->nullable();
            $table->json('result')->nullable();
            $table->text('failure')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_analytics_access', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('role')->default('viewer');
            $table->timestamps();
            $table->unique(['asset_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_analytics_access');
        Schema::dropIfExists('crm_analytics_executions');
        Schema::dropIfExists('crm_analytics_assets');
    }
};
