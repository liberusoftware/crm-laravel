<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\CPQ\Actions\PriceQuote;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource;

final class CreateCpqQuote extends CreateRecord
{
    protected static string $resource = CpqQuoteResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user !== null && (int) $user->current_team_id > 0, 403);

        return app(PriceQuote::class)->execute((int) $user->current_team_id, (int) $user->getAuthIdentifier(), $data);
    }
}
