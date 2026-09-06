<section class="crm-card overflow-hidden" aria-label="Sales pipeline board">
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 p-6"><div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Sales workspace</p><h2 class="mt-1 text-2xl font-semibold text-slate-950">Pipeline at a glance</h2><p class="mt-1 text-sm text-slate-500">Move opportunities forward, spot risk, and keep forecast context close.</p></div><span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">{{ $opportunities->count() }} opportunities</span></header>
    <div class="grid gap-4 overflow-x-auto p-5 md:grid-cols-2 xl:grid-cols-4">
        @forelse($stages as $stage)
            @php($stageOpportunities = $opportunities->where('stage_id', $stage->id))
            <div class="min-w-[260px] rounded-2xl bg-slate-50 p-3" wire:key="stage-{{ $stage->id }}"><header class="flex items-center justify-between px-2 py-2"><div><h3 class="font-semibold text-slate-900">{{ $stage->name }}</h3><p class="text-xs text-slate-500">{{ $stageOpportunities->count() }} · {{ $stage->probability }}% likely</p></div><span class="grid h-7 w-7 place-items-center rounded-full bg-white text-xs font-semibold text-slate-600">{{ $stageOpportunities->count() }}</span></header><div class="space-y-3 pt-2">
                @foreach($stageOpportunities as $opportunity)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm" wire:key="opportunity-{{ $opportunity->id }}"><div class="flex items-start justify-between gap-2"><strong class="text-sm text-slate-900">{{ $opportunity->name }}</strong><span class="text-xs font-medium text-slate-500">{{ $opportunity->probability }}%</span></div><p class="mt-3 text-lg font-semibold text-slate-950">{{ number_format((float) $opportunity->value, 2) }}</p><div class="mt-3 flex flex-wrap gap-2">@foreach($stages as $nextStage) @if($nextStage->id !== $stage->id)<button type="button" wire:click="move({{ $opportunity->id }}, {{ $nextStage->id }})" wire:loading.attr="disabled" class="rounded-lg border border-slate-200 px-2 py-1 text-xs font-medium text-slate-600 hover:border-indigo-300 hover:text-indigo-700">→ {{ $nextStage->name }}</button>@endif @endforeach</div></article>
                @endforeach
            </div></div>
        @empty
            <div class="p-8 text-sm text-slate-500">Create a pipeline and stages to start managing opportunities.</div>
        @endforelse
    </div>
</section>
