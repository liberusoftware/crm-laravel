<?php

declare(strict_types=1);

namespace Liberu\CRM\LeadCapture\Filament\Resources;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureFormResource\Pages\CreateCaptureForm;
use Liberu\CRM\LeadCapture\Filament\Resources\CaptureFormResource\Pages\ListCaptureForms;
use Liberu\CRM\LeadCapture\Models\CaptureForm;

final class CaptureFormResource extends Resource
{
    protected static ?string $model = CaptureForm::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('kind')->options(['form' => 'Form', 'survey' => 'Survey'])->required(), TextInput::make('name')->required(), TextInput::make('slug')->required(), Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required(), KeyValue::make('schema')->required(), KeyValue::make('settings')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('name')->searchable(), TextColumn::make('slug'), TextColumn::make('status')->badge(), TextColumn::make('submissions_count')]);
    }

    public static function getPages(): array
    {
        return ['index' => ListCaptureForms::route('/'), 'create' => CreateCaptureForm::route('/create')];
    }
}
