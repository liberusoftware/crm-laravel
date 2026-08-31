<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\FormsAndSurveysFilament\Resources\SurveyFormResource;

final class CreateSurveyForm extends CreateRecord
{
    protected static string $resource = SurveyFormResource::class;
}
