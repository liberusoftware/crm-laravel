<div>
    <input type="search" wire:model.live="search" placeholder="Search opportunities" class="rounded border-gray-300">
    <div class="mt-4 divide-y">@forelse ($opportunities as $opportunity)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $opportunity->opportunity_key }}</span><span>{{ $opportunity->stage }}</span></div><p>{{ $opportunity->partner_key }} · {{ number_format((float) $opportunity->amount, 2) }}</p></article>@empty<p class="py-4">No opportunities found.</p>@endforelse</div>
    {{ $opportunities->links() }}
</div>
