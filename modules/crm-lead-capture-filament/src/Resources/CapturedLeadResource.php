<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCaptureFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadCapture\Models\CapturedLead;
use Liberu\CRM\LeadCaptureFilament\Resources\Pages\CreateCapturedLead;
use Liberu\CRM\LeadCaptureFilament\Resources\Pages\EditCapturedLead;
use Liberu\CRM\LeadCaptureFilament\Resources\Pages\ListCapturedLeads;

final class CapturedLeadResource extends Resource
{
    protected static ?string $model = CapturedLead::class;

    protected static ?string $navigationLabel = 'Lead Inbox';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCapturedLeads::route('/'), 'create' => CreateCapturedLead::route('/create'), 'edit' => EditCapturedLead::route('/{record}/edit')];
    }
}
