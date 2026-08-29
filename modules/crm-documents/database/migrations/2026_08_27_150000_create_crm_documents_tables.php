<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('kind')->default('file');
            $table->string('folder')->nullable();
            $table->string('storage_provider')->default('local');
            $table->string('storage_key');
            $table->string('status')->default('active');
            $table->timestamp('retention_until')->nullable();
            $table->json('access')->nullable();
            $table->timestamps();
            $table->unique(['team_id', 'storage_key']);
        });
        Schema::create('crm_document_versions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('document_id')->constrained('crm_documents');
            $table->unsignedInteger('version');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('storage_key');
            $table->string('checksum')->nullable();
            $table->timestamps();
            $table->unique(['document_id', 'version']);
        });
        Schema::create('crm_document_links', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('document_id')->constrained('crm_documents');
            $table->string('token')->unique();
            $table->string('status')->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
        Schema::create('crm_document_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('team_id');
            $table->foreignId('document_id')->constrained('crm_documents');
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('event');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_document_events');
        Schema::dropIfExists('crm_document_links');
        Schema::dropIfExists('crm_document_versions');
        Schema::dropIfExists('crm_documents');
    }
};
