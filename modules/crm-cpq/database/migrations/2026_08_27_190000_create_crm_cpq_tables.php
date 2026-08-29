<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_cpq_quotes', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('owner_id');
            $t->string('name');
            $t->string('status')->default('draft');
            $t->string('currency', 3)->default('USD');
            $t->json('configuration');
            $t->json('lines');
            $t->decimal('subtotal', 15, 2)->default(0);
            $t->decimal('discount', 15, 2)->default(0);
            $t->decimal('total', 15, 2)->default(0);
            $t->decimal('margin', 5, 2)->nullable();
            $t->timestamps();
        });
        Schema::create('crm_cpq_approvals', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id')->index();
            $t->unsignedBigInteger('quote_id');
            $t->unsignedBigInteger('actor_id');
            $t->string('status')->default('pending');
            $t->text('reason')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_cpq_approvals');
        Schema::dropIfExists('crm_cpq_quotes');
    }
};
