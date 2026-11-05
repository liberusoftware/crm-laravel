<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_platform_settings', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('provider')->default('direct');
            $table->text('credentials')->nullable();
            $table->json('options')->nullable();
            $table->timestamp('setup_completed_at')->nullable();
            $table->timestamps();
        });
        Schema::table('teams', function (Blueprint $table): void {
            $table->json('marketing_preferences')->nullable();
        });
        Schema::table('social_media_posts', function (Blueprint $table): void {
            $table->string('publishing_provider')->nullable();
            $table->timestamp('publishing_started_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('social_media_posts', fn (Blueprint $table) => $table->dropColumn(['publishing_provider', 'publishing_started_at', 'last_error', 'approved_at', 'approved_by']));
        Schema::table('teams', fn (Blueprint $table) => $table->dropColumn('marketing_preferences'));
        Schema::dropIfExists('marketing_platform_settings');
    }
};
