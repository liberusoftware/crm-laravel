<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        foreach (['sso_connections', 'saml_connections'] as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'require_sso')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->boolean('require_sso')->default(false);
            });
        }
    }

    public function down(): void
    {
        foreach (['sso_connections', 'saml_connections'] as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'require_sso')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('require_sso');
            });
        }
    }
};
