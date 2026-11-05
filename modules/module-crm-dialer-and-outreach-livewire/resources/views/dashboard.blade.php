<section aria-labelledby="dialer-heading" wire:poll.15s>
    <h2 id="dialer-heading">Dialer and outreach</h2>
    <ul>
        @forelse($lists as $list)
            <li wire:key="dialer-list-{{ $list->id }}">
                <span>{{ $list->name }} — {{ $list->mode }} — {{ $list->status }}</span>
                @if($list->status === 'active')
                    <button type="button" wire:click="setStatus({{ $list->id }}, 'paused')" wire:loading.attr="disabled">Pause {{ $list->name }}</button>
                @else
                    <button type="button" wire:click="setStatus({{ $list->id }}, 'active')" wire:loading.attr="disabled">Start {{ $list->name }}</button>
                @endif
            </li>
        @empty
            <li>No dialer lists.</li>
        @endforelse
    </ul>
</section>
