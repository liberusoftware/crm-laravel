<div>
    <label for="customer-data-model-search">Search schemas</label>
    <input id="customer-data-model-search" type="search" wire:model.live.debounce.300ms="search">
    <ul>
        @forelse ($objects as $object)
            <li wire:key="customer-data-model-object-{{ $object->getKey() }}">{{ $object->label }} ({{ $object->fields_count }} fields)</li>
        @empty
            <li>No schemas found.</li>
        @endforelse
    </ul>
    {{ $objects->links() }}
</div>
