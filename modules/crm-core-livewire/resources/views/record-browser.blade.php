<div>
    <label for="crm-core-search">Search records</label>
    <input id="crm-core-search" type="search" wire:model.live.debounce.300ms="search">
    <ul>
        @forelse ($records as $record)
            <li wire:key="crm-core-record-{{ $record->getKey() }}">{{ $record->name }}</li>
        @empty
            <li>No records found.</li>
        @endforelse
    </ul>
    {{ $records->links() }}
</div>
