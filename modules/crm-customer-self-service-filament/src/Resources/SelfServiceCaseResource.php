<?php

declare(strict_types=1);

namespace Liberu\CRM\CustomerSelfServiceFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceCase;
use Liberu\CRM\CustomerSelfService\Models\SelfServiceProfile;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\CreateSelfServiceCase;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\EditSelfServiceCase;
use Liberu\CRM\CustomerSelfServiceFilament\Resources\Pages\ListSelfServiceCases;

final class SelfServiceCaseResource extends Resource
{
    protected static ?string $model = SelfServiceCase::class;

    protected static ?string $navigationLabel = 'Self-service cases';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('profile_id')->label('Customer profile')->options(fn (): array => SelfServiceProfile::query()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'))->orderBy('display_name')->pluck('display_name', 'id')->all())->searchable()->required(),
            TextInput::make('subject')->required()->maxLength(255),
            Textarea::make('description')->required()->rows(8)->columnSpanFull(),
            Select::make('status')->options(['open' => 'Open', 'in_progress' => 'In progress', 'waiting' => 'Waiting on customer', 'resolved' => 'Resolved', 'closed' => 'Closed'])->default('open')->required(),
            Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->default('normal')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('subject')->searchable()->sortable(),
            TextColumn::make('profile_id')->label('Customer profile')->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('priority')->badge()->sortable(),
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
        return ['index' => ListSelfServiceCases::route('/'), 'create' => CreateSelfServiceCase::route('/create'), 'edit' => EditSelfServiceCase::route('/{record}/edit')];
    }
}
