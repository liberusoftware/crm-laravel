<?php

declare(strict_types=1);

namespace Liberu\CRM\ConsentAndPreferences\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages\CreateConsentRecord;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages\EditConsentRecord;
use Liberu\CRM\ConsentAndPreferences\Filament\Resources\ConsentRecordResource\Pages\ListConsentRecords;
use Liberu\CRM\ConsentAndPreferences\Models\ConsentRecord;

final class ConsentRecordResource extends Resource
{
    protected static ?string $model = ConsentRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('subject_type')->required()->maxLength(120), TextInput::make('subject_id')->numeric()->required(), TextInput::make('channel')->required()->maxLength(40), TextInput::make('topic')->default('general')->required(), Select::make('lawful_basis')->options(['consent' => 'Consent', 'contract' => 'Contract', 'legal_obligation' => 'Legal obligation', 'vital_interests' => 'Vital interests', 'public_task' => 'Public task', 'legitimate_interest' => 'Legitimate interest'])->required(), TextInput::make('source')->required()->maxLength(120), DateTimePicker::make('expires_at')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('channel')->badge(), TextColumn::make('topic'), TextColumn::make('lawful_basis')->badge(), TextColumn::make('status')->badge(), TextColumn::make('expires_at')->dateTime(), TextColumn::make('created_at')->dateTime()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListConsentRecords::route('/'), 'create' => CreateConsentRecord::route('/create'), 'edit' => EditConsentRecord::route('/{record}/edit')];
    }
}
