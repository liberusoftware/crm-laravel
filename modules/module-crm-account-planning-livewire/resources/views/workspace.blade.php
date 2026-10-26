<div>
    <form wire:submit="save">
        <select wire:model="kind" aria-label="Record type"><option value="">Choose a planning record</option>@foreach ($kinds as $value)<option value="{{ $value }}">{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select>
        <input wire:model="name" type="text" aria-label="Name">
        <button type="submit">Save</button>
    </form>
    <ul>@foreach ($records as $record)<li wire:key="planning-record-{{ $record->id }}">{{ $record->name }} — {{ $record->status }}</li>@endforeach</ul>
</div>
