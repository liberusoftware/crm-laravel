<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesFilament\Resources\Pages;

use Filament\Resources\Pages\EditRecord;
use Liberu\CRM\LearningAndCoursesFilament\Resources\LearningCourseResource;

final class EditLearningCourse extends EditRecord
{
    protected static string $resource = LearningCourseResource::class;
}
