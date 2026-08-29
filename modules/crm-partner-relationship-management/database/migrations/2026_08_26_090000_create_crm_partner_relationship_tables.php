<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_partner_accounts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->string('tier')->default('registered');
            $table->string('status')->default('prospect');
            $table->text('agreement')->nullable();
            $table->json('competencies')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_partner_contacts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('partner_id');
            $table->string('name');
            $table->string('email');
            $table->string('role')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'partner_id', 'email']);
        });
        Schema::create('crm_partner_activities', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('partner_id');
            $table->string('kind');
            $table->string('status')->default('open');
            $table->decimal('value', 14, 2)->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
        Schema::create('crm_partner_performance', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('partner_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('revenue', 14, 2)->default(0);
            $table->unsignedInteger('deals')->default(0);
            $table->decimal('score', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_partner_performance');
        Schema::dropIfExists('crm_partner_activities');
        Schema::dropIfExists('crm_partner_contacts');
        Schema::dropIfExists('crm_partner_accounts');
    }
};
