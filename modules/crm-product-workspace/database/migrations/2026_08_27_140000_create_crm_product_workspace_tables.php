<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_product_workspace_products', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('sku');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->boolean('eligible')->default(true);
            $table->json('price_book')->nullable();
            $table->json('metadata')->nullable();
            $table->string('sync_status')->default('local');
            $table->timestamps();
            $table->unique(['team_id', 'sku']);
        });
        Schema::create('crm_product_workspace_bundles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('name');
            $table->json('product_ids');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('crm_product_workspace_entitlements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('customer_id');
            $table->foreignId('product_id')->constrained('crm_product_workspace_products');
            $table->string('status')->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(
                ['team_id', 'customer_id', 'status'],
                'crm_product_entitlement_customer_index',
            );
        });
        Schema::create('crm_product_workspace_syncs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('provider');
            $table->string('resource');
            $table->string('status')->default('queued');
            $table->text('error')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_product_workspace_syncs');
        Schema::dropIfExists('crm_product_workspace_entitlements');
        Schema::dropIfExists('crm_product_workspace_bundles');
        Schema::dropIfExists('crm_product_workspace_products');
    }
};
