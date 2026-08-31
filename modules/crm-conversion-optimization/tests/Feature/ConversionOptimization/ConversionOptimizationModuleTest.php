<?php

declare(strict_types=1);

namespace Tests\Feature\ConversionOptimization;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Liberu\CRM\ConversionOptimization\Actions\ActivateExperiment;
use Liberu\CRM\ConversionOptimization\Actions\CreateConversionExperiment;
use Liberu\CRM\ConversionOptimization\Actions\RecordConversion;
use Liberu\CRM\ConversionOptimization\Queries\ConversionReportQuery;
use Tests\TestCase;

final class ConversionOptimizationModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_experiment_activation_conversion_and_report_are_scoped(): void
    {
        $u = User::factory()->create();
        $t = Team::factory()->create(['user_id' => $u->id]);
        $e = app(CreateConversionExperiment::class)->execute($t->id, $u->id, ['name' => 'CTA', 'variants' => ['A', 'B'], 'goals' => ['signup']]);
        $e = app(ActivateExperiment::class)->execute($t->id, $u->id, $e);
        $o = app(RecordConversion::class)->execute($t->id, $e, 'visitor-1', 'A', 'signup');
        $report = app(ConversionReportQuery::class)->report($t->id, $e);
        $this->assertSame('active', $e->status);
        $this->assertSame('A', $o->variant);
        $this->assertSame(1, $report['A']['observations']);
    }
}
