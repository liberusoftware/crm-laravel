<?php

declare(strict_types=1);

namespace Liberu\CRM\ServiceAnalytics\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ServiceAnalytics\Filament\Resources\AnalyticsSnapshotResource\Pages\ListAnalyticsSnapshots;
use Liberu\CRM\ServiceAnalytics\Models\AnalyticsSnapshot;

final class AnalyticsSnapshotResource extends Resource
{
    protected static ?string $model = AnalyticsSnapshot::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([Select::make('metric')->options(['volume' => 'Volume', 'backlog' => 'Backlog', 'deflection' => 'Deflection', 'first_response' => 'First response', 'resolution' => 'Resolution', 'reopen' => 'Reopen', 'transfer' => 'Transfer', 'sla' => 'SLA', 'satisfaction' => 'Satisfaction', 'quality' => 'Quality', 'staffing' => 'Staffing', 'cost_to_serve' => 'Cost to serve'])->required(), TextInput::make('value')->numeric()->required(), TextInput::make('period_start')->required(), TextInput::make('period_end')->required()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('metric')->badge(), TextColumn::make('value'), TextColumn::make('period_start')->dateTime(), TextColumn::make('period_end')->dateTime(), TextColumn::make('source')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAnalyticsSnapshots::route('/')];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['team_id'] = (int) auth()->user()->current_team_id;
        $data['recorded_by'] = (int) auth()->id();
        $data['dimensions'] = [];
        $data['dimensions_hash'] = hash('sha256', '[]');

        return $data;
    }
}
