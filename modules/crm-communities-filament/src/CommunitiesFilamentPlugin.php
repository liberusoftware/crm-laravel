<?php

declare(strict_types=1);

namespace Liberu\CRM\CommunitiesFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\CommunitiesFilament\Resources\CommunitySpaceResource;

final class CommunitiesFilamentPlugin implements Plugin
{
    public static function make(): self { return new self(); }
    public function getId(): string { return 'crm-communities'; }
    public function register(Panel $panel): void { $panel->resources([CommunitySpaceResource::class]); }
    public function boot(Panel $panel): void {}
}
