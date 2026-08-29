<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_deal_registrations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('external_key');
            $table->string('company');
            $table->string('contact_email');
            $table->string('territory')->nullable();
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('protection_until')->nullable();
            $table->json('attribution')->nullable();
            $table->json('collaborators')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'contact_email']);
        });
        Schema::create('crm_deal_registration_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('deal_id')->constrained('crm_deal_registrations');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_deal_registration_events');
        Schema::dropIfExists('crm_deal_registrations');
    }
};
