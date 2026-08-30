<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_snapshot_bundles', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->string('name');
            $t->unsignedInteger('version');
            $t->string('status')->default('draft');
            $t->json('payload');
            $t->string('checksum', 64);
            $t->string('share_token_hash', 64)->nullable();
            $t->timestamp('shared_at')->nullable();
            $t->unsignedBigInteger('created_by');
            $t->timestamps();
            $t->unique(['team_id', 'name', 'version']);
        });
        Schema::create('crm_snapshot_installs', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('bundle_id');
            $t->unsignedInteger('version');
            $t->string('status')->default('installed');
            $t->unsignedBigInteger('installed_by');
            $t->timestamps();
            $t->unique(['team_id', 'bundle_id']);
        });
        Schema::create('crm_snapshot_audits', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('event');
            $t->json('details')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['crm_snapshot_audits', 'crm_snapshot_installs', 'crm_snapshot_bundles'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
