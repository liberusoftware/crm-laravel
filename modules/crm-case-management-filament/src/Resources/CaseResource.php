<?php

declare(strict_types=1);

namespace Liberu\CRM\CaseManagementFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\CaseManagement\Models\CaseRecord;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages\CreateCase;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages\EditCase;
use Liberu\CRM\CaseManagementFilament\Resources\CaseResource\Pages\ListCases;

final class CaseResource extends Resource
{
    protected static ?string $model = CaseRecord::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) auth()->user()?->getAttribute('current_team_id'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('case_key')->required()->maxLength(120), TextInput::make('subject')->required()->maxLength(255), Select::make('type')->options(['support' => 'Support', 'technical' => 'Technical', 'billing' => 'Billing'])->required(), Select::make('priority')->options(['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'])->required(), Textarea::make('description')->columnSpanFull()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('case_key')->searchable(), TextColumn::make('subject')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('priority')->badge(), TextColumn::make('escalation_level'), TextColumn::make('updated_at')->dateTime()->sortable()])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListCases::route('/'), 'create' => CreateCase::route('/create'), 'edit' => EditCase::route('/{record}/edit')];
    }
}
