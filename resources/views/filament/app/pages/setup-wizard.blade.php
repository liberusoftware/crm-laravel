<x-filament-panels::page>
    @if ($saved)
        <x-filament::section icon="heroicon-o-check-circle" icon-color="success" class="mb-6">
            <x-slot name="heading">Setup saved</x-slot>
            <p class="text-sm text-gray-600 dark:text-gray-400">Your settings are saved. Account connections and publishing actions are managed separately.</p>
        </x-filament::section>
    @endif

    <form wire:submit="save">
        {{ $this->form }}
    </form>
</x-filament-panels::page>
