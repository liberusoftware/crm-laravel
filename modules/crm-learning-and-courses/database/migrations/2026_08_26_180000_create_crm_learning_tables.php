<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_learning_courses', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('slug');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'slug']);
        });
        Schema::create('crm_learning_lessons', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('course_id')->constrained('crm_learning_courses')->cascadeOnDelete();
            $t->string('slug');
            $t->string('name');
            $t->unsignedInteger('position');
            $t->string('media_reference')->nullable();
            $t->json('drip')->nullable();
            $t->timestamps();
            $t->unique(['course_id', 'slug']);
        });
        Schema::create('crm_learning_enrollments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('course_id')->constrained('crm_learning_courses')->cascadeOnDelete();
            $t->unsignedBigInteger('member_id');
            $t->string('status')->default('active');
            $t->string('entitlement_reference')->nullable();
            $t->decimal('progress', 5, 2)->default(0);
            $t->timestamp('completed_at')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'course_id', 'member_id']);
        });
        Schema::create('crm_learning_records', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('enrollment_id')->constrained('crm_learning_enrollments')->cascadeOnDelete();
            $t->foreignId('lesson_id')->nullable()->constrained('crm_learning_lessons')->nullOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->decimal('score', 5, 2)->nullable();
            $t->string('certificate_reference')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'enrollment_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_learning_records');
        Schema::dropIfExists('crm_learning_enrollments');
        Schema::dropIfExists('crm_learning_lessons');
        Schema::dropIfExists('crm_learning_courses');
    }
};
