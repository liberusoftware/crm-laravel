<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('team_subscriptions', function (Blueprint $table): void {
            $table->string('plan_key')->nullable()->after('stripe_price');
            $table->timestamp('current_period_ends_at')->nullable()->after('trial_ends_at');
            $table->timestamp('cancelled_at')->nullable()->after('ends_at');
            $table->index(['stripe_status', 'trial_ends_at']);
        });
    }

    public function down(): void
    {
        Schema::table('team_subscriptions', function (Blueprint $table): void {
            $table->dropIndex(['stripe_status', 'trial_ends_at']);
            $table->dropColumn(['plan_key', 'current_period_ends_at', 'cancelled_at']);
        });
    }
};
