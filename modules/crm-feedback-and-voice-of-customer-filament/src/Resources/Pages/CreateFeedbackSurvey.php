<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\FeedbackSurveyResource;

final class CreateFeedbackSurvey extends CreateRecord
{
    protected static string $resource = FeedbackSurveyResource::class;
}
