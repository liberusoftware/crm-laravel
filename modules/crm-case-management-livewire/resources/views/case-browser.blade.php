<div>
    <div class="flex gap-3">
        <input type="search" wire:model.live="search" placeholder="Search cases" class="rounded border-gray-300">
        <select wire:model.live="status" class="rounded border-gray-300">
            <option value="open">Open</option><option value="pending">Pending</option><option value="escalated">Escalated</option><option value="resolved">Resolved</option><option value="closed">Closed</option>
        </select>
    </div>
    <div class="mt-4 divide-y">
        @forelse ($cases as $case)
            <article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $case->case_key }}</span><span>{{ $case->status }}</span></div><p>{{ $case->subject }}</p></article>
        @empty
            <p class="py-4">No cases found.</p>
        @endforelse
    </div>
    {{ $cases->links() }}
</div>
