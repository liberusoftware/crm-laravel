<div>
    <div class="flex gap-3"><input type="search" wire:model.live="search" placeholder="Search contracts" class="rounded border-gray-300"><select wire:model.live="status" class="rounded border-gray-300"><option value="">All statuses</option><option value="draft">Draft</option><option value="pending_approval">Pending approval</option><option value="approved">Approved</option><option value="active">Active</option></select></div>
    <div class="mt-4 divide-y">@forelse ($contracts as $contract)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $contract->name }}</span><span>{{ $contract->status }}</span></div><p>Version {{ $contract->version }} · Ends {{ optional($contract->ends_on)->toDateString() ?? '—' }}</p></article>@empty<p class="py-4">No contracts found.</p>@endforelse</div>
    {{ $contracts->links() }}
</div>
