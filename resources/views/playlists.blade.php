@extends('layouts.app')

@section('title', 'Playlists')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Your Playlists</h1>
        <button onclick="showCreatePlaylistModal()" 
                class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span>Create Playlist</span>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-600 text-white p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-600 text-white p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Watch Later Playlist -->
        <div class="bg-[#181818] rounded-lg overflow-hidden hover:bg-[#232323] transition group">
            <a href="{{ route('playlist.show', $watchLater) }}" class="block">
                <div class="aspect-video bg-gradient-to-br from-purple-600 to-blue-600 flex items-center justify-center relative">
                    @if($watchLater->first_video_thumbnail)
                        <img src="{{ asset('storage/' . $watchLater->first_video_thumbnail) }}" 
                             alt="Watch Later" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                            <i class="fa-regular fa-clock text-4xl text-white"></i>
                        </div>
                    @else
                        <i class="fa-regular fa-clock text-4xl text-white"></i>
                    @endif
                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 px-2 py-1 rounded text-xs">
                        {{ $watchLater->video_count }} videos
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-1">Watch Later</h3>
                    <p class="text-[#aaa] text-sm">Private playlist</p>
                </div>
            </a>
        </div>

        <!-- Regular Playlists -->
        @foreach($playlists as $playlist)
            <div class="bg-[#181818] rounded-lg overflow-hidden hover:bg-[#232323] transition group relative">
                <a href="{{ route('playlist.show', $playlist) }}" class="block">
                    <div class="aspect-video bg-[#222] flex items-center justify-center relative">
                        @if($playlist->first_video_thumbnail)
                            <img src="{{ asset('storage/' . $playlist->first_video_thumbnail) }}" 
                                 alt="{{ $playlist->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-list text-4xl text-[#666]"></i>
                        @endif
                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 px-2 py-1 rounded text-xs">
                            {{ $playlist->video_count }} videos
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-1 truncate">{{ $playlist->name }}</h3>
                        <p class="text-[#aaa] text-sm capitalize">{{ $playlist->visibility }} playlist</p>
                        @if($playlist->description)
                            <p class="text-[#aaa] text-xs mt-1 line-clamp-2">{{ $playlist->description }}</p>
                        @endif
                    </div>
                </a>
                <!-- Edit and Delete buttons -->
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition flex gap-1">
                    <a href="{{ route('playlist.edit', $playlist) }}" 
                       class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full"
                       title="Edit playlist">
                        <i class="fa-solid fa-edit text-xs"></i>
                    </a>
                    <form method="POST" action="{{ route('playlist.destroy', $playlist) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this playlist?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 p-2 rounded-full"
                                title="Delete playlist">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    @if($playlists->isEmpty())
        <div class="text-center py-16">
            <i class="fa-solid fa-list text-6xl text-[#666] mb-4"></i>
            <h3 class="text-xl font-semibold mb-2">No playlists yet</h3>
            <p class="text-[#aaa] mb-6">Create your first playlist to organize your favorite videos</p>
            <button onclick="showCreatePlaylistModal()" 
                    class="bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded-lg">
                Create Your First Playlist
            </button>
        </div>
    @endif
</div>

<!-- Create Playlist Modal -->
<div id="createPlaylistModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-[#181818] rounded-lg p-6 w-full max-w-md mx-4">
        <h2 class="text-xl font-bold mb-4">Create New Playlist</h2>
        <form method="POST" action="{{ route('playlists.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-2">Name *</label>
                <input type="text" id="name" name="name" required maxlength="255"
                       class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                       placeholder="Enter playlist name">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-2">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="1000"
                          class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                          placeholder="Enter playlist description"></textarea>
            </div>
            
            <div class="mb-6">
                <label for="visibility" class="block text-sm font-medium mb-2">Visibility</label>
                <select id="visibility" name="visibility" required
                        class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="private">Private</option>
                    <option value="unlisted">Unlisted</option>
                    <option value="public">Public</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="hideCreatePlaylistModal()"
                        class="flex-1 px-4 py-2 border border-[#333] rounded hover:bg-[#222] transition">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
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
