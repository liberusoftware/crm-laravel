<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_release_snapshots', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('environment')->default('sandbox');
            $t->string('status')->default('draft');
            $t->json('configuration');
            $t->json('test_data_policy')->nullable();
            $t->string('checksum', 64);
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
            $t->index(['team_id', 'environment', 'status']);
        });
        Schema::create('crm_release_changesets', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->json('changes');
            $t->json('dependencies')->nullable();
            $t->json('validation')->nullable();
            $t->string('source_environment')->default('sandbox');
            $t->string('target_environment')->default('staging');
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
            $t->index(['team_id', 'status']);
        });
        Schema::create('crm_release_deployments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('changeset_id')->constrained('crm_release_changesets')->cascadeOnDelete();
            $t->string('environment');
            $t->string('operation');
            $t->string('status')->default('pending');
            $t->json('comparison')->nullable();
            $t->text('failure_reason')->nullable();
            $t->unsignedBigInteger('actor_id');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_release_deployments', 'crm_release_changesets', 'crm_release_snapshots'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
