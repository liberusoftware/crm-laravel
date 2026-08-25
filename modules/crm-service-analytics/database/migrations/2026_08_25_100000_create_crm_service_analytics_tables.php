<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_service_analytics_snapshots', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('metric');
            $t->timestamp('period_start');
            $t->timestamp('period_end');
            $t->decimal('value', 20, 6);
            $t->json('dimensions')->nullable();
            $t->string('dimensions_hash', 64);
            $t->string('source')->nullable();
            $t->unsignedBigInteger('recorded_by')->nullable();
            $t->timestamp('generated_at')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'metric', 'period_start', 'period_end', 'dimensions_hash']);
            $t->index(['team_id', 'metric', 'period_start']);
        });
        Schema::create('crm_service_analytics_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('details')->nullable();
            $t->string('request_id')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_service_analytics_audits');
        Schema::dropIfExists('crm_service_analytics_snapshots');
    }
};
