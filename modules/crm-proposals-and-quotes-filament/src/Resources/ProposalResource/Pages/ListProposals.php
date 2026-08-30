<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource;

final class ListProposals extends ListRecords
{
    protected static string $resource = ProposalResource::class;
}
