<?php

declare(strict_types=1);

namespace Tests\Feature\DataOperations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\DataOperations\Actions\CreateDataOperation;
use Liberu\CRM\DataOperations\Actions\ResolveException;
use Liberu\CRM\DataOperations\Actions\TransitionDataOperation;
use Liberu\CRM\DataOperations\Models\OperationException;
use Liberu\CRM\DataOperations\Services\DataOperationService;
use Tests\TestCase;

final class DataOperationsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_operation_lifecycle_and_mapping_are_team_scoped(): void
    {
        $operation = app(CreateDataOperation::class)->execute(7, 11, ['kind' => 'import', 'source' => 'contacts.csv'], [['source_field' => 'Email', 'target_field' => 'email', 'transform' => 'lowercase']]);
        self::assertSame('draft', $operation->status);
        self::assertSame('ada@example.test', app(DataOperationService::class)->mapAndNormalize(['Email' => ' ADA@EXAMPLE.TEST '], ['Email' => ['target_field' => 'email', 'transform' => 'lowercase']])['email']);

        $transition = app(TransitionDataOperation::class);
        $transition->execute($operation, 'queued');
        $running = $transition->execute($operation->refresh(), 'running');
        self::assertNotNull($running->started_at);
    }

    public function test_invalid_transition_and_exception_resolution_are_safe(): void
    {
        $operation = app(CreateDataOperation::class)->execute(7, 11, ['kind' => 'quality']);
        $this->expectException(ValidationException::class);
        app(TransitionDataOperation::class)->execute($operation, 'completed');
    }

    public function test_duplicate_confidence_and_exception_resolution(): void
    {
        $operation = app(CreateDataOperation::class)->execute(7, 11, ['kind' => 'deduplication']);
        $exception = OperationException::query()->create(['team_id' => 7, 'operation_id' => $operation->getKey(), 'payload' => ['row' => 1], 'reason' => 'Invalid email']);
        $resolved = app(ResolveException::class)->execute($exception, 11);
        self::assertSame('resolved', $resolved->status);
        self::assertSame(1.0, app(DataOperationService::class)->duplicateConfidence(['email' => 'ada@example.test', 'name' => 'Ada'], ['email' => 'ADA@example.test', 'name' => 'Ada'], ['email', 'name']));
    }
}
