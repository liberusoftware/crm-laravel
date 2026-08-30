<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_forms_surveys', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('kind')->default('form');
            $t->string('status')->default('draft');
            $t->json('schema');
            $t->json('settings')->nullable();
            $t->json('embedding')->nullable();
            $t->unsignedInteger('submissions_count')->default(0);
            $t->timestamps();
            $t->unique(['team_id', 'slug']);
        });
        Schema::create('crm_forms_submissions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('form_id')->constrained('crm_forms_surveys')->cascadeOnDelete();
            $t->string('status')->default('received');
            $t->string('spam_status')->default('unchecked');
            $t->boolean('consent')->default(false);
            $t->string('visitor_key')->nullable();
            $t->json('attribution')->nullable();
            $t->json('payload');
            $t->timestamps();
            $t->index(['team_id', 'form_id', 'status']);
        });
        Schema::create('crm_forms_follow_ups', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('submission_id')->constrained('crm_forms_submissions')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('pending');
            $t->text('details')->nullable();
            $t->timestamp('scheduled_at')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'submission_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_forms_follow_ups');
        Schema::dropIfExists('crm_forms_submissions');
        Schema::dropIfExists('crm_forms_surveys');
    }
};
