<form wire:submit="save">
    <select wire:model="kind"><option value="manual">Manual</option><option value="import">Import</option><option value="api">API</option><option value="chat">Chat</option><option value="call">Call</option><option value="event">Event</option><option value="referral">Referral</option></select>
    <input wire:model="name" type="text" placeholder="Name"><input wire:model="email" type="email" placeholder="Email"><input wire:model="phone" type="text" placeholder="Phone"><input wire:model="source" type="text" placeholder="Source"><button type="submit">Capture lead</button>
</form>
