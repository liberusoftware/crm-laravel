<section class="crm-card overflow-hidden" aria-label="Unified helpdesk inbox">
    <header class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 p-6">
        <div><p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">Unified helpdesk</p><h2 class="mt-1 text-2xl font-semibold text-slate-950">Every conversation, one queue</h2><p class="mt-1 text-sm text-slate-500">Email, WhatsApp, social DMs, comments and reviews in the team workspace.</p></div>
        <span class="rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">{{ $conversations->total() }} open threads</span>
    </header>
    <div class="divide-y divide-slate-100">
        @forelse($conversations as $conversation)
            <article class="flex flex-wrap items-center justify-between gap-4 p-5 transition hover:bg-slate-50">
                <div class="flex min-w-0 items-center gap-4"><div class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-indigo-50 text-sm font-bold uppercase text-indigo-700">{{ substr($conversation->channel, 0, 2) }}</div><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><strong class="text-slate-900">{{ $conversation->subject ?: 'Untitled conversation' }}</strong><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ str_replace('zernio.', '', $conversation->channel) }}</span>@if($conversation->priority === 'high')<span class="rounded-full bg-rose-50 px-2 py-0.5 text-xs font-medium text-rose-700">High priority</span>@endif</div><p class="mt-1 text-sm text-slate-500">{{ $conversation->last_message_at?->diffForHumans() ?: 'Awaiting first message' }} · {{ ucfirst($conversation->status) }}</p></div></div>
                <a href="#conversation-{{ $conversation->id }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:border-indigo-300 hover:text-indigo-700">Open thread</a>
            </article>
        @empty
            <div class="p-12 text-center"><p class="font-semibold text-slate-900">Your inbox is clear</p><p class="mt-1 text-sm text-slate-500">New messages from connected channels will appear here.</p></div>
        @endforelse
    </div>
    @if($conversations->hasPages())<footer class="border-t border-slate-200 p-4">{{ $conversations->links() }}</footer>@endif
</section>
