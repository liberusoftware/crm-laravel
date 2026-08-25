<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_routing_rules', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->unsignedInteger('priority')->default(0);
            $t->json('conditions')->nullable();
            $t->json('action')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
        Schema::create('crm_routing_agents', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->unsignedBigInteger('user_id');
            $t->json('territories')->nullable();
            $t->json('skills')->nullable();
            $t->json('languages')->nullable();
            $t->json('availability')->nullable();
            $t->unsignedInteger('workload')->default(0);
            $t->decimal('value_capacity', 20, 6)->nullable();
            $t->unsignedInteger('sla_minutes')->nullable();
            $t->timestamp('last_assigned_at')->nullable();
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'user_id']);
        });
        Schema::create('crm_routing_assignments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('agent_id')->constrained('crm_routing_agents')->cascadeOnDelete();
            $t->string('subject_type');
            $t->unsignedBigInteger('subject_id');
            $t->string('status')->default('pending');
            $t->timestamp('acceptance_due_at')->nullable();
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('fallback_at')->nullable();
            $t->json('criteria')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
    }

    public function down(): void
    {
        foreach (['crm_routing_assignments', 'crm_routing_agents', 'crm_routing_rules'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
