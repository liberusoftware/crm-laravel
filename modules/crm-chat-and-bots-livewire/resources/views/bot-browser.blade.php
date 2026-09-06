<div>
    <input type="search" wire:model.live="search" placeholder="Search chat bots" class="rounded border-gray-300">
    <div class="mt-4 divide-y">@forelse ($bots as $bot)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $bot->name }}</span><span>{{ $bot->status }}</span></div><p>Chat bot</p></article>@empty<p class="py-4">No chat bots found.</p>@endforelse</div>
    {{ $bots->links() }}
</div>
