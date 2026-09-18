<section aria-label="Sales engagement dashboard"><header><h2>{{ __('Sales Engagement') }}</h2><p>{{ __(':sequences sequences · :enrollments enrollments · :tasks tasks', ['sequences' => $sequences->count(), 'enrollments' => $enrollments->count(), 'tasks' => $tasks->count()]) }}</p></header><div class="grid gap-4 md:grid-cols-3">@foreach($enrollments as $enrollment)<article wire:key="enrollment-{{ $enrollment->id }}"><strong>{{ $enrollment->contact_id }}</strong><span>{{ $enrollment->status }}</span><small>{{ __('Step :step', ['step' => $enrollment->current_step]) }}</small></article>@endforeach</div>
    <h3>{{ __('Follow-up tasks') }}</h3>
    @error('task') <p role="alert">{{ $message }}</p> @enderror
    <div class="space-y-3">
        @forelse ($tasks as $task)
            <article wire:key="task-{{ $task->id }}" class="rounded-lg border p-4">
                <strong>{{ ucfirst($task->channel) }}</strong>
                <span>{{ $task->status }}</span>
                <p>{{ $task->payload['template'] ?? '' }}</p>
                <small>{{ __('Due: :date', ['date' => $task->due_at?->toDayDateTimeString() ?? '—']) }}</small>
                @if ($task->status === 'queued')
                    <button type="button" wire:click="completeTask({{ $task->id }})" wire:loading.attr="disabled" class="rounded border px-3 py-2">
                        {{ __('Mark completed') }}
                    </button>
                @endif
            </article>
        @empty
            <p>{{ __('No follow-up tasks yet.') }}</p>
        @endforelse
    </div>
</section>
