<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\FormsAndSurveysFilament\Resources\SurveyFormResource;

final class EditSurveyForm extends EditRecord
{
    protected static string $resource = SurveyFormResource::class;
}
