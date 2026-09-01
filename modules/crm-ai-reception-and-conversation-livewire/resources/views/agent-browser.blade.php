<div>
    <div class="flex gap-3"><input type="search" wire:model.live="search" placeholder="Search agents" class="rounded border-gray-300"><select wire:model.live="status" class="rounded border-gray-300"><option value="">All statuses</option><option value="draft">Draft</option><option value="active">Active</option></select></div>
    <div class="mt-4 divide-y">@forelse ($agents as $agent)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $agent->name }}</span><span>{{ $agent->status }}</span></div><p>{{ ucfirst($agent->channel) }} agent</p></article>@empty<p class="py-4">No agents found.</p>@endforelse</div>
    {{ $agents->links() }}
</div>
