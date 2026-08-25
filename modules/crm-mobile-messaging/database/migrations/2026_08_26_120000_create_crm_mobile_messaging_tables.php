<?php

declare(strict_types=1);
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('crm_mobile_messaging_contacts', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('address');
            $t->string('channel');
            $t->string('consent')->default('unknown');
            $t->timestamp('consent_at')->nullable();
            $t->string('keyword')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->unique(['team_id', 'address', 'channel']);
            $t->index(['team_id', 'consent']);
        });
        Schema::create('crm_mobile_messaging_templates', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('channel');
            $t->text('body');
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['team_id', 'name']);
        });
        Schema::create('crm_mobile_messaging_campaigns', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->string('name');
            $t->string('channel');
            $t->string('status')->default('draft');
            $t->foreignId('template_id')->nullable()->constrained('crm_mobile_messaging_templates')->nullOnDelete();
            $t->timestamp('quiet_start')->nullable();
            $t->timestamp('quiet_end')->nullable();
            $t->string('sender')->nullable();
            $t->json('metadata')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'channel']);
        });
        Schema::create('crm_mobile_messaging_messages', function (Blueprint $t): void {
            $t->id();
            $t->unsignedBigInteger('team_id');
            $t->foreignId('campaign_id')->nullable()->constrained('crm_mobile_messaging_campaigns')->nullOnDelete();
            $t->foreignId('contact_id')->constrained('crm_mobile_messaging_contacts')->cascadeOnDelete();
            $t->string('direction');
            $t->string('status')->default('queued');
            $t->text('body');
            $t->string('external_key')->nullable();
            $t->json('payload')->nullable();
            $t->timestamp('sent_at')->nullable();
            $t->timestamps();
            $t->index(['team_id', 'status', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_mobile_messaging_messages');
        Schema::dropIfExists('crm_mobile_messaging_campaigns');
        Schema::dropIfExists('crm_mobile_messaging_templates');
        Schema::dropIfExists('crm_mobile_messaging_contacts');
    }
};
