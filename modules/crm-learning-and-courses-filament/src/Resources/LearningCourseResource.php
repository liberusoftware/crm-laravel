<?php

declare(strict_types=1);

namespace Liberu\CRM\LearningAndCoursesFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\CRM\LearningAndCourses\Models\LearningCourse;
use Liberu\CRM\LearningAndCoursesFilament\Resources\Pages\CreateLearningCourse;
use Liberu\CRM\LearningAndCoursesFilament\Resources\Pages\EditLearningCourse;
use Liberu\CRM\LearningAndCoursesFilament\Resources\Pages\ListLearningCourses;

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

    public static function getEloquentQuery(): Builder
    {
        $teamId = (int) auth()->user()?->current_team_id;

        abort_unless($teamId > 0, 403);

        return parent::getEloquentQuery()->where('team_id', $teamId);
    }

    public static function getPages(): array
    {
        return ['index' => ListLearningCourses::route('/'), 'create' => CreateLearningCourse::route('/create'), 'edit' => EditLearningCourse::route('/{record}/edit')];
    }
}
