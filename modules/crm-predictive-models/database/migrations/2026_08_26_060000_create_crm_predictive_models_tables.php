<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_predictive_model_registry', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('kind');
            $table->string('version');
            $table->string('status')->default('staged');
            $table->json('configuration')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'name', 'version']);
        });
        Schema::create('crm_predictive_predictions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('model_id');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('kind');
            $table->decimal('score', 8, 4)->nullable();
            $table->string('label')->nullable();
            $table->json('explanation')->nullable();
            $table->json('features')->nullable();
            $table->timestamp('predicted_at');
            $table->timestamps();
            $table->index(['team_id', 'subject_type', 'subject_id']);
        });
        Schema::create('crm_predictive_evaluations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('model_id');
            $table->decimal('accuracy', 8, 4)->nullable();
            $table->decimal('precision_score', 8, 4)->nullable();
            $table->decimal('recall', 8, 4)->nullable();
            $table->json('metrics')->nullable();
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });
        Schema::create('crm_predictive_drift', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('model_id');
            $table->string('feature');
            $table->decimal('baseline', 12, 6);
            $table->decimal('observed', 12, 6);
            $table->decimal('threshold', 12, 6);
            $table->string('status')->default('normal');
            $table->timestamp('detected_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_predictive_drift');
        Schema::dropIfExists('crm_predictive_evaluations');
        Schema::dropIfExists('crm_predictive_predictions');
        Schema::dropIfExists('crm_predictive_model_registry');
    }
};
