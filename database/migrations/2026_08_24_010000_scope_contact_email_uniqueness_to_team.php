<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->dropUnique(['email_hash']);
            $table->unique(['team_id', 'email_hash'], 'contacts_team_email_hash_unique');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table): void {
            $table->dropUnique('contacts_team_email_hash_unique');
            $table->unique('email_hash');
        });
    }
};
