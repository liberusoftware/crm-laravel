<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_self_service_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('display_name');
            $table->string('email');
            $table->json('preferences')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });
        Schema::create('crm_self_service_cases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_self_service_profiles');
            $table->string('subject');
            $table->text('description');
            $table->string('status')->default('open');
            $table->string('priority')->default('normal');
            $table->timestamps();
        });
        Schema::create('crm_self_service_resources', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('kind');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('url')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->index(['team_id', 'kind', 'published']);
        });
        Schema::create('crm_self_service_references', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('profile_id')->constrained('crm_self_service_profiles');
            $table->string('kind');
            $table->string('reference_key');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_self_service_references');
        Schema::dropIfExists('crm_self_service_resources');
        Schema::dropIfExists('crm_self_service_cases');
        Schema::dropIfExists('crm_self_service_profiles');
    }
};
