<form wire:submit="save">
    <select wire:model="kind"><option value="task">Task</option><option value="call">Call</option><option value="meeting">Meeting</option><option value="email">Email</option></select>
    <input wire:model="title" type="text" required><textarea wire:model="description"></textarea><input wire:model="dueAt" type="datetime-local"><button type="submit">Save activity</button>
</form>
