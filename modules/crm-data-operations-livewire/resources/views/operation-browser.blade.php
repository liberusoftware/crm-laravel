<div>
    <label for="crm-data-operation-search">Search operations</label>
    <input id="crm-data-operation-search" type="search" wire:model.live.debounce.300ms="search">
    <label for="crm-data-operation-status">Status</label>
    <select id="crm-data-operation-status" wire:model.live="status"><option value="">All</option><option value="draft">Draft</option><option value="queued">Queued</option><option value="running">Running</option><option value="completed">Completed</option><option value="failed">Failed</option><option value="partial">Partial</option></select>
    <ul>@forelse ($operations as $operation)<li wire:key="crm-data-operation-{{ $operation->getKey() }}">{{ $operation->kind }}: {{ $operation->status }} ({{ $operation->processed_rows }}/{{ $operation->total_rows }})</li>@empty<li>No operations found.</li>@endforelse</ul>
    {{ $operations->links() }}
</div>
