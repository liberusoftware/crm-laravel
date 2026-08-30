<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_configurations', function (Blueprint $table): void {
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('oauth_configurations', function (Blueprint $table): void {
            $table->dropColumn(['access_token', 'refresh_token', 'token_expires_at']);
        });
    }
};
