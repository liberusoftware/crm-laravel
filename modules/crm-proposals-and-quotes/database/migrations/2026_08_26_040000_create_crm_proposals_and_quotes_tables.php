<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_proposal_templates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('name');
            $table->json('branding')->nullable();
            $table->json('sections')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        Schema::create('crm_proposals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('title');
            $table->string('status')->default('draft');
            $table->string('currency', 3)->default('USD');
            $table->decimal('total', 14, 2)->default(0);
            $table->date('expires_at')->nullable();
            $table->string('access_token')->unique();
            $table->timestamps();
        });
        Schema::create('crm_proposal_versions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedInteger('version');
            $table->json('scope')->nullable();
            $table->json('line_items');
            $table->json('options')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['team_id', 'proposal_id', 'version']);
        });
        Schema::create('crm_proposal_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('proposal_id');
            $table->unsignedBigInteger('version_id')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_proposal_events');
        Schema::dropIfExists('crm_proposal_versions');
        Schema::dropIfExists('crm_proposals');
        Schema::dropIfExists('crm_proposal_templates');
    }
};
