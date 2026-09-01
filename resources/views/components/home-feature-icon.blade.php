@php($paths = [
    'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    'chart' => '<path d="M3 3v18h18"/><path d="m7 16 4-5 3 3 5-7"/>',
    'message' => '<path d="M21 11.5a8.38 8.38 0 0 1-9 8.5 9.6 9.6 0 0 1-4-.8L3 21l1.8-4.3A8.2 8.2 0 0 1 3 11.5 8.38 8.38 0 0 1 12 3a8.38 8.38 0 0 1 9 8.5Z"/>',
    'check' => '<path d="M20 6 9 17l-5-5"/>',
    'support' => '<path d="M4 14a8 8 0 0 1 16 0"/><path d="M18 19c0 1.1-1.8 2-4 2h-1"/><path d="M4 14v3a2 2 0 0 0 2 2h1v-5H4Zm16 0v3a2 2 0 0 1-2 2h-1v-5h3Z"/>',
    'report' => '<path d="M4 19V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14"/><path d="M2 19h20v2H2zM8 15v-3m4 3V8m4 7v-5"/>',
    'team' => '<circle cx="9" cy="7" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 20a6 6 0 0 1 12 0M15 15a5 5 0 0 1 6 5"/>',
    'plug' => '<path d="M12 22v-5M9 8V2m6 6V2M6 8h12v3a6 6 0 0 1-12 0V8Z"/>',
]);
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $paths[$icon] ?? $paths['check'] !!}</svg>
