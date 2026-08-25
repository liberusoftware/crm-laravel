<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_revenue_insights', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('kind');
            $table->unsignedTinyInteger('score')->nullable();
            $table->string('severity')->default('info');
            $table->json('payload')->nullable();
            $table->timestamp('observed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id']);
        });
        Schema::create('crm_revenue_intelligence_alerts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('kind');
            $table->string('severity');
            $table->string('status')->default('open');
            $table->text('message');
            $table->json('payload')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_revenue_intelligence_alerts');
        Schema::dropIfExists('crm_revenue_insights');
    }
};
