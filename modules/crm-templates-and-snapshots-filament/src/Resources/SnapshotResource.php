<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages\ListSnapshots;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;

final class SnapshotResource extends Resource
{
    protected static ?string $model = SnapshotBundle::class;

    protected static ?string $slug = 'snapshot-bundles';

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required(), TextInput::make('status')->required(), TextInput::make('payload')->required()]);
    }

    public static function table(Table $t): Table
    {
        return $t->columns([TextColumn::make('name'), TextColumn::make('version'), TextColumn::make('status'), TextColumn::make('checksum')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function mutateFormDataBeforeCreate(array $d): array
    {
        $d['team_id'] = (int) auth()->user()->current_team_id;
        $d['created_by'] = (int) auth()->id();
        $d['payload'] = is_string($d['payload']) ? (json_decode($d['payload'], true) ?? []) : $d['payload'];
        $d['checksum'] = hash('sha256', (string) json_encode($d['payload']));

        return $d;
    }

    public static function getPages(): array
    {
        return ['index' => ListSnapshots::route('/')];
    }
}
