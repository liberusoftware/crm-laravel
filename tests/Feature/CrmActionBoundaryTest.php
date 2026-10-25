<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Liberu\CRM\Core\Actions\CreateRecord;
use Liberu\CRM\Core\Actions\MergeRecords;
use Liberu\CRM\Forecasting\Actions\AdjustForecast;
use Liberu\CRM\Forecasting\Actions\CreateCategory;
use Liberu\CRM\Forecasting\Actions\RecordForecast;

it('rejects self, cross-team and incompatible record merges without deleting records', function () {
    $create = app(CreateRecord::class);
    $target = $create->execute('contact', 1, 'Target');
    $foreign = $create->execute('contact', 2, 'Foreign');
    $different = $create->execute('organization', 1, 'Different');
    foreach ([$target, $foreign, $different] as $source) {
        try {
            app(MergeRecords::class)->execute($target, $source);
            $this->fail('An incompatible merge must fail.');
        } catch (ValidationException) {
            expect($target->fresh())->not->toBeNull();
            expect($source->fresh())->not->toBeNull();
        }
    }
});

it('rejects invalid and cross-team forecast adjustments without changing the forecast', function () {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $other = Team::factory()->create(['user_id' => $owner->id]);
    $category = app(CreateCategory::class)->execute($team->id, $owner->id, ['name' => 'Commit', 'code' => 'commit']);
    $forecast = app(RecordForecast::class)->execute($team->id, $owner->id, ['category_id' => $category->id, 'period' => '2026-Q3', 'commit' => 100]);
    foreach ([['amount' => '1.001', 'reason' => 'Too precise'], ['amount' => 1, 'reason' => '   '], ['amount' => '100000000000000', 'reason' => 'Too large']] as $input) {
        try {
            app(AdjustForecast::class)->execute($team->id, $owner->id, $forecast->id, $input);
            $this->fail('Invalid adjustment must fail.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('crm_forecast_adjustments', 0);
        }
    }
    try {
        app(AdjustForecast::class)->execute($other->id, $owner->id, $forecast->id, ['amount' => 1, 'reason' => 'Foreign']);
        $this->fail('Foreign forecast must be inaccessible.');
    } catch (ModelNotFoundException) {
        $this->assertDatabaseCount('crm_forecast_adjustments', 0);
    }
    expect((float) $forecast->fresh()->commit)->toBe(100.0);
});
