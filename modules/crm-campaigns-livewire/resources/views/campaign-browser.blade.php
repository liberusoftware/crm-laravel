<div>
    <input type="search" wire:model.live="search" placeholder="Search campaigns" class="rounded border-gray-300">
    <div class="mt-4 divide-y">@forelse ($campaigns as $campaign)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $campaign->name }}</span><span>{{ $campaign->status }}</span></div><p>Budget: {{ number_format((float) $campaign->budget, 2) }}</p></article>@empty<p class="py-4">No campaigns found.</p>@endforelse</div>
    {{ $campaigns->links() }}
</div>
