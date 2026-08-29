<?php

declare(strict_types=1);

namespace Liberu\CRM\Segmentation\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\Segmentation\Filament\Resources\AudienceResource\Pages\ListAudiences;
use Liberu\CRM\Segmentation\Models\Audience;

final class AudienceResource extends Resource
{
    protected static ?string $model = Audience::class;

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), Textarea::make('description'), Select::make('kind')->options(['static' => 'Static', 'dynamic' => 'Dynamic'])->required(), Select::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'paused' => 'Paused'])->required(), TextInput::make('estimated_count')->numeric()->disabled()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name')->searchable(), TextColumn::make('kind')->badge(), TextColumn::make('status')->badge(), TextColumn::make('estimated_count'), TextColumn::make('refreshed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListAudiences::route('/')];
    }
}
