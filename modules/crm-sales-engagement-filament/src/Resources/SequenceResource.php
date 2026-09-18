<?php

declare(strict_types=1);

namespace Liberu\CRM\SalesEngagement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages\CreateSequence;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages\EditSequence;
use Liberu\CRM\SalesEngagement\Filament\Resources\SequenceResource\Pages\ListSequences;
use Liberu\CRM\SalesEngagement\Models\EngagementSequence;

final class SequenceResource extends Resource
{
    protected static ?string $model = EngagementSequence::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([
            TextInput::make('name')->required(),
            Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused']),
            TextInput::make('timezone')->required(),
            Toggle::make('stop_rules.reply')->label('Stop when the contact replies')->default(true),
            Toggle::make('stop_rules.meeting')->label('Stop when the contact books a meeting')->default(true),
        ]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('timezone'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->getAttribute('current_team_id');
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListSequences::route('/'), 'create' => CreateSequence::route('/create'), 'edit' => EditSequence::route('/{record}/edit')];
    }
}
