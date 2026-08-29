<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ProposalsAndQuotes\Actions\CreateProposal as CreateProposalAction;
use Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource;

final class CreateProposal extends CreateRecord
{
    protected static string $resource = ProposalResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateProposalAction::class)->execute((int) auth()->user()?->current_team_id, auth()->id(), $data);
    }
}
