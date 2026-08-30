<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_ci_conversations', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('subject');
            $t->string('type')->default('meeting');
            $t->string('status')->default('recorded');
            $t->string('recording_url')->nullable();
            $t->text('transcript')->nullable();
            $t->json('summary')->nullable();
            $t->json('insights')->nullable();
            $t->json('sentiment_policy')->nullable();
            $t->timestamps();
        });
        Schema::create('crm_ci_evidence', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('conversation_id');
            $t->string('kind');
            $t->string('label');
            $t->text('content');
            $t->unsignedInteger('start_second')->nullable();
            $t->unsignedInteger('end_second')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'kind', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_ci_evidence');
        Schema::dropIfExists('crm_ci_conversations');
    }
};
