@props([
    'thumbnail',
    'duration',
    'title',
    'channel',
    'meta',
    'video' => null,
])

@if($video)
<a href="{{ route('video.show', $video->id) }}" class="block group h-full touch-manipulation">
@endif
    <div class="glass-card rounded-xl overflow-hidden shadow-lg flex flex-col h-full w-full min-w-0 transition-all duration-300 hover:scale-105 hover:shadow-2xl group">
        <div class="relative w-full h-[140px] sm:h-[160px] md:h-[180px] bg-gradient-to-br from-[#222] to-[#181818] flex items-center justify-center overflow-hidden">
            <img src="{{ $thumbnail }}" alt="{{ $title }}" class="w-full h-full object-cover object-center group-hover:scale-110 transition-transform duration-500 ease-out">
            @if($duration)
                <span class="absolute bottom-2 right-2 glass-button text-white text-xs px-2 py-0.5 rounded-md backdrop-blur-sm">{{ $duration }}</span>
            @endif
            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            <!-- Play button overlay -->
            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <div class="glass-button rounded-full p-3 text-white transform scale-0 group-hover:scale-100 transition-transform duration-300 delay-100">
                    <i class="fa-solid fa-play text-xl"></i>
                </div>
            </div>
        </div>
        <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between bg-gradient-to-b from-transparent to-[rgba(0,0,0,0.1)]">
            <div class="font-semibold text-sm sm:text-base leading-tight mb-2 line-clamp-2 text-white group-hover:text-blue-300 transition-colors duration-300" title="{{ $title }}">{{ $title }}</div>
            <div class="space-y-1">
                <div class="text-[#aaa] text-xs sm:text-sm truncate hover:text-white transition-colors duration-300">{{ $channel }}</div>
                <div class="text-[#888] text-xs sm:text-sm flex items-center gap-2">
                    @if($video)
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-eye text-xs"></i>
                            {{ $video->formatted_views }} views
                        </span>
                        <span class="text-[#666]">•</span>
                    @endif
                    <span class="flex items-center gap-1">
                        <i class="fa-regular fa-clock text-xs"></i>
                        {{ $meta }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@if($video)
</a>
@endif
