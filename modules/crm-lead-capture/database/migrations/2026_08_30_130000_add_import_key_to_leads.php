<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->string('import_key')->nullable()->after('team_id');
            $table->unique(['team_id', 'import_key'], 'leads_team_import_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropUnique('leads_team_import_key_unique');
            $table->dropColumn('import_key');
        });
    }
};
