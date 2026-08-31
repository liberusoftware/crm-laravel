<?php

declare(strict_types=1);

namespace Tests\Feature\LearningAndCourses;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\LearningAndCourses\Actions\CreateCourse;
use Liberu\CRM\LearningAndCourses\Actions\EnrollLearner;
use Liberu\CRM\LearningAndCourses\Actions\RecordLearningProgress;
use Tests\TestCase;

final class LearningAndCoursesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrollment_progress_and_certificate_are_team_scoped(): void
    {
        $owner = User::factory()->create();
        $team = Team::factory()->create(['user_id' => $owner->id]);
        $other = Team::factory()->create();
        $course = app(CreateCourse::class)->execute($team->id, $owner->id, ['slug' => 'onboarding', 'name' => 'Onboarding', 'status' => 'published']);
        $enrollment = app(EnrollLearner::class)->execute($team->id, $owner->id, $course, ['member_id' => $owner->id, 'entitlement_reference' => 'contract-1']);
        app(RecordLearningProgress::class)->execute($team->id, $owner->id, $enrollment, ['kind' => 'progress', 'status' => 'recorded', 'score' => 80]);
        app(RecordLearningProgress::class)->execute($team->id, $owner->id, $enrollment, ['kind' => 'certificate', 'status' => 'issued', 'certificate_reference' => 'CERT-1']);
        $this->assertDatabaseHas('crm_learning_enrollments', ['team_id' => $team->id, 'status' => 'completed', 'progress' => '80.00']);
        $this->assertDatabaseHas('crm_learning_records', ['team_id' => $team->id, 'certificate_reference' => 'CERT-1']);
        $this->assertDatabaseMissing('crm_learning_courses', ['team_id' => $other->id, 'slug' => 'onboarding']);
    }
}
