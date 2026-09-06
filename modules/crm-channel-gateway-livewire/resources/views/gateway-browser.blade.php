<div>
    <input type="search" wire:model.live="search" placeholder="Search gateway channels" class="rounded border-gray-300">
    <div class="mt-4 divide-y">@forelse ($channels as $channel)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $channel->key }}</span><span>{{ $channel->status }}</span></div><p>{{ $channel->kind }} · {{ $channel->provider }}</p></article>@empty<p class="py-4">No gateway channels found.</p>@endforelse</div>
    {{ $channels->links() }}
</div>
