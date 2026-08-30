<form wire:submit="save">
    <input wire:model="subjectType" type="text" required placeholder="Subject type">
    <input wire:model="subjectId" type="number" min="1" required placeholder="Subject id">
    <input wire:model="fitScore" type="number" min="0" max="100" required placeholder="Fit score">
    <input wire:model="engagementScore" type="number" min="0" max="100" required placeholder="Engagement score">
    <button type="submit">Save qualification</button>
</form>
