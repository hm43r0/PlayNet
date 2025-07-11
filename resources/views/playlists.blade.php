@extends('layouts.app')

@section('title', 'Playlists')

@section('content')
<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-white text-responsive-xl">Your Playlists</h1>
        <button onclick="showCreatePlaylistModal()" 
                class="glass-button rounded-full px-4 sm:px-6 py-2 sm:py-3 text-white font-semibold flex items-center gap-2 text-sm sm:text-base transition touch-manipulation">
            <i class="fa-solid fa-plus"></i>
            <span>Create Playlist</span>
        </button>
    </div>

    @if(session('success'))
        <div class="glass-notification rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border border-green-500/20">
            <div class="text-green-400 text-sm sm:text-base">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-notification rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border border-red-500/20">
            <div class="text-red-400 text-sm sm:text-base">{{ session('error') }}</div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <!-- Watch Later Playlist -->
        <div class="glass-card rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl group">
            <a href="{{ route('playlist.show', $watchLater) }}" class="block touch-manipulation">
                <div class="aspect-video bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center relative">
                    @if($watchLater->first_video_thumbnail)
                        <img src="{{ asset('storage/' . $watchLater->first_video_thumbnail) }}" 
                             alt="Watch Later" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                            <i class="fa-regular fa-clock text-3xl sm:text-4xl text-white"></i>
                        </div>
                    @else
                        <i class="fa-regular fa-clock text-3xl sm:text-4xl text-white"></i>
                    @endif
                    <div class="absolute bottom-2 right-2 bg-black/70 px-2 py-1 rounded text-xs backdrop-blur-sm">
                        {{ $watchLater->video_count }} videos
                    </div>
                </div>
                <div class="p-3 sm:p-4">
                    <h3 class="font-semibold text-base sm:text-lg mb-1 text-white">Watch Later</h3>
                    <p class="text-[#aaa] text-xs sm:text-sm">Private playlist</p>
                </div>
            </a>
        </div>

        <!-- Regular Playlists -->
        @foreach($playlists as $playlist)
            <div class="glass-card rounded-xl overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl group relative">
                <a href="{{ route('playlist.show', $playlist) }}" class="block touch-manipulation">
                    <div class="aspect-video bg-[#222] flex items-center justify-center relative">
                        @if($playlist->first_video_thumbnail)
                            <img src="{{ asset('storage/' . $playlist->first_video_thumbnail) }}" 
                                 alt="{{ $playlist->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-list text-3xl sm:text-4xl text-[#666]"></i>
                        @endif
                        <div class="absolute bottom-2 right-2 bg-black/70 px-2 py-1 rounded text-xs backdrop-blur-sm">
                            {{ $playlist->video_count }} videos
                        </div>
                    </div>
                    <div class="p-3 sm:p-4">
                        <h3 class="font-semibold text-base sm:text-lg mb-1 truncate text-white">{{ $playlist->name }}</h3>
                        <p class="text-[#aaa] text-xs sm:text-sm capitalize">{{ $playlist->visibility }} playlist</p>
                        @if($playlist->description)
                            <p class="text-[#aaa] text-xs mt-1 line-clamp-2">{{ $playlist->description }}</p>
                        @endif
                    </div>
                </a>
                <!-- Edit and Delete buttons -->
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                    <a href="{{ route('playlist.edit', $playlist) }}" 
                       class="glass-button rounded-full p-2 text-blue-400 hover:bg-blue-600/20 transition touch-manipulation"
                       title="Edit playlist">
                        <i class="fa-solid fa-edit text-xs"></i>
                    </a>
                    <form method="POST" action="{{ route('playlist.destroy', $playlist) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this playlist?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="glass-button rounded-full p-2 text-red-400 hover:bg-red-600/20 transition touch-manipulation"
                                title="Delete playlist">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @if($playlists->isEmpty())
        <div class="glass-card rounded-xl p-6 sm:p-8 md:p-16 text-center">
            <i class="fa-solid fa-list text-4xl sm:text-5xl md:text-6xl text-[#666] mb-4 sm:mb-6"></i>
            <h3 class="text-xl sm:text-2xl font-semibold mb-2 sm:mb-4 text-white text-responsive-lg">No playlists yet</h3>
            <p class="text-[#aaa] mb-4 sm:mb-6 text-sm sm:text-base text-responsive-base">Create your first playlist to organize your favorite videos</p>
            <button onclick="showCreatePlaylistModal()" 
                    class="glass-button rounded-full px-6 sm:px-8 py-3 sm:py-4 text-white font-semibold text-sm sm:text-base transition touch-manipulation">
                Create Your First Playlist
            </button>
        </div>
    @endif
</div>

<!-- Create Playlist Modal -->
<div id="createPlaylistModal" class="fixed inset-0 glass-overlay z-50 hidden flex items-center justify-center">
    <div class="glass-card rounded-xl p-4 sm:p-6 w-full max-w-sm sm:max-w-md mx-4">
        <h2 class="text-lg sm:text-xl font-bold mb-4 text-white">Create New Playlist</h2>
        <form method="POST" action="{{ route('playlists.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-2 text-white">Name *</label>
                <input type="text" id="name" name="name" required maxlength="255"
                       class="w-full px-3 py-2 glass-input rounded-lg text-white focus:outline-none text-sm placeholder-[#606060]"
                       placeholder="Enter playlist name">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-2 text-white">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="1000"
                          class="w-full px-3 py-2 glass-input rounded-lg text-white focus:outline-none text-sm placeholder-[#606060]"
                          placeholder="Enter playlist description"></textarea>
            </div>
            
            <div class="mb-6">
                <label for="visibility" class="block text-sm font-medium mb-2 text-white">Visibility</label>
                <select id="visibility" name="visibility" required
                        class="w-full px-3 py-2 glass-input rounded-lg text-white focus:outline-none text-sm">
                    <option value="private">Private</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="public">Public</option>
                </select>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="hideCreatePlaylistModal()"
                        class="flex-1 px-4 py-2 glass-button rounded-lg transition text-white font-semibold text-sm touch-manipulation">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg transition text-white font-semibold text-sm touch-manipulation">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.remove('hidden');
    }
    
    function hideCreatePlaylistModal() {
        document.getElementById('createPlaylistModal').classList.add('hidden');
    }
    
    // Close modal when clicking outside
    document.getElementById('createPlaylistModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCreatePlaylistModal();
        }
    });
</script>
@endsection
