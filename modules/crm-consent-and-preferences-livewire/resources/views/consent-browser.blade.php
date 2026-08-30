<div>
    <label for="crm-consent-search">Search consent</label>
    <input id="crm-consent-search" type="search" wire:model.live.debounce.300ms="search">
    <ul>@forelse ($records as $record)<li wire:key="crm-consent-{{ $record->getKey() }}">{{ $record->subject_type }} #{{ $record->subject_id }} — {{ $record->channel }}/{{ $record->topic }} — {{ $record->status }}</li>@empty<li>No consent records found.</li>@endforelse</ul>
    {{ $records->links() }}
</div>
