<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\CreateEmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\EditEmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\ListEmailMessages;

final class EmailMessageResource extends Resource
{
    protected static ?string $model = EmailMessage::class;

    protected static ?string $navigationLabel = 'Email productivity';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmailMessages::route('/'), 'create' => CreateEmailMessage::route('/create'), 'edit' => EditEmailMessage::route('/{record}/edit')];
    }
}
