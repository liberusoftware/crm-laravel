<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_attribution_touchpoints', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key')->index();
            $table->string('source')->index();
            $table->string('medium')->nullable();
            $table->string('campaign')->nullable();
            $table->string('content')->nullable();
            $table->string('term')->nullable();
            $table->string('click_id')->nullable()->index();
            $table->string('channel')->nullable();
            $table->decimal('cost', 12, 2)->default(0);
            $table->timestamp('occurred_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'visitor_key', 'click_id']);
        });

        Schema::create('crm_attribution_conversions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('visitor_key')->index();
            $table->string('conversion_key');
            $table->string('model')->default('multi_touch');
            $table->decimal('value', 14, 2)->default(0);
            $table->json('allocations')->nullable();
            $table->timestamp('converted_at')->index();
            $table->timestamps();
            $table->unique(['team_id', 'visitor_key', 'conversion_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_attribution_conversions');
        Schema::dropIfExists('crm_attribution_touchpoints');
    }
};
