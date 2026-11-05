<x-filament-panels::page>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div><h2 class="text-xl font-semibold">{{ \Illuminate\Support\Carbon::createFromFormat('!Y-m', $month)->format('F Y') }}</h2><p class="text-sm text-gray-500">All times in {{ $timezone }}</p></div>
        <div class="flex gap-2"><x-filament::button color="gray" wire:click="moveMonth(-1)">Previous month</x-filament::button><x-filament::button color="gray" wire:click="moveMonth(1)">Next month</x-filament::button></div>
    </div>
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-800">
        <div class="grid min-w-[840px] grid-cols-7 gap-px bg-gray-200 dark:bg-gray-800">
            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
                <div class="bg-gray-50 p-3 text-sm font-semibold dark:bg-gray-900">{{ $weekday }}</div>
            @endforeach
            @foreach($days as $day)
                <div @class(['min-h-32 bg-white p-3 dark:bg-gray-900', 'opacity-60' => $day['date']->format('Y-m') !== $month])>
                    <time datetime="{{ $day['date']->toDateString() }}" class="text-sm font-semibold">{{ $day['date']->format('j') }}</time>
                    <div class="mt-2 space-y-2">
                        @foreach($day['posts'] as $post)
                            <a class="block rounded-lg border border-gray-200 p-2 text-xs hover:border-primary-500 dark:border-gray-700" href="{{ \App\Filament\App\Resources\SocialMediaPostResource::getUrl('view', ['record' => $post], panel: 'app') }}">
                                <span class="font-medium">{{ $post->scheduled_at->copy()->timezone($timezone)->format('H:i') }} · {{ ucfirst(str_replace('_', ' ', $post->status)) }}</span>
                                <p class="mt-1">{{ \Illuminate\Support\Str::limit($post->content, 85) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
