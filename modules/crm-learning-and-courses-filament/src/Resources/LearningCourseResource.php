<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Liberu\CRM\LearningAndCourses\Models\LearningCourse;

final class LearningCourseResource extends Resource
{
    protected static ?string $model = LearningCourse::class;

    protected static ?string $navigationLabel = 'Learning';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [];
    }
}
