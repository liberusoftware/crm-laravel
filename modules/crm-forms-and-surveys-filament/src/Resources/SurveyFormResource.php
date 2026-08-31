<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\FormsAndSurveys\Models\SurveyForm;
use Liberu\CRM\FormsAndSurveysFilament\Resources\Pages\CreateSurveyForm;
use Liberu\CRM\FormsAndSurveysFilament\Resources\Pages\EditSurveyForm;
use Liberu\CRM\FormsAndSurveysFilament\Resources\Pages\ListSurveyForms;

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

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListSurveyForms::route('/'), 'create' => CreateSurveyForm::route('/create'), 'edit' => EditSurveyForm::route('/{record}/edit')];
    }
}
