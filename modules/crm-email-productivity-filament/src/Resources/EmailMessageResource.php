<?php

declare(strict_types=1);

namespace Liberu\CRM\EmailProductivityFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\EmailProductivity\Models\EmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\CreateEmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\EditEmailMessage;
use Liberu\CRM\EmailProductivityFilament\Resources\Pages\ListEmailMessages;

final class EmailMessageResource extends Resource
{
    protected static ?string $model = EmailMessage::class;

    protected static ?string $navigationLabel = 'Email productivity';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('direction')->options(['outbound' => 'Outbound', 'inbound' => 'Inbound'])->default('outbound')->required(),
            Select::make('status')->options(['draft' => 'Draft', 'scheduled' => 'Scheduled', 'sent' => 'Sent', 'failed' => 'Failed'])->default('draft')->required(),
            TextInput::make('to_address')->label('Recipient')->email()->required()->maxLength(255),
            TextInput::make('subject')->required()->maxLength(255),
            Textarea::make('body')->required()->rows(12)->columnSpanFull(),
            DateTimePicker::make('scheduled_at')->label('Schedule send')->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('to_address')->label('Recipient')->searchable(),
            TextColumn::make('subject')->searchable()->limit(60),
            TextColumn::make('direction')->badge(),
            TextColumn::make('status')->badge(),
            TextColumn::make('sent_at')->dateTime()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->getAttribute('current_team_id');

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListEmailMessages::route('/'), 'create' => CreateEmailMessage::route('/create'), 'edit' => EditEmailMessage::route('/{record}/edit')];
    }
}
