<div>
    <form wire:submit="save">
        <input wire:model="name" type="text" aria-label="Quote name">
        <select wire:model="currency" aria-label="Currency"><option value="USD">USD</option><option value="EUR">EUR</option><option value="GBP">GBP</option></select>
        @foreach ($lines as $index => $line)
            <fieldset wire:key="cpq-line-{{ $index }}">
                <input wire:model="lines.{{ $index }}.description" type="text" aria-label="Line description">
                <input wire:model="lines.{{ $index }}.unit_price" type="number" step="0.01" aria-label="Unit price">
                <input wire:model="lines.{{ $index }}.quantity" type="number" step="0.01" aria-label="Quantity">
                <input wire:model="lines.{{ $index }}.discount" type="number" step="0.01" aria-label="Discount">
                <button type="button" wire:click="removeLine({{ $index }})">Remove</button>
            </fieldset>
        @endforeach
        <button type="button" wire:click="addLine">Add line</button>
        <button type="submit">Price quote</button>
    </form>
</div>
