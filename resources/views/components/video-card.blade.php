@props([
    'thumbnail',
    'duration',
    'title',
    'channel',
    'meta',
    'video' => null,
])

@if($video)
<a href="{{ route('video.show', $video->id) }}" class="block group h-full">
@endif
    <div class="bg-[#181818] rounded-xl overflow-hidden shadow-lg flex flex-col h-full w-full min-w-0">
        <div class="relative w-full h-[180px] bg-[#222] flex items-center justify-center">
            <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
            @if($duration)
                <span class="absolute bottom-2 right-2 bg-black/80 text-white text-xs px-2 py-0.5 rounded">{{ $duration }}</span>
            @endif
        </div>
        <div class="p-4 flex-1 flex flex-col justify-between">
            <div class="font-semibold text-base leading-tight mb-1 truncate" title="{{ $title }}">{{ $title }}</div>
            <div class="text-[#aaa] text-xs truncate mt-1">{{ $channel }}</div>
            <div class="text-[#aaa] text-sm flex items-center gap-2">
                <span>{{ $video->formatted_views }} views</span>
                <span>• {{ $meta }}</span>
            </div>
        </div>
    </div>
@if($video)
</a>
@endif
