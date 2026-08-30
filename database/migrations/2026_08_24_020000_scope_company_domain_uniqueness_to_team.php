<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique(['domain']);
            $table->unique(['team_id', 'domain'], 'companies_team_domain_unique');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique('companies_team_domain_unique');
            $table->unique('domain');
        });
    }
};
