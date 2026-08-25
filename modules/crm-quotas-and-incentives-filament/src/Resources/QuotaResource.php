<?php

declare(strict_types=1);

namespace Liberu\CRM\QuotasAndIncentives\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\QuotasAndIncentives\Filament\Resources\QuotaResource\Pages\ListQuotas;
use Liberu\CRM\QuotasAndIncentives\Models\Quota;

final class QuotaResource extends Resource
{
    protected static ?string $model = Quota::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('user_id')->numeric(), TextInput::make('territory'), DatePicker::make('period_start')->required(), DatePicker::make('period_end')->required(), TextInput::make('target')->numeric()->required(), TextInput::make('currency')->length(3)->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('user_id'), TextColumn::make('territory'), TextColumn::make('period_start')->date(), TextColumn::make('period_end')->date(), TextColumn::make('target'), TextColumn::make('attained'), TextColumn::make('status')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListQuotas::route('/')];
    }
}
