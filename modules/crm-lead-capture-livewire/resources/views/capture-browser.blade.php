<div>
    <input wire:model.live="search" type="search" placeholder="Search captured leads">
    <select wire:model.live="kind"><option value="">All channels</option><option value="manual">Manual</option><option value="import">Import</option><option value="api">API</option><option value="form">Form</option><option value="survey">Survey</option><option value="chat">Chat</option><option value="call">Call</option><option value="advertisement">Advertisement</option><option value="event">Event</option><option value="referral">Referral</option></select>
    <select wire:model.live="status"><option value="">All statuses</option><option value="received">Received</option><option value="processing">Processing</option><option value="converted">Converted</option><option value="rejected">Rejected</option><option value="failed">Failed</option></select>
    @foreach ($captures as $capture)<article wire:key="capture-{{ $capture->id }}"><strong>{{ $capture->name ?: $capture->email ?: $capture->phone }}</strong><span>{{ $capture->kind }} · {{ $capture->status }}</span><small>{{ $capture->source }}</small></article>@endforeach
    {{ $captures->links() }}
</div>
