<?php

declare(strict_types=1);

namespace Liberu\CRM\ChatAndBotsFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ChatAndBotsFilament\Resources\ChatBotResource;

final class ChatAndBotsFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-chat-and-bots';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ChatBotResource::class]);
    }

    public function boot(Panel $panel): void {}
}
