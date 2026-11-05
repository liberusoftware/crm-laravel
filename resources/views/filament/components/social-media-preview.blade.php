@php
    $content = (string) ($get('content') ?? '');
    $targets = $get('platforms') ?? [];
    $image = $get('image_path');
    $link = $get('link');
    $scheduled = $get('scheduled_at');
@endphp
<div class="space-y-4">
    @foreach($targets as $target)
        @php($platform = is_array($target) ? ($target['platform'] ?? '') : explode('::', $target, 2)[0])
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <div class="mb-3 flex items-center gap-2 font-semibold"><x-heroicon-o-globe-alt class="h-5 w-5" />{{ ucfirst($platform) }} preview</div>
            <p class="whitespace-pre-wrap text-sm">{{ $content }}</p>
            @if(is_string($image) && $image !== '')
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image) }}" alt="Post preview" class="mt-3 max-h-48 rounded-lg object-cover" />
            @endif
            @if($link)
                <p class="mt-2 break-all text-sm text-primary-600">{{ $link }}</p>
            @endif
            <p class="mt-3 text-xs text-gray-500">{{ $scheduled ? 'Planned for '.$scheduled : 'Draft — choose a publishing time after review' }}</p>
        </div>
    @endforeach
</div>
