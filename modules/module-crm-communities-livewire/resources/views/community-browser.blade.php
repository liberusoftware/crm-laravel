<div>
    <input type="search" wire:model.live="search" placeholder="Search community spaces" class="rounded border-gray-300">
    <div class="mt-4 divide-y">@forelse ($spaces as $space)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $space->name }}</span><span>{{ $space->status }}</span></div><p>{{ ucfirst($space->kind) }} community</p></article>@empty<p class="py-4">No community spaces found.</p>@endforelse</div>
    {{ $spaces->links() }}
</div>
