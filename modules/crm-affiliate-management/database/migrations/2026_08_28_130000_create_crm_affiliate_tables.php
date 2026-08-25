<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_affiliates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('status')->default('applicant');
            $table->string('payout_method')->nullable();
            $table->json('profile')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_affiliate_links', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('affiliate_id')->constrained('crm_affiliates')->cascadeOnDelete();
            $table->string('code');
            $table->string('campaign')->nullable();
            $table->string('destination');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['team_id', 'code']);
        });
        Schema::create('crm_affiliate_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('affiliate_id')->constrained('crm_affiliates')->cascadeOnDelete();
            $table->foreignId('link_id')->nullable()->constrained('crm_affiliate_links')->nullOnDelete();
            $table->string('type');
            $table->string('external_key')->nullable();
            $table->decimal('value', 14, 2)->default(0);
            $table->decimal('commission', 14, 2)->default(0);
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'type', 'external_key']);
        });
        Schema::create('crm_affiliate_payouts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->foreignId('affiliate_id')->constrained('crm_affiliates')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('status')->default('pending');
            $table->json('dispute')->nullable();
            $table->json('assets')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_affiliate_payouts');
        Schema::dropIfExists('crm_affiliate_events');
        Schema::dropIfExists('crm_affiliate_links');
        Schema::dropIfExists('crm_affiliates');
    }
};
