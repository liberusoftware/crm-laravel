<section aria-label="{{ __('Agency accounts') }}">
    <label for="agency-account-search">{{ __('Search accounts') }}</label>
    <input id="agency-account-search" type="search" wire:model.live.debounce.300ms="search">
    <select wire:model.live="status" aria-label="{{ __('Status') }}">
        <option value="">{{ __('All statuses') }}</option>
        <option value="active">{{ __('Active') }}</option>
        <option value="suspended">{{ __('Suspended') }}</option>
        <option value="archived">{{ __('Archived') }}</option>
    </select>
    <ul>
        @forelse ($accounts as $account)
            <li wire:key="agency-account-{{ $account->getKey() }}"><span>{{ $account->name }}</span> <span>{{ $account->status }}</span></li>
        @empty
            <li>{{ __('No agency accounts found.') }}</li>
        @endforelse
    </ul>
    {{ $accounts->links() }}
</section>
