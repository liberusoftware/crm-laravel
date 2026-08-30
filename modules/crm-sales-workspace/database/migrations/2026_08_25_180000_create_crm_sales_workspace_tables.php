<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_sales_workspace_items', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('owner_id')->nullable();
            $t->string('kind');
            $t->string('title');
            $t->string('status')->default('open');
            $t->string('priority')->default('normal');
            $t->timestamp('due_at')->nullable();
            $t->timestamp('last_activity_at')->nullable();
            $t->string('next_action')->nullable();
            $t->json('risk_indicators')->nullable();
            $t->json('customer_history')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'priority', 'due_at']);
        });
        Schema::create('crm_sales_workspace_updates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('item_id')->constrained('crm_sales_workspace_items')->cascadeOnDelete();
            $t->unsignedBigInteger('actor_id');
            $t->string('type');
            $t->text('body')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_sales_workspace_updates');
        Schema::dropIfExists('crm_sales_workspace_items');
    }
};
