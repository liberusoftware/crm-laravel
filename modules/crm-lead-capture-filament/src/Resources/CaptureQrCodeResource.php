<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource\Pages\CreateCaptureQrCode;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureQrCodeResource\Pages\ListCaptureQrCodes;
use Liberu\CRM\LeadCapture\Models\CaptureQrCode;

final class CaptureQrCodeResource extends Resource
{
    protected static ?string $model = CaptureQrCode::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('code')->required(), TextInput::make('destination')->url()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('code'), TextColumn::make('destination')->limit(60), TextColumn::make('status')->badge(), TextColumn::make('scan_count')]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListCaptureQrCodes::route('/'), 'create' => CreateCaptureQrCode::route('/create')];
    }
}
