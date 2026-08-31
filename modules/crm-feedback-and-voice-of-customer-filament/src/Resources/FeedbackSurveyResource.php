<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages\CreateFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages\EditFeedbackSurvey;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages\ListFeedbackSurveys;

final class FeedbackSurveyResource extends Resource
{
    protected static ?string $model = FeedbackSurvey::class;

    protected static ?string $navigationLabel = 'Customer feedback';

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListFeedbackSurveys::route('/'), 'create' => CreateFeedbackSurvey::route('/create'), 'edit' => EditFeedbackSurvey::route('/{record}/edit')];
    }
}
