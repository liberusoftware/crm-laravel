<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesFilament\Resources\Pages;

use Filament\Resources\Pages\CreateRecord;
use Liberu\CRM\LearningAndCoursesFilament\Resources\LearningCourseResource;

final class CreateLearningCourse extends CreateRecord
{
    protected static string $resource = LearningCourseResource::class;
}
