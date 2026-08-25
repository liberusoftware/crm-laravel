<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_community_spaces', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('name');
            $t->string('kind')->default('customer');
            $t->string('status')->default('active');
            $t->json('settings')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_community_memberships', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('space_id');
            $t->string('subject_key');
            $t->string('role')->default('member');
            $t->string('status')->default('active');
            $t->unsignedInteger('points')->default(0);
            $t->timestamps();
            $t->unique(['space_id', 'subject_key']);
        });
        Schema::create('crm_community_content', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('space_id');
            $t->string('author_key');
            $t->string('kind')->default('post');
            $t->text('body');
            $t->string('status')->default('published');
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['space_id', 'status', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_community_content');
        Schema::dropIfExists('crm_community_memberships');
        Schema::dropIfExists('crm_community_spaces');
    }
};
