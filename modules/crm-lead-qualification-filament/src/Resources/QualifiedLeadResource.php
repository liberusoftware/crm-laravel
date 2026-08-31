<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadQualificationFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadQualification\Models\QualifiedLead;
use Liberu\CRM\LeadQualificationFilament\Resources\Pages\CreateQualifiedLead;
use Liberu\CRM\LeadQualificationFilament\Resources\Pages\EditQualifiedLead;
use Liberu\CRM\LeadQualificationFilament\Resources\Pages\ListQualifiedLeads;

final class QualifiedLeadResource extends Resource
{
    protected static ?string $model = QualifiedLead::class;

    protected static ?string $navigationLabel = 'Lead Qualification';

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
        return ['index' => ListQualifiedLeads::route('/'), 'create' => CreateQualifiedLead::route('/create'), 'edit' => EditQualifiedLead::route('/{record}/edit')];
    }
}
