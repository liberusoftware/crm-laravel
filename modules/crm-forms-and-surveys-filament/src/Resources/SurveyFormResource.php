<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;

final class SurveyFormResource extends Resource
{
    protected static ?string $model = SurveyForm::class;

    protected static ?string $navigationLabel = 'Forms and Surveys';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
