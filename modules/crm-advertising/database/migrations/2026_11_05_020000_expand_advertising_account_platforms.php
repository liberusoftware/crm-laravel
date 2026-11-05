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
        Schema::table('advertising_accounts', function (Blueprint $table): void {
            $table->string('platform', 80)->change();
            $table->text('access_token')->nullable()->change();
        });
        foreach (['Facebook Advertising' => 'facebook', 'LinkedIn Business' => 'linkedin', 'Google AdWords' => 'google'] as $old => $new) {
            DB::table('advertising_accounts')->where('platform', $old)->update(['platform' => $new]);
        }
    }

    public function down(): void
    {
        $platforms = ['facebook', 'linkedin', 'google', 'Facebook Advertising', 'LinkedIn Business', 'Google AdWords'];
        if (DB::table('advertising_accounts')->whereNotIn('platform', $platforms)->orWhereNull('access_token')->exists()) {
            throw new RuntimeException('Restore legacy-compatible advertising accounts and tokens before rolling back this migration.');
        }
        foreach (['facebook' => 'Facebook Advertising', 'linkedin' => 'LinkedIn Business', 'google' => 'Google AdWords'] as $old => $new) {
            DB::table('advertising_accounts')->where('platform', $old)->update(['platform' => $new]);
        }
        Schema::table('advertising_accounts', function (Blueprint $table): void {
            $table->enum('platform', ['Google AdWords', 'LinkedIn Business', 'Facebook Advertising'])->change();
            $table->text('access_token')->nullable(false)->change();
        });
    }
};
