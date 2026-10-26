<section aria-label="{{ __('Affiliates') }}">
    <div>
        <label for="affiliate-search">{{ __('Search affiliates') }}</label>
        <input id="affiliate-search" type="search" wire:model.live.debounce.300ms="search">
        <select wire:model.live="status" aria-label="{{ __('Status') }}">
            <option value="">{{ __('All statuses') }}</option>
            <option value="applicant">{{ __('Applicant') }}</option>
            <option value="active">{{ __('Active') }}</option>
            <option value="suspended">{{ __('Suspended') }}</option>
        </select>
    </div>
    <ul>
        @forelse ($affiliates as $affiliate)
            <li wire:key="affiliate-{{ $affiliate->getKey() }}"><span>{{ $affiliate->name }}</span> <span>{{ $affiliate->status }}</span>@if ($affiliate->status === 'applicant') <button type="button" wire:click="approve({{ $affiliate->getKey() }})">{{ __('Approve') }}</button>@endif</li>
        @empty
            <li>{{ __('No affiliates found.') }}</li>
        @endforelse
    </ul>
    {{ $affiliates->links() }}
</section>
