<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Connect your marketing channels</x-slot>
        <x-slot name="description">Your administrator selected {{ $provider === 'zernio' ? 'Zernio' : 'standalone APIs' }}. Account permissions stay within this workspace.</x-slot>
        <form wire:submit="connect" class="flex flex-wrap items-end gap-4">
            <label class="block text-sm font-medium">
                Platform
                <select wire:model="platform" class="mt-2 block rounded-lg border-gray-300 dark:bg-gray-900">
                    @foreach($provider === 'zernio' ? \App\Services\Zernio\ZernioTenantService::PLATFORMS : \App\Services\Marketing\DirectPostPublisher::PLATFORMS as $channel)
                        <option value="{{ $channel }}">{{ ucfirst($channel) }}</option>
                    @endforeach
                </select>
            </label>
            <x-filament::button type="submit" wire:loading.attr="disabled">Connect account</x-filament::button>
            <x-filament::button color="gray" wire:click="reloadAccounts" wire:loading.attr="disabled">Refresh connections</x-filament::button>
        </form>
    </x-filament::section>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($accounts as $account)
            <x-filament::section wire:key="marketing-account-{{ $account['id'] }}">
                <x-slot name="heading">{{ $account['name'] }}</x-slot>
                <p class="text-sm">{{ ucfirst($account['platform']) }} · {{ $account['status'] }}</p>
                @if($provider === 'direct' && $account['status'] === 'Connected')
                    <x-filament::button class="mt-3" color="gray" size="sm" wire:click="disableConnection({{ (int) $account['id'] }})" wire:confirm="Disable this connection for publishing?">Disable publishing</x-filament::button>
                @endif
                @if($provider === 'direct' && $account['platform'] !== 'twitter')
                    <form wire:submit="saveDestination({{ (int) $account['id'] }})" class="mt-4 space-y-3">
                        <label class="block text-sm font-medium">Publishing destination
                            <input wire:model="destinations.{{ $account['id'] }}" class="mt-2 block w-full rounded-lg border-gray-300 dark:bg-gray-900" required maxlength="255" />
                        </label>
                        <p class="text-xs text-gray-500">Use your Facebook Page ID, Instagram professional account ID, or LinkedIn author URN. The connected account must have permission to publish there.</p>
                        <x-filament::button type="submit" size="sm">Save destination</x-filament::button>
                    </form>
                @endif
            </x-filament::section>
        @empty
            <x-filament::section><p>No accounts connected yet. Connect a channel to start publishing.</p></x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
