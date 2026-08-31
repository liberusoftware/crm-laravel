<?php

declare(strict_types=1);

namespace Liberu\CRM\FormsAndSurveysFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\FormsAndSurveysFilament\Resources\SurveyFormResource;

final class ListSurveyForms extends ListRecords
{
    protected static string $resource = SurveyFormResource::class;
}
