<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_dialer_lists', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('mode')->default('preview');
            $table->string('status')->default('draft');
            $table->json('local_time_policy')->nullable();
            $table->json('compliance')->nullable();
            $table->json('script')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_dialer_calls', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('list_id')->constrained('crm_dialer_lists');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('phone');
            $table->string('status')->default('queued');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('outcome')->nullable();
            $table->boolean('voicemail_dropped')->default(false);
            $table->string('detection')->nullable();
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'status']);
        });
        Schema::create('crm_dialer_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('call_id')->constrained('crm_dialer_calls');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_dialer_events');
        Schema::dropIfExists('crm_dialer_calls');
        Schema::dropIfExists('crm_dialer_lists');
    }
};
