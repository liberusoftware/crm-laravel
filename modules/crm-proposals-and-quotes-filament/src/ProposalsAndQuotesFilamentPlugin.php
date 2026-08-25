<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource;

final class ProposalsAndQuotesFilamentPlugin implements Plugin
{
    public static function make(): self
    {
        return new self();
    }

    public function getId(): string
    {
        return 'crm-proposals-and-quotes';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([ProposalResource::class]);
    }

    public function boot(Panel $panel): void {}
}
