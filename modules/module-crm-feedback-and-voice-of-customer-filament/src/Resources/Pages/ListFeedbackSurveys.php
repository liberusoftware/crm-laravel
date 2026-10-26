<?php

declare(strict_types=1);

namespace Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\FeedbackAndVoiceOfCustomerFilament\Resources\FeedbackSurveyResource;

final class ListFeedbackSurveys extends ListRecords
{
    protected static string $resource = FeedbackSurveyResource::class;
}
