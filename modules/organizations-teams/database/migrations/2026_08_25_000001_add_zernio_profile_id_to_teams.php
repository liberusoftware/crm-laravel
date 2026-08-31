<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('teams', 'zernio_profile_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table): void {
            $table->string('zernio_profile_id', 64)->nullable()->unique()->after('personal_team');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('teams', 'zernio_profile_id')) {
            return;
        }

        Schema::table('teams', function (Blueprint $table): void {
            $table->dropUnique(['zernio_profile_id']);
            $table->dropColumn('zernio_profile_id');
        });
    }
};
