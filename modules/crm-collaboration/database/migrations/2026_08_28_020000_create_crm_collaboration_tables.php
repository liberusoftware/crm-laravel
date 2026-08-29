<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_collaboration_records', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('record_key');
            $t->string('kind');
            $t->string('body');
            $t->string('author_key');
            $t->string('status')->default('open');
            $t->json('mentions')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'record_key', 'kind']);
        });
        Schema::create('crm_collaboration_work', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('queue_key');
            $t->string('subject_key');
            $t->string('assignee_key')->nullable();
            $t->string('status')->default('open');
            $t->string('handoff_reason')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'queue_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_collaboration_work');
        Schema::dropIfExists('crm_collaboration_records');
    }
};
