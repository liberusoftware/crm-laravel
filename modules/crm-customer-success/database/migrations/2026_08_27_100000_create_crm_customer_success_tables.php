<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_success_customers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->string('customer_key');
            $table->string('segment')->nullable();
            $table->string('lifecycle')->default('onboarding');
            $table->unsignedTinyInteger('health_score')->default(50);
            $table->json('onboarding')->nullable();
            $table->json('success_plan')->nullable();
            $table->json('objectives')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'customer_key']);
        });
        Schema::create('crm_success_signals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('customer_id')->constrained('crm_success_customers');
            $table->string('kind');
            $table->decimal('value', 16, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('observed_at');
            $table->timestamps();
        });
        Schema::create('crm_success_risks', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('customer_id')->constrained('crm_success_customers');
            $table->string('kind');
            $table->string('severity')->default('medium');
            $table->string('status')->default('open');
            $table->text('mitigation')->nullable();
            $table->timestamps();
        });
        Schema::create('crm_success_renewals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('customer_id')->constrained('crm_success_customers');
            $table->date('renewal_date');
            $table->string('status')->default('upcoming');
            $table->decimal('value', 16, 2)->nullable();
            $table->json('attribution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_success_renewals');
        Schema::dropIfExists('crm_success_risks');
        Schema::dropIfExists('crm_success_signals');
        Schema::dropIfExists('crm_success_customers');
    }
};
