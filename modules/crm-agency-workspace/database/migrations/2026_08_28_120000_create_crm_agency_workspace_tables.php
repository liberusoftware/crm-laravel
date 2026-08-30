<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_agency_accounts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('account_type')->default('client');
            $table->string('status')->default('active');
            $table->json('branding')->nullable();
            $table->json('usage_snapshot')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_agency_access', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('account_id')->constrained('crm_agency_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('role')->default('delegate');
            $table->string('status')->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->timestamps();
            $table->unique(['account_id', 'user_id']);
        });
        Schema::create('crm_agency_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('account_id')->constrained('crm_agency_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('type');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_agency_audits');
        Schema::dropIfExists('crm_agency_access');
        Schema::dropIfExists('crm_agency_accounts');
    }
};
