<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WebhookDeliveryResource\Pages\ListWebhookDeliveries;
use App\Models\WebhookDelivery;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WebhookDeliveryResource extends Resource
{
    protected static ?string $model = WebhookDelivery::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = Filament::getTenant()?->getKey() ?? auth()->user()?->getAttribute('current_team_id');

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->dateTime()->sortable(),
            TextColumn::make('webhook.name')->searchable(),
            TextColumn::make('event')->searchable(),
            IconColumn::make('success')->boolean(),
            TextColumn::make('status_code'),
            TextColumn::make('error')->wrap(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListWebhookDeliveries::route('/')];
    }
}
