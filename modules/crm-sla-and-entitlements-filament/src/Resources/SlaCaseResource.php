<?php

declare(strict_types=1);

namespace Liberu\CRM\SlaAndEntitlements\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\SlaAndEntitlements\Filament\Resources\SlaCaseResource\Pages\ListSlaCases;
use Liberu\CRM\SlaAndEntitlements\Models\SlaCase;

final class SlaCaseResource extends Resource
{
    protected static ?string $model = SlaCase::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('response_due_at')->dateTime(), TextColumn::make('resolution_due_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListSlaCases::route('/')];
    }
}
