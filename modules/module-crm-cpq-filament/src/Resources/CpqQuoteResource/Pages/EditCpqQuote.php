<?php

declare(strict_types=1);

namespace Liberu\CRM\CPQFilament\Resources\CpqQuoteResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\CRM\CPQFilament\Resources\CpqQuoteResource;

final class EditCpqQuote extends EditRecord
{
    protected static string $resource = CpqQuoteResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        abort(422, 'CPQ quotes are repriced by creating a new draft.');
    }
}
