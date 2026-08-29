<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_customer_data_objects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->string('key', 80);
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_standard')->default(false);
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('current_version')->default(0);
            $table->timestamps();
            $table->unique(['team_id', 'key']);
        });
        Schema::create('crm_customer_data_fields', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('object_id')->constrained('crm_customer_data_objects')->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label');
            $table->string('type', 30);
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_calculated')->default(false);
            $table->text('calculation')->nullable();
            $table->json('required_stages')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->unique(['object_id', 'key']);
        });
        Schema::create('crm_customer_data_relationships', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->foreignId('from_object_id')->constrained('crm_customer_data_objects')->cascadeOnDelete();
            $table->foreignId('to_object_id')->constrained('crm_customer_data_objects')->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label');
            $table->string('cardinality', 20);
            $table->json('config')->nullable();
            $table->timestamps();
            $table->unique(
                ['team_id', 'from_object_id', 'to_object_id', 'key'],
                'crm_customer_data_relationships_object_key_unique',
            );
        });
        Schema::create('crm_customer_data_layouts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('object_id')->constrained('crm_customer_data_objects')->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('label');
            $table->json('sections');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->unique(['object_id', 'key']);
        });
        Schema::create('crm_customer_data_schema_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('object_id')->constrained('crm_customer_data_objects')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->string('status', 30);
            $table->json('snapshot');
            $table->unsignedBigInteger('published_by')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['object_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_customer_data_schema_versions');
        Schema::dropIfExists('crm_customer_data_layouts');
        Schema::dropIfExists('crm_customer_data_relationships');
        Schema::dropIfExists('crm_customer_data_fields');
        Schema::dropIfExists('crm_customer_data_objects');
    }
};
