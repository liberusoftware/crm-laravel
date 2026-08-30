<div class="space-y-6" wire:loading.class="opacity-50">
    <div class="grid grid-cols-4 gap-4">
        <div>Total visits: {{ $summary['total'] }}</div><div>Hot: {{ $summary['hot'] }}</div><div>Warm: {{ $summary['warm'] }}</div><div>Open alerts: {{ $summary['open_alerts'] }}</div>
    </div>
    <div class="flex gap-3"><input aria-label="Search visitor" type="search" wire:model.live.debounce.300ms="search"><select aria-label="Intent level" wire:model.live="intentLevel"><option value="">All intent</option><option value="hot">Hot</option><option value="warm">Warm</option><option value="cool">Cool</option></select></div>
    <div><h2>Open alerts</h2>@forelse($alerts as $alert)<div><span>{{ $alert->title }}</span><button type="button" wire:click="resolve({{ $alert->id }})">Resolve</button></div>@empty<p>No open alerts.</p>@endforelse</div>
    <div><h2>Visits</h2>@forelse($visits as $visit)<div>{{ $visit->visitor_key }} — {{ $visit->intent_level }} ({{ $visit->score }})</div>@empty<p>No visits found.</p>@endforelse{{ $visits->links() }}</div>
</div>
