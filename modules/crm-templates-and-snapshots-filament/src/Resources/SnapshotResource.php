<?php

declare(strict_types=1);

namespace Liberu\CRM\TemplatesAndSnapshots\Filament\Resources;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages\CreateSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages\EditSnapshot;
use Liberu\CRM\TemplatesAndSnapshots\Filament\Resources\SnapshotResource\Pages\ListSnapshots;
use Liberu\CRM\TemplatesAndSnapshots\Models\SnapshotBundle;

final class SnapshotResource extends Resource
{
    protected static ?string $model = SnapshotBundle::class;

    protected static ?string $slug = 'snapshot-bundles';

    public static function form(Schema $s): Schema
    {
        return $s->components([TextInput::make('name')->required()->maxLength(255), TextInput::make('status')->required()->in(['draft', 'published']), Textarea::make('payload')->required()->json()->rows(12)]);
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

    public static function getPages(): array
    {
        return ['index' => ListSnapshots::route('/'), 'create' => CreateSnapshot::route('/create'), 'edit' => EditSnapshot::route('/{record}/edit')];
    }
}
