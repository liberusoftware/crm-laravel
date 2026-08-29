<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_marketing_resources', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('key');
            $t->string('kind');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->text('content')->nullable();
            $t->string('file_reference')->nullable();
            $t->string('cms_reference')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'key']);
            $t->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_marketing_resource_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('resource_id')->constrained('crm_marketing_resources')->cascadeOnDelete();
            $t->string('kind');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('status')->default('recorded');
            $t->text('notes')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'resource_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_marketing_resource_events');
        Schema::dropIfExists('crm_marketing_resources');
    }
};
