<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\FeedbackSurveyResource;

final class EditFeedbackSurvey extends EditRecord
{
    protected static string $resource = FeedbackSurveyResource::class;
}
