<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('crm_advocacy_records')) {
            return;
        }
        Schema::create('crm_advocacy_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('kind', 48);
            $table->string('name');
            $table->string('status', 24)->default('draft');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'kind', 'status']);
            $table->index(['team_id', 'contact_id']);
            $table->unique(['team_id', 'kind', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_advocacy_records');
    }
};
