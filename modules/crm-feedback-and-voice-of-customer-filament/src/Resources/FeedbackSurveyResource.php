<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources;

use Filament\Resources\Resource;
use Liberu\CRM\FeedbackAndVoiceOfCustomer\Models\FeedbackSurvey;

final class FeedbackSurveyResource extends Resource
{
    protected static ?string $model = FeedbackSurvey::class;

    protected static ?string $navigationLabel = 'Customer feedback';

    public static function getPages(): array
    {
        return [];
    }
}
