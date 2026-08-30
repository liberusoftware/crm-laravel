<div>
    <input wire:model.live="search" type="search" placeholder="Search activities">
    <select wire:model.live="kind"><option value="">All types</option><option value="task">Tasks</option><option value="call">Calls</option><option value="meeting">Meetings</option><option value="email">Emails</option></select>
    <select wire:model.live="status"><option value="">All statuses</option><option value="planned">Planned</option><option value="in_progress">In progress</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select>
    @foreach ($activities as $activity)<article wire:key="activity-{{ $activity->id }}"><strong>{{ $activity->title }}</strong><span>{{ $activity->kind }} · {{ $activity->status }}</span><time>{{ $activity->due_at?->toDateTimeString() }}</time></article>@endforeach
    {{ $activities->links() }}
</div>
