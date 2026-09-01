<div>
    <div class="flex gap-3"><input type="search" wire:model.live="search" placeholder="Search clients" class="rounded border-gray-300"><select wire:model.live="status" class="rounded border-gray-300"><option value="">All statuses</option><option value="intake">Intake</option><option value="in_progress">In progress</option><option value="complete">Complete</option></select></div>
    <div class="mt-4 divide-y">@forelse ($onboardings as $onboarding)<article class="py-3"><div class="flex justify-between"><span class="font-medium">{{ $onboarding->client_key }}</span><span>{{ $onboarding->status }}</span></div><p>Health: {{ $onboarding->health }}%</p></article>@empty<p class="py-4">No onboarding records found.</p>@endforelse</div>
    {{ $onboardings->links() }}
</div>
