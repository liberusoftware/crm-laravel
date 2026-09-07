<?php

declare(strict_types=1);

namespace Liberu\CRM\DealRegistrationFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\DealRegistration\Models\DealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\CreateDealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\EditDealRegistration;
use Liberu\CRM\DealRegistrationFilament\Resources\Pages\ListDealRegistrations;

final class DealRegistrationResource extends Resource
{
    protected static ?string $model = DealRegistration::class;

    protected static ?string $navigationLabel = 'Deal registration';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('external_key')->label('External deal key')->required()->maxLength(120),
            TextInput::make('company')->required()->maxLength(180),
            TextInput::make('contact_email')->email()->required()->maxLength(255),
            TextInput::make('partner_id')->numeric()->label('Partner ID'),
            TextInput::make('territory')->maxLength(80),
            Select::make('status')->options(['pending' => 'Pending review', 'approved' => 'Approved', 'protected' => 'Protected', 'rejected' => 'Rejected', 'expired' => 'Expired', 'converted' => 'Converted'])->default('pending')->required(),
            Textarea::make('description')->rows(6)->columnSpanFull(),
            DateTimePicker::make('protection_until'),
            KeyValue::make('attribution')->json()->columnSpanFull(),
            KeyValue::make('collaborators')->json()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('external_key')->label('Deal key')->searchable()->sortable(),
            TextColumn::make('company')->searchable()->sortable(),
            TextColumn::make('contact_email')->searchable(),
            TextColumn::make('territory')->badge(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('protection_until')->dateTime()->sortable(),
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
        return ['index' => ListDealRegistrations::route('/'), 'create' => CreateDealRegistration::route('/create'), 'edit' => EditDealRegistration::route('/{record}/edit')];
    }
}
