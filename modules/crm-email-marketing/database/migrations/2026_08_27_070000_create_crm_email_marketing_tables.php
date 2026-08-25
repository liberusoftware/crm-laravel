<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_email_marketing_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->string('content_type')->default('code');
            $table->text('subject');
            $table->longText('content');
            $table->json('personalization')->nullable();
            $table->json('dynamic_content')->nullable();
            $table->json('deliverability')->nullable();
            $table->unsignedInteger('throttle_per_minute')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_email_marketing_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('campaign_id')->constrained('crm_email_marketing_campaigns');
            $table->string('recipient');
            $table->string('status')->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'recipient']);
        });
        Schema::create('crm_email_marketing_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('campaign_id')->constrained('crm_email_marketing_campaigns');
            $table->foreignId('delivery_id')->nullable()->constrained('crm_email_marketing_deliveries');
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_email_marketing_events');
        Schema::dropIfExists('crm_email_marketing_deliveries');
        Schema::dropIfExists('crm_email_marketing_campaigns');
    }
};
