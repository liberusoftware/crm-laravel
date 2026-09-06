<?php

namespace App\Filament\App\Pages;

use App\Models\Deal;
use App\Models\Pipeline;
use App\Models\Stage;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class VisualPipeline extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.app.pages.visual-pipeline';

    public ?Pipeline $pipeline = null;

    /** @var Collection<int, Stage> */
    public Collection $stages;

    /** @var Collection<int|string, Collection<int, Deal>> */
    public Collection $deals;

    public function mount(): void
    {
        $this->pipeline = Pipeline::query()
            ->where('is_active', true)
            ->with('stages')
            ->first()
            ?? Pipeline::query()->with('stages')->first();

        if ($this->pipeline === null) {
            $this->stages = collect();
            $this->deals = collect();

            return;
        }

        $this->loadPipelineData();
    }

    public function getHeading(): string
    {
        return $this->pipeline?->name ? "Pipeline: {$this->pipeline->name}" : 'Visual pipeline';
    }

    public function updateDealStage(int $dealId, int $newStageId): void
    {
        if ($this->pipeline === null) {
            return;
        }

        $stage = Stage::query()
            ->where('pipeline_id', $this->pipeline->id)
            ->find($newStageId);
        if ($stage === null) {
            throw ValidationException::withMessages(['stage' => 'The selected stage is not part of this pipeline.']);
        }

        $deal = Deal::query()
            ->where('pipeline_id', $this->pipeline->id)
            ->findOrFail($dealId);
        $deal->update(['stage_id' => $stage->id, 'stage' => $stage->name]);
        $this->loadPipelineData();
    }

    private function loadPipelineData(): void
    {
        $this->stages = $this->pipeline->stages;
        $this->deals = Deal::query()
            ->where('pipeline_id', $this->pipeline->id)
            ->get()
            ->groupBy('stage_id');
    }
}
