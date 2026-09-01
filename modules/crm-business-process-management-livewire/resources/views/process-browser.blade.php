<div>
    <div class="flex gap-3"><input type="search" wire:model.live="search" placeholder="Search processes" class="rounded border-gray-300"><select wire:model.live="status" class="rounded border-gray-300"><option value="">All statuses</option><option value="draft">Draft</option><option value="active">Active</option></select></div>
    <div class="mt-4 divide-y">@forelse ($processes as $process)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $process->name }}</span><span>{{ $process->status }}</span></div><p>{{ $process->key }} · v{{ $process->version }}</p></article>@empty<p class="py-4">No processes found.</p>@endforelse</div>
    {{ $processes->links() }}
</div>
