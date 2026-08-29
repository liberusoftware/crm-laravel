<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_chat_bots', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->json('playbook');
            $t->json('office_hours')->nullable();
            $t->json('channels')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_chat_sessions', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('bot_id');
            $t->string('visitor_key');
            $t->string('status')->default('active');
            $t->string('channel')->default('web');
            $t->string('handoff_to')->nullable();
            $t->json('qualification')->nullable();
            $t->json('transcript')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'visitor_key', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_chat_sessions');
        Schema::dropIfExists('crm_chat_bots');
    }
};
