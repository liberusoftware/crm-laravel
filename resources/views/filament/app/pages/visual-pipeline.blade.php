<x-filament-panels::page>
    @if (! $pipeline)
        <x-filament::callout icon="heroicon-o-information-circle" color="gray">
            <x-slot name="heading">
                No pipeline available
            </x-slot>
            Create a pipeline with stages to use the visual board. At least one active pipeline is preferred.
        </x-filament::callout>
    @else
        <div class="overflow-x-auto">
            <div class="flex gap-4 min-w-max pb-4">
                @foreach ($stages as $stage)
                    <div wire:key="stage-{{ $stage->id }}" class="w-72 shrink-0 rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $stage->name }}
                            </h3>
                            <span class="rounded-full bg-white px-2 py-1 text-xs text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                {{ $deals->get($stage->id, collect())->count() }}
                            </span>
                        </div>

                        <div
                            class="space-y-2 min-h-24"
                            data-stage-id="{{ $stage->id }}"
                            x-data
                            x-init="
                                new Sortable($el, {
                                    group: 'deals',
                                    animation: 150,
                                    onEnd(evt) {
                                        $wire.updateDealStage(
                                            parseInt(evt.item.dataset.dealId, 10),
                                            parseInt(evt.to.dataset.stageId, 10)
                                        );
                                    },
                                });
                            "
                        >
                            @forelse ($deals->get($stage->id, collect()) as $deal)
                                <div
                                    wire:key="deal-{{ $deal->id }}"
                                    class="rounded-lg bg-white p-3 shadow-sm dark:bg-gray-900"
                                    data-deal-id="{{ $deal->id }}"
                                >
                                    <h4 class="font-medium text-gray-950 dark:text-white">
                                        {{ $deal->name }}
                                    </h4>
                                    @if ($deal->value)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            ${{ number_format((float) $deal->value, 2) }}
                                        </p>
                                    @endif
                                    @if ($deal->close_date)
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                            {{ $deal->close_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <p class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-xs text-gray-500 dark:border-gray-600 dark:text-gray-400">Drop deals here</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endif
</x-filament-panels::page>
