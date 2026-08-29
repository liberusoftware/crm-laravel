<?php

declare(strict_types=1);

namespace Liberu\CRM\PredictiveModels\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages\CreatePrediction;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages\EditPrediction;
use Liberu\CRM\PredictiveModels\Filament\Resources\PredictionResource\Pages\ListPredictions;
use Liberu\CRM\PredictiveModels\Models\Prediction;

final class PredictionResource extends Resource
{
    protected static ?string $model = Prediction::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('model_id')->numeric()->required(), TextInput::make('subject_type')->required(), TextInput::make('subject_id')->numeric()->required(), Select::make('kind')->options(['scoring' => 'Scoring', 'churn' => 'Churn', 'next_action' => 'Next action', 'next_product' => 'Next product', 'forecast' => 'Forecast', 'routing' => 'Routing'])->required(), TextInput::make('score')->numeric(), TextInput::make('label'), Textarea::make('explanation')->json()->required(), Textarea::make('features')->json()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('kind')->badge(), TextColumn::make('subject_type'), TextColumn::make('subject_id'), TextColumn::make('score'), TextColumn::make('label'), TextColumn::make('predicted_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListPredictions::route('/'), 'create' => CreatePrediction::route('/create'), 'edit' => EditPrediction::route('/{record}/edit')];
    }
}
