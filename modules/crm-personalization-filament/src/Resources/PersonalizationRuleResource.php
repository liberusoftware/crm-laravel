<?php

declare(strict_types=1);

namespace Liberu\CRM\Personalization\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Personalization\Filament\Resources\PersonalizationRuleResource\Pages\ListPersonalizationRules;
use Liberu\CRM\Personalization\Models\PersonalizationRule;

final class PersonalizationRuleResource extends Resource
{
    protected static ?string $model = PersonalizationRule::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), Select::make('kind')->options(['content' => 'Content', 'offer' => 'Offer', 'channel' => 'Channel', 'send_time' => 'Send time', 'locale' => 'Locale', 'lifecycle' => 'Lifecycle'])->required(), Textarea::make('conditions')->required(), Textarea::make('variants')->required(), TextInput::make('holdout_percent')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('kind')->badge(), TextColumn::make('status')->badge(), TextColumn::make('holdout_percent'), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListPersonalizationRules::route('/')];
    }
}
