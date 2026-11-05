<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('crm_dialer_calls', function (Blueprint $table): void {
            $table->string('provider_sid')->nullable()->unique();
            $table->boolean('consent_granted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('crm_dialer_calls', function (Blueprint $table): void {
            $table->dropColumn(['provider_sid', 'consent_granted']);
        });
    }
};
