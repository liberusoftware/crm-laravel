<div>
    <form wire:submit="save" class="space-y-4">
        <select wire:model="kind" aria-label="Record type">
            <option value="">Choose a capability record</option>
            @foreach ($kinds as $value)
                <option value="{{ $value }}">{{ str($value)->replace('_', ' ')->title() }}</option>
            @endforeach
        </select>
        <input wire:model="name" type="text" aria-label="Name" placeholder="Name">
        <select wire:model="status" aria-label="Status">
            @foreach (\Liberu\CRM\AccountBasedMarketing\Models\AccountBasedMarketingRecord::STATUSES as $value)
                <option value="{{ $value }}">{{ str($value)->title() }}</option>
            @endforeach
        </select>
        <button type="submit">Save</button>
    </form>
    <ul>
        @foreach ($records as $record)
            <li wire:key="abm-record-{{ $record->id }}">{{ $record->name }} — {{ $record->status }}</li>
        @endforeach
    </ul>
</div>
