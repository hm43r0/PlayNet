@extends('layouts.app')

@section('title', 'Liked Videos')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Liked Videos</h1>
    @if(empty($grouped) || $grouped->isEmpty())
        <div class="text-gray-400 text-lg">You haven't liked any videos yet.</div>
    @else
        <div class="flex flex-col gap-8">
            @foreach($grouped as $section => $videos)
                <div>
                    <div class="text-lg font-bold text-white mb-3 mt-6">{{ $section }}</div>
                    <div class="flex flex-col gap-4">
                        @foreach($videos as $video)
                            <div class="flex gap-4 group hover:bg-[#232323] rounded-lg p-2 transition relative"
                                 x-data="{
                                    saveMenuOpen: false,
                                    playlists: [],
                                    loadingPlaylists: false,
                                    videoSavedToPlaylists: [],
                                    watchLaterPlaylistId: null,
                                    async loadPlaylists() {
                                        if (!@json(auth()->check())) {
                                            window.location.href = '{{ route('login') }}';
                                            return;
                                        }
                                        if (this.playlists.length > 0) return;
                                        this.loadingPlaylists = true;
                                        try {
                                            const response = await fetch('{{ route('api.user-playlists') }}');
                                            this.playlists = await response.json();
                                            await this.checkVideoInPlaylists();
                                        } catch (error) {
                                            console.error('Failed to load playlists:', error);
                                        }
                                        this.loadingPlaylists = false;
                                    },
                                    async checkVideoInPlaylists() {
                                        try {
                                            const response = await fetch('/api/video-playlists/{{ $video->id }}');
                                            if (response.ok) {
                                                const data = await response.json();
                                                this.videoSavedToPlaylists = data.playlist_ids || data; // Handle both old and new format
                                                this.watchLaterPlaylistId = data.watch_later_id || null;
                                            }
                                        } catch (error) {
                                            console.error('Failed to check video playlists:', error);
                                        }
                                    },
                                    isVideoInPlaylist(playlistId) {
                                        return this.videoSavedToPlaylists.includes(playlistId);
                                    },
                                    isVideoInWatchLater() {
                                        return this.watchLaterPlaylistId && this.videoSavedToPlaylists.includes(this.watchLaterPlaylistId);
                                    },
                                    get isVideoSaved() {
                                        return this.videoSavedToPlaylists.length > 0;
                                    },
                                    async togglePlaylist(playlistId) {
                                        try {
                                            const isCurrentlyInPlaylist = this.isVideoInPlaylist(playlistId);
                                            const url = isCurrentlyInPlaylist ? '{{ route('playlist.remove-video') }}' : '{{ route('playlist.add-video') }}';
                                            
                                            const response = await fetch(url, {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                body: JSON.stringify({
                                                    video_id: {{ $video->id }},
                                                    playlist_id: playlistId
                                                })
                                            });
                                            
                                            const data = await response.json();
                                            if (data.success) {
                                                if (isCurrentlyInPlaylist) {
                                                    this.videoSavedToPlaylists = this.videoSavedToPlaylists.filter(id => id !== playlistId);
                                                } else {
                                                    this.videoSavedToPlaylists.push(playlistId);
                                                }
                                            } else {
                                                alert(data.error || 'Operation failed');
                                            }
                                        } catch (error) {
                                            alert('Operation failed');
                                        }
                                    },
                                    async saveToWatchLater() {
                                        try {
                                            const response = await fetch('{{ route('watch-later.add', $video) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                }
                                            });
                                            const data = await response.json();
                                            if (data.success) {
                                                await this.checkVideoInPlaylists();
                                            } else {
                                                alert(data.error || 'Failed to add to Watch Later');
                                            }
                                        } catch (error) {
                                            alert('Failed to add to Watch Later');
                                        }
                                    }
                                 }">
                                <a href="{{ route('video.show', $video->id) }}" class="flex gap-4 flex-1">
                                    <div class="w-32 h-20 rounded-lg overflow-hidden bg-[#222] flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-sm text-white leading-tight mb-1 truncate" title="{{ $video->title }}">{{ $video->title }}</div>
                                        <div class="text-[#aaa] text-xs truncate">{{ $video->user->name ?? 'Unknown' }}</div>
                                        <div class="text-[#aaa] text-xs flex items-center gap-2 mt-1">
                                            <span>{{ $video->pivot->created_at ? $video->pivot->created_at->diffForHumans() : 'Unknown time' }}</span>
                                            @if($video->duration)
                                                <span>• {{ $video->duration }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                                
                                <!-- Save Button -->
                                @if(auth()->check())
                                <div class="flex items-center">
                                    <button @click="saveMenuOpen = true; loadPlaylists()" 
                                            class="opacity-0 group-hover:opacity-100 transition text-[#aaa] hover:text-white p-2"
                                            title="Save to playlist">
                                        <i :class="isVideoSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'" class="text-sm"></i>
                                    </button>
                                </div>
                                @endif

                                <!-- Save Popup Modal -->
                                @if(auth()->check())
                                <div x-show="saveMenuOpen" @click.away="saveMenuOpen = false" 
                                     class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
                                     x-transition style="display: none;">
                                    <div class="bg-[#232323] rounded-lg shadow-xl border border-[#333] w-full max-w-md mx-4">
                                        <div class="flex items-center justify-between p-4 border-b border-[#333]">
                                            <h3 class="text-lg font-semibold text-white">Save video to...</h3>
                                            <button @click="saveMenuOpen = false" class="text-[#aaa] hover:text-white">
                                                <i class="fa-solid fa-xmark text-xl"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="p-4 max-h-80 overflow-y-auto">
                                            <!-- Watch Later -->
                                            <button @click="saveToWatchLater(); saveMenuOpen = false" 
                                                    class="w-full text-left px-3 py-3 hover:bg-[#333] rounded flex items-center gap-3 text-white mb-2">
                                                <i class="fa-regular fa-clock text-lg w-5"></i>
                                                <span>Watch Later</span>
                                                <i x-show="isVideoInWatchLater()" class="fa-solid fa-check text-blue-400 ml-auto"></i>
                                            </button>
                                            
                                            <div class="border-t border-[#333] my-3"></div>
                                            
                                            <!-- Loading State -->
                                            <div x-show="loadingPlaylists" class="px-3 py-4 text-[#aaa] text-sm text-center">
                                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                                Loading playlists...
                                            </div>
                                            
                                            <!-- Playlists -->
                                            <template x-for="playlist in playlists" :key="playlist.id">
                                                <button @click="togglePlaylist(playlist.id)" 
                                                        class="w-full text-left px-3 py-3 hover:bg-[#333] rounded flex items-center gap-3 text-white mb-1">
                                                    <i class="fa-solid fa-list text-lg w-5"></i>
                                                    <span x-text="playlist.name" class="flex-1"></span>
                                                    <i x-show="isVideoInPlaylist(playlist.id)" class="fa-solid fa-check text-blue-400"></i>
                                                </button>
                                            </template>
                                            
                                            <!-- No Playlists -->
                                            <div x-show="!loadingPlaylists && playlists.length === 0" 
                                                 class="px-3 py-4 text-[#aaa] text-sm text-center">
                                                No playlists found
                                            </div>
                                            
                                            <div class="border-t border-[#333] my-3"></div>
                                            
                                            <!-- Create New Playlist -->
                                            <a href="{{ route('playlists') }}" 
                                               class="w-full text-left px-3 py-3 hover:bg-[#333] rounded flex items-center gap-3 text-white block">
                                                <i class="fa-solid fa-plus text-lg w-5"></i>
                                                <span>Create new playlist</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
