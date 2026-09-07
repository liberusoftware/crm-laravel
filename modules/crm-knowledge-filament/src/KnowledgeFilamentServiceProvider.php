<?php

declare(strict_types=1);

namespace Liberu\CRM\KnowledgeFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Liberu\CRM\KnowledgeFilament\Resources\KnowledgeArticleResource;

final class KnowledgeFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(KnowledgeFilamentPlugin::class);
    }
}

final class KnowledgeFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-knowledge';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([KnowledgeArticleResource::class]);
    }

    public function boot(Panel $panel): void {}
}
