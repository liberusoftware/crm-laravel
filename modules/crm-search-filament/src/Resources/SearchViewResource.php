<?php

declare(strict_types=1);

namespace Liberu\CRM\CrmSearchFilament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CrmSearch\Models\SearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\CreateSearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\EditSearchView;
use Liberu\CRM\CrmSearchFilament\Resources\Pages\ListSearchViews;

final class SearchViewResource extends Resource
{
    protected static ?string $model = SearchView::class;

    protected static ?string $navigationLabel = 'Saved search views';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bookmark';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            Select::make('record_type')->options([
                'contact' => 'Contacts',
                'company' => 'Companies',
                'lead' => 'Leads',
                'opportunity' => 'Opportunities',
                'deal' => 'Deals',
                'activity' => 'Activities',
                'ticket' => 'Tickets',
            ])->searchable()->required(),
            Toggle::make('shared')->label('Share with the team'),
            KeyValue::make('filters')->label('Filters')->keyLabel('Field')->valueLabel('Value')->json()->columnSpanFull()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('record_type')->label('Records')->badge(),
            TextColumn::make('shared')->formatStateUsing(static fn (mixed $state): string => (bool) $state ? 'Team' : 'Private'),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->defaultSort('updated_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSearchViews::route('/'), 'create' => CreateSearchView::route('/create'), 'edit' => EditSearchView::route('/{record}/edit')];
    }
}
