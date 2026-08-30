<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('crm_telephony_settings', function (Blueprint $table): void {
            $table->string('account_sid')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('messaging_service_sid')->nullable();
            $table->string('default_from_number', 32)->nullable();
            $table->timestamp('credentials_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('crm_telephony_settings', function (Blueprint $table): void {
            $table->dropColumn(['account_sid', 'auth_token', 'messaging_service_sid', 'default_from_number', 'credentials_verified_at']);
        });
    }
};
