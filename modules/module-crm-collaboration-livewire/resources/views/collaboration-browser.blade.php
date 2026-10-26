<div>
    <div class="flex gap-3"><input type="search" wire:model.live="search" placeholder="Search collaboration work" class="rounded border-gray-300"><input type="text" wire:model.live="queue" placeholder="Queue" class="rounded border-gray-300"></div>
    <div class="mt-4 divide-y">@forelse ($work as $item)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $item->subject_key }}</span><span>{{ $item->status }}</span></div><p>{{ $item->assignee_key ?? 'Unassigned' }}</p></article>@empty<p class="py-4">No collaboration work found.</p>@endforelse</div>
    {{ $work->links() }}
</div>
