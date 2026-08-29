<?php

declare(strict_types=1);

namespace Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\ProposalsAndQuotes\Actions\UpdateProposal;
use Liberu\CRM\ProposalsAndQuotes\Filament\Resources\ProposalResource;
use Liberu\CRM\ProposalsAndQuotes\Models\Proposal;

final class EditProposal extends EditRecord
{
    protected static string $resource = ProposalResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort_unless($record instanceof Proposal, 404);
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (int) $record->team_id === (int) $teamId, 403);

        return app(UpdateProposal::class)->execute((int) $teamId, auth()->id(), $record->id, $data);
    }
}
