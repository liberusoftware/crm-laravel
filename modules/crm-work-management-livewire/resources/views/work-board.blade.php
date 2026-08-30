<section aria-label="Work management board">
    <form wire:submit="save">
        <label for="work-search">Search</label>
        <input id="work-search" wire:model.live="search" type="search">
        <label for="work-status">Status</label>
        <select id="work-status" wire:model.live="status"><option value="">All statuses</option><option value="pending">Pending</option><option value="in_progress">In progress</option><option value="blocked">Blocked</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
        <label for="work-view">View</label>
        <select id="work-view" wire:model.live="view"><option value="team">Team</option><option value="personal">Personal</option></select>
        <label for="work-title">New work item</label>
        <input id="work-title" wire:model="newTitle" type="text" maxlength="200">
        <button type="submit">Create</button>
        @error('newTitle') <span role="alert">{{ $message }}</span> @enderror
    </form>
    <div wire:loading aria-live="polite">Loading work items…</div>
    @forelse ($items as $item)
        <article wire:key="work-item-{{ $item->id }}">
            <h2>{{ $item->title }}</h2>
            <p>{{ $item->status }} · {{ $item->priority }}</p>
            @if ($item->status !== 'completed' && $item->status !== 'cancelled')<button wire:click="complete({{ $item->id }})" type="button">Complete</button>@endif
        </article>
    @empty
        <p>No work items match this view.</p>
    @endforelse
    {{ $items->links() }}
</section>
