<ul class="space-y-1 mt-2">
    <li>
        <a href="{{ route('home') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium {{ request()->routeIs('home') ? 'bg-[#222]' : 'hover:bg-[#222]' }}">
            <i class="fa-solid fa-house mr-4 text-lg"></i>Home
        </a>
    </li>
    <li>
        <a href="{{ route('subscriptions') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium {{ request()->routeIs('subscriptions') ? 'bg-[#222]' : 'hover:bg-[#222]' }} relative">
            <i class="fa-regular fa-rectangle-list mr-4 text-lg"></i>Subscriptions
        </a>
    </li>
</ul>
<hr class="my-3 border-[#222]">
<div class="px-4 flex items-center justify-between text-[#aaa] text-xs font-semibold mb-1">
    <span>You</span>
    <i class="fa-solid fa-chevron-right text-xs text-[#aaa]"></i>
</div>
<ul class="space-y-1">
    <li><a href="{{ route('history') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium hover:bg-[#222] {{ request()->routeIs('history') ? 'bg-[#222]' : '' }}"><i class="fa-solid fa-rotate-left mr-4 text-lg"></i>History</a></li>
    <li><a href="{{ route('playlists') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium hover:bg-[#222] {{ request()->routeIs('playlists') ? 'bg-[#222]' : '' }}"><i class="fa-solid fa-bars-staggered mr-4 text-lg"></i>Playlists</a></li>
    <li><a href="{{ route('videos.index') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium hover:bg-[#222] {{ request()->routeIs('videos.index') ? 'bg-[#222]' : '' }}"><i class="fa-regular fa-circle-play mr-4 text-lg"></i>Your videos</a></li>
    <li><a href="{{ route('watchlater') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium hover:bg-[#222] {{ request()->routeIs('watchlater') ? 'bg-[#222]' : '' }}"><i class="fa-regular fa-clock mr-4 text-lg"></i>Watch Later</a></li>
    <li><a href="{{ route('liked') }}" class="flex items-center px-4 py-2 rounded-lg text-white font-medium hover:bg-[#222] {{ request()->routeIs('liked') ? 'bg-[#222]' : '' }}"><i class="fa-regular fa-thumbs-up mr-4 text-lg"></i>Liked videos</a></li>
</ul>
<hr class="my-3 border-[#222]">
<div class="px-4 text-xs font-bold text-white mb-2">Subscriptions</div>
@if(auth()->check())
    @php $channels = auth()->user()->subscriptions()->limit(8)->get(); @endphp
    @forelse($channels as $channel)
        <div class="flex items-center px-4 py-2">
            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ urlencode($channel->name) }}" alt="avatar" class="w-6 h-6 rounded-full bg-[#222] mr-3">
            <span class="text-white text-sm flex-1 truncate">{{ $channel->name }}</span>
        </div>
    @empty
        <div class="px-4 text-[#aaa] text-xs">No subscriptions</div>
    @endforelse
@endif
