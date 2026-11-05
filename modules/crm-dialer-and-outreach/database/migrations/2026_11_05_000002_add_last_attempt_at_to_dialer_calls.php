<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('crm_dialer_calls', function (Blueprint $table): void {
            $table->timestamp('last_attempt_at')->nullable();
            $table->index(['team_id', 'contact_id', 'last_attempt_at'], 'dialer_contact_last_attempt');
        });
        Schema::table('call_logs', function (Blueprint $table): void {
            $table->index(['team_id', 'contact_id', 'created_at'], 'call_logs_contact_history');
        });
        Schema::table('crm_telephony_calls', function (Blueprint $table): void {
            $table->index(['team_id', 'contact_id', 'started_at'], 'telephony_contact_history');
        });
        DB::table('crm_dialer_calls')->where('attempts', '>', 0)->update(['last_attempt_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('call_logs', fn (Blueprint $table) => $table->dropIndex('call_logs_contact_history'));
        Schema::table('crm_telephony_calls', fn (Blueprint $table) => $table->dropIndex('telephony_contact_history'));
        Schema::table('crm_dialer_calls', function (Blueprint $table): void {
            $table->dropIndex('dialer_contact_last_attempt');
            $table->dropColumn('last_attempt_at');
        });
    }
};
