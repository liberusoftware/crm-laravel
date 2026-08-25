<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_orders_payments', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('reference')->unique();
            $t->string('kind');
            $t->string('status')->default('draft');
            $t->string('currency', 3)->default('USD');
            $t->decimal('amount', 14, 2)->default(0);
            $t->decimal('paid_amount', 14, 2)->default(0);
            $t->decimal('refunded_amount', 14, 2)->default(0);
            $t->string('external_reference')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'kind', 'status']);
        });
        Schema::create('crm_orders_payments_events', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('transaction_id')->constrained('crm_orders_payments')->cascadeOnDelete();
            $t->string('kind');
            $t->string('status')->default('recorded');
            $t->decimal('amount', 14, 2)->nullable();
            $t->text('notes')->nullable();
            $t->string('external_reference')->nullable();
            $t->json('payload')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'transaction_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_orders_payments_events');
        Schema::dropIfExists('crm_orders_payments');
    }
};
