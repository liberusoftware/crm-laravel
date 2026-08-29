<?php

declare(strict_types=1);

namespace Liberu\CRM\ReputationManagement\Filament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource\Pages\CreateReview;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource\Pages\EditReview;
use Liberu\CRM\ReputationManagement\Filament\Resources\ReputationResource\Pages\ListReviews;
use Liberu\CRM\ReputationManagement\Models\ReputationReview;

final class ReputationResource extends Resource
{
    protected static ?string $model = ReputationReview::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('connection_id')->numeric(), TextInput::make('customer_id')->numeric(), Select::make('rating')->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])->required(), Select::make('sentiment')->options(['positive' => 'Positive', 'neutral' => 'Neutral', 'negative' => 'Negative'])->required(), Textarea::make('content'), Textarea::make('response')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('rating')->badge(), TextColumn::make('sentiment')->badge(), TextColumn::make('status')->badge(), TextColumn::make('content')->limit(60), TextColumn::make('reviewed_at')->dateTime()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $id = auth()->user()?->current_team_id;
        abort_unless($id !== null, 403);

        return parent::getEloquentQuery()->where('team_id', $id);
    }

    public static function getPages(): array
    {
        return ['index' => ListReviews::route('/'), 'create' => CreateReview::route('/create'), 'edit' => EditReview::route('/{record}/edit')];
    }
}
