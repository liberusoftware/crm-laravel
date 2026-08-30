<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_white_label_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->unique();
            $table->string('brand_name')->nullable();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('theme')->default('default');
            $table->json('email_settings')->nullable();
            $table->json('application_settings')->nullable();
            $table->json('client_experience')->nullable();
            $table->string('provider')->default('platform');
            $table->boolean('show_platform_attribution')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->index('provider');
        });

        Schema::create('crm_white_label_audits', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('actor_id')->nullable()->index();
            $table->string('event');
            $table->json('changes')->nullable();
            $table->string('request_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_white_label_audits');
        Schema::dropIfExists('crm_white_label_settings');
    }
};
