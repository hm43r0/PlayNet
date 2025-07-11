<div class="h-full overflow-y-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-transparent">
    <!-- Main Navigation -->
    <div class="p-3 sm:p-4">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('home') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('home') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-solid fa-house mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Home</span>
                </a>
            </li>
            <li>
                <a href="{{ route('subscriptions') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('subscriptions') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-regular fa-rectangle-list mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Subscriptions</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Divider -->
    <div class="px-3 sm:px-4 mb-3">
        <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    </div>

    <!-- You Section -->
    <div class="p-3 sm:p-4">
        <div class="flex items-center justify-between mb-3 px-2">
            <span class="text-white text-sm sm:text-base font-bold">You</span>
            <i class="fa-solid fa-chevron-right text-xs text-[#aaa]"></i>
        </div>
        <ul class="space-y-1">
            <li>
                <a href="{{ route('history') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('history') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-solid fa-rotate-left mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">History</span>
                </a>
            </li>
            <li>
                <a href="{{ route('playlists') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('playlists') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-solid fa-bars-staggered mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Playlists</span>
                </a>
            </li>
            <li>
                <a href="{{ route('videos.index') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('videos.index') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-regular fa-circle-play mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Your videos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('watchlater') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('watchlater') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-regular fa-clock mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Watch Later</span>
                </a>
            </li>
            <li>
                <a href="{{ route('liked') }}" class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl text-white font-medium transition-all duration-300 touch-manipulation {{ request()->routeIs('liked') ? 'glass-light text-blue-300' : 'hover:glass-light hover:text-blue-300' }}">
                    <i class="fa-regular fa-thumbs-up mr-3 sm:mr-4 text-base sm:text-lg"></i>
                    <span class="text-sm sm:text-base">Liked videos</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Divider -->
    <div class="px-3 sm:px-4 mb-3">
        <div class="h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    </div>

    <!-- Subscriptions Section -->
    <div class="p-3 sm:p-4">
        <div class="flex items-center justify-between mb-3 px-2">
            <span class="text-white text-sm sm:text-base font-bold">Subscriptions</span>
        </div>
        @if(auth()->check())
            @php $channels = auth()->user()->subscriptions()->limit(8)->get(); @endphp
            @forelse($channels as $channel)
                <div class="flex items-center px-3 sm:px-4 py-2 sm:py-3 rounded-xl hover:glass-light transition-all duration-300 cursor-pointer group touch-manipulation">
                    <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-xs sm:text-sm font-bold text-white mr-3 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        {{ strtoupper($channel->name[0]) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-xs sm:text-sm font-medium truncate group-hover:text-blue-300 transition-colors duration-300">{{ $channel->name }}</div>
                        <div class="text-[#aaa] text-xs truncate">{{ $channel->subscribers_count ?? 0 }} subscribers</div>
                    </div>
                    <div class="w-2 h-2 rounded-full bg-red-500 ml-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
            @empty
                <div class="px-3 sm:px-4 py-3 text-center">
                    <i class="fa-solid fa-user-plus text-2xl sm:text-3xl text-[#666] mb-2 block"></i>
                    <div class="text-[#aaa] text-xs sm:text-sm">No subscriptions yet</div>
                    <a href="{{ route('home') }}" class="text-blue-400 text-xs hover:text-blue-300 transition-colors duration-300">Browse channels</a>
                </div>
            @endforelse
        @else
            <div class="px-3 sm:px-4 py-3 text-center glass-card rounded-xl">
                <i class="fa-solid fa-user text-2xl sm:text-3xl text-[#666] mb-2 block"></i>
                <div class="text-[#aaa] text-xs sm:text-sm mb-2">Sign in to see your subscriptions</div>
                <a href="{{ route('login') }}" class="glass-button rounded-full px-3 py-1 text-xs text-white hover:text-blue-300 transition-colors duration-300">
                    Sign In
                </a>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="p-3 sm:p-4 mt-auto border-t border-white/10">
        <div class="text-[#888] text-xs text-center space-y-1">
            <div>© 2025 PlayNet</div>
            <div class="flex justify-center space-x-2">
                <a href="#" class="hover:text-white transition-colors duration-300">About</a>
                <span>•</span>
                <a href="#" class="hover:text-white transition-colors duration-300">Privacy</a>
                <span>•</span>
                <a href="#" class="hover:text-white transition-colors duration-300">Terms</a>
            </div>
        </div>
    </div>
</div>
