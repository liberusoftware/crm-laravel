<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_forecast_categories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedTinyInteger('weight')->default(100);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_forecasts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('category_id')->constrained('crm_forecast_categories');
            $table->string('period');
            $table->string('scenario')->default('base');
            $table->decimal('pipeline', 16, 2)->default(0);
            $table->decimal('best_case', 16, 2)->default(0);
            $table->decimal('commit', 16, 2)->default(0);
            $table->decimal('coverage', 12, 4)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'category_id', 'period', 'scenario']);
        });
        Schema::create('crm_forecast_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('forecast_id')->constrained('crm_forecasts');
            $table->unsignedBigInteger('actor_id');
            $table->decimal('amount', 16, 2);
            $table->text('reason');
            $table->timestamps();
        });
        Schema::create('crm_forecast_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('forecast_id')->constrained('crm_forecasts');
            $table->unsignedBigInteger('actor_id');
            $table->string('status')->default('submitted');
            $table->timestamp('submitted_at');
            $table->json('snapshot');
            $table->timestamps();
        });
        Schema::create('crm_forecast_accuracy', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('forecast_id')->constrained('crm_forecasts');
            $table->decimal('actual', 16, 2)->nullable();
            $table->decimal('variance', 16, 2)->nullable();
            $table->decimal('accuracy', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_forecast_accuracy');
        Schema::dropIfExists('crm_forecast_submissions');
        Schema::dropIfExists('crm_forecast_adjustments');
        Schema::dropIfExists('crm_forecasts');
        Schema::dropIfExists('crm_forecast_categories');
    }
};
