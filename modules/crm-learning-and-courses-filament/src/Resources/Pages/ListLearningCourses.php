<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesFilament\Resources\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\CRM\LearningAndCoursesFilament\Resources\LearningCourseResource;

final class ListLearningCourses extends ListRecords
{
    protected static string $resource = LearningCourseResource::class;
}
