<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_reputation_connections', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('site');
            $table->string('location')->nullable();
            $table->string('status')->default('active');
            $table->text('credentials')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'site', 'location']);
        });
        Schema::create('crm_reputation_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->string('channel');
            $table->string('status')->default('pending');
            $table->string('token')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_reputation_reviews', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('connection_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('external_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('sentiment')->default('neutral');
            $table->text('content')->nullable();
            $table->text('response')->nullable();
            $table->string('status')->default('unresponded');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status']);
        });
        Schema::create('crm_reputation_templates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('channel');
            $table->text('content');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_reputation_rollups', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('location')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedInteger('review_count')->default(0);
            $table->decimal('average_rating', 4, 2)->default(0);
            $table->json('sentiment')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'location', 'period_start', 'period_end'],
                'crm_reputation_rollup_period_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_reputation_rollups');
        Schema::dropIfExists('crm_reputation_templates');
        Schema::dropIfExists('crm_reputation_reviews');
        Schema::dropIfExists('crm_reputation_requests');
        Schema::dropIfExists('crm_reputation_connections');
    }
};
