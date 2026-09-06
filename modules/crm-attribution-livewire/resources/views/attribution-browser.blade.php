<section aria-label="{{ __('Attribution') }}">
    <div>
        <label for="attribution-visitor-key">{{ __('Visitor key') }}</label>
        <input id="attribution-visitor-key" type="search" wire:model.live.debounce.300ms="visitorKey">
        <select wire:model.live="view" aria-label="{{ __('Attribution view') }}">
            <option value="touchpoints">{{ __('Touchpoints') }}</option>
            <option value="conversions">{{ __('Conversions') }}</option>
        </select>
    </div>
    <ul>
        @forelse ($records as $record)
            <li wire:key="attribution-{{ $record->getKey() }}"><span>{{ $record->visitor_key }}</span> <span>{{ $record->source ?? $record->conversion_key }}</span></li>
        @empty
            <li>{{ __('No attribution records found.') }}</li>
        @endforelse
    </ul>
    {{ $records->links() }}
</section>
