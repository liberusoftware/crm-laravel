<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_conversation_analytics', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('analyst_id');
            $t->string('conversation_key');
            $t->json('topics')->nullable();
            $t->json('objections')->nullable();
            $t->json('competitors')->nullable();
            $t->json('questions')->nullable();
            $t->json('outcomes')->nullable();
            $t->json('talk_ratios')->nullable();
            $t->json('coaching_moments')->nullable();
            $t->json('scorecard')->nullable();
            $t->date('observed_on');
            $t->timestamps();
            $t->unique(['team_id', 'conversation_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_conversation_analytics');
    }
};
