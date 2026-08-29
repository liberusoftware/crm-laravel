<section aria-label="Telephony dashboard">
    <header><h2>{{ __('Telephony') }}</h2><p>{{ __(':calls calls, :numbers numbers, :queues queues', ['calls' => $calls->count(), 'numbers' => $numbers->count(), 'queues' => $queues->count()]) }}</p></header>
    <div class="grid gap-4 md:grid-cols-3">@foreach ($calls as $call)<article wire:key="telephony-call-{{ $call->id }}"><strong>{{ $call->from_number }} → {{ $call->to_number }}</strong><span>{{ $call->status }}</span><small>{{ $call->disposition ?? __('No disposition') }}</small></article>@endforeach</div>
</section>
