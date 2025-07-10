@extends('layouts.app')

@section('title', $video->title)

@push('body_class')
video-player-page
@endpush

@section('content')
<div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto mt-8">
    <!-- Main Video Player -->
    <div class="flex-1 min-w-0">
        <div class="bg-[#181818] rounded-xl shadow-lg overflow-hidden">
            <div class="w-full bg-black aspect-video flex items-center justify-center">
                <video controls poster="{{ asset('storage/' . $video->thumbnail_path) }}" class="w-full h-full max-h-[520px] bg-black rounded-t-xl">
                    <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-2 text-white">{{ $video->title }}</h1>
                <!-- Channel/Action Row (YouTube style) -->
                <div class="flex flex-col gap-2 mb-4">
                    <div x-data="{
                            subscribed: @json($subscribed),
                            subscriberCount: @json($subscribersCount),
                            loading: false,
                            subscribe() {
                                if (!@json(auth()->check())) {
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                }
                                this.loading = true;
                                fetch(this.subscribed ? '{{ route('channels.unsubscribe', $video->user->id) }}' : '{{ route('channels.subscribe', $video->user->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => {
                                    this.subscribed = data.subscribed;
                                    this.subscriberCount = data.count;
                                    this.loading = false;
                                });
                            }
                        }" class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ urlencode($video->user->name ?? 'U') }}" alt="avatar" class="w-10 h-10 rounded-full bg-[#222] border border-[#333]">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1">
                                    <span class="font-semibold text-white text-base truncate max-w-[160px]">{{ $video->user->name ?? 'Unknown' }}</span>
                                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="text-[#aaa] text-xs"><span x-text="subscriberCount"></span> subscribers</div>
                            </div>
                        </div>
                        @if(auth()->check() && auth()->id() !== $video->user->id)
                        <div>
                            <button @click="subscribe()" 
                                :class="subscribed ? 'subscribed-btn' : 'bg-white text-black hover:bg-gray-200'" 
                                class="font-semibold px-5 py-2 rounded-full transition text-sm min-w-[110px]">
                                <span x-show="!subscribed">Subscribe</span>
                                <span x-show="subscribed">Subscribed</span>
                            </button>
                        </div>
                        @endif
                        <div class="flex items-center gap-2 ml-auto" x-data="{
                            liked: @json($liked),
                            disliked: @json($disliked ?? false),
                            likeCount: @json($likeCount),
                            dislikeCount: @json($dislikeCount ?? 0),
                            loading: false,
                            toggleLike() {
                                if (!@json(auth()->check())) {
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                }
                                this.loading = true;
                                fetch(this.liked ? '{{ route('videos.unlike', $video->id) }}' : '{{ route('videos.like', $video->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => {
                                    this.liked = data.liked;
                                    this.likeCount = data.count;
                                    if (this.liked && this.disliked) {
                                        this.disliked = false;
                                        this.dislikeCount = data.dislikeCount;
                                    }
                                    this.loading = false;
                                });
                            },
                            toggleDislike() {
                                if (!@json(auth()->check())) {
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                }
                                this.loading = true;
                                fetch(this.disliked ? '{{ route('videos.undislike', $video->id) }}' : '{{ route('videos.dislike', $video->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => {
                                    this.disliked = data.disliked;
                                    this.dislikeCount = data.count;
                                    if (this.disliked && this.liked) {
                                        this.liked = false;
                                        this.likeCount = data.likeCount;
                                    }
                                    this.loading = false;
                                });
                            }
                        }">
                            <form @submit.prevent="toggleLike()">
                                <button type="submit" :class="liked ? 'bg-blue-600' : 'bg-[#232323] hover:bg-[#333]'" class="flex items-center gap-1 text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                                    <i :class="liked ? 'fa-solid fa-thumbs-up' : 'fa-regular fa-thumbs-up'" class="text-lg"></i>
                                    <span x-text="likeCount"></span>
                                </button>
                            </form>
                            <form @submit.prevent="toggleDislike()">
                                <button type="submit" :class="disliked ? 'bg-blue-600' : 'bg-[#232323] hover:bg-[#333]'" class="flex items-center gap-1 text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                                    <i :class="disliked ? 'fa-solid fa-thumbs-down' : 'fa-regular fa-thumbs-down'" class="text-lg"></i>
                                    <span x-text="dislikeCount"></span>
                                </button>
                            </form>
                            <div x-data="{ 
                                open: false, 
                                copied: false,
                                saveMenuOpen: false,
                                playlists: [],
                loadingPlaylists: false,
                videoSavedToPlaylists: [],
                watchLaterPlaylistId: null,
                                async loadPlaylists() {
                                    if (this.playlists.length > 0) return;
                                    this.loadingPlaylists = true;
                                    try {
                                        const response = await fetch('{{ route('api.user-playlists') }}');
                                        this.playlists = await response.json();
                                        
                                        // Check which playlists this video is already in
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
                                            // Add watch later playlist ID if we have it
                                            await this.checkVideoInPlaylists();
                                        } else {
                                            alert(data.error || 'Failed to add to Watch Later');
                                        }
                                    } catch (error) {
                                        alert('Failed to add to Watch Later');
                                    }
                                }
                            }" class="relative" x-init="checkVideoInPlaylists()">
                                <!-- Save Menu Button -->
                                <button @click="saveMenuOpen = true; loadPlaylists()" 
                                        class="flex items-center gap-1 bg-[#232323] hover:bg-[#333] text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                                    <i :class="isVideoSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'" class="text-lg"></i>
                                    <span x-text="isVideoSaved ? 'Saved' : 'Save'"></span>
                                </button>
                                
                                <!-- Save Popup Modal -->
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
                                            <button @click="saveToWatchLater()" 
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
                            </div>
                            
                            <div x-data="{ open: false, copied: false }" class="relative">
                                <button @click="open = true" class="flex items-center gap-1 bg-[#232323] hover:bg-[#333] text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                                    <i class="fa-solid fa-share text-lg"></i>
                                    Share
                                </button>
                                <div x-show="open" @click.away="open = false" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-transition style="display: none;">
                                    <div class="bg-[#232323] rounded-xl shadow-xl p-6 w-full max-w-xs relative flex flex-col items-center">
                                        <button @click="open = false" class="absolute top-2 right-2 text-[#aaa] hover:text-white"><i class="fa-solid fa-xmark text-2xl"></i></button>
                                        <div class="font-semibold text-lg text-white mb-4">Share</div>
                                        <div class="flex flex-col gap-3 w-full">
                                            <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 1500)" class="flex items-center gap-2 bg-[#181818] hover:bg-[#333] text-white px-4 py-2 rounded-lg w-full justify-center">
                                                <i class="fa-solid fa-link"></i>
                                                <span>Copy link</span>
                                                <span x-show="copied" class="ml-2 text-green-400 text-xs" x-transition>Copied!</span>
                                            </button>
                                            <a :href="'https://wa.me/?text=' + encodeURIComponent(window.location.href)" target="_blank" class="flex items-center gap-2 bg-[#181818] hover:bg-[#333] text-white px-4 py-2 rounded-lg w-full justify-center">
                                                <i class="fa-brands fa-whatsapp text-green-400"></i>
                                                <span>WhatsApp</span>
                                            </a>
                                            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href)" target="_blank" class="flex items-center gap-2 bg-[#181818] hover:bg-[#333] text-white px-4 py-2 rounded-lg w-full justify-center">
                                                <i class="fa-brands fa-facebook text-blue-500"></i>
                                                <span>Facebook</span>
                                            </a>
                                            <a :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href)" target="_blank" class="flex items-center gap-2 bg-[#181818] hover:bg-[#333] text-white px-4 py-2 rounded-lg w-full justify-center">
                                                <i class="fa-brands fa-x-twitter"></i>
                                                <span>Twitter/X</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="flex items-center gap-1 bg-[#232323] hover:bg-[#333] text-white px-2 py-2 rounded-full text-sm font-semibold transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/><circle cx="5" cy="12" r="2"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-[#aaa] text-sm mt-1 flex-wrap">
                        <span>{{ $video->created_at->diffForHumans() }}</span>
                        @if($video->duration)
                            <span>• {{ $video->duration }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-white text-base mb-2">{{ $video->description }}</div>
            </div>
        </div>
    </div>
    <!-- Related Videos Sidebar -->
    <div class="w-full lg:w-[400px] flex-shrink-0">
        <div class="flex flex-col gap-4">
            <div class="flex flex-wrap gap-2 mb-2">
                <span class="bg-[#232323] text-white text-xs px-3 py-1 rounded-full font-semibold">All</span>
                <span class="bg-[#232323] text-white text-xs px-3 py-1 rounded-full font-semibold">From the series</span>
                <span class="bg-[#232323] text-white text-xs px-3 py-1 rounded-full font-semibold">From {{ $video->user->name ?? 'Uploader' }}</span>
            </div>
            @php
                $related = \App\Models\Video::where('id', '!=', $video->id)->latest()->take(8)->get();
            @endphp
            @foreach($related as $rel)
                <a href="{{ route('video.show', $rel->id) }}" class="flex gap-3 group hover:bg-[#232323] rounded-lg p-2 transition">
                    <div class="w-32 h-20 rounded-lg overflow-hidden bg-[#222] flex items-center justify-center">
                        <img src="{{ asset('storage/' . $rel->thumbnail_path) }}" alt="{{ $rel->title }}" class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm text-white leading-tight mb-1 truncate" title="{{ $rel->title }}">{{ $rel->title }}</div>
                        <div class="text-[#aaa] text-xs truncate">{{ $rel->user->name ?? 'Unknown' }}</div>
                        <div class="text-[#aaa] text-xs flex items-center gap-2 mt-1">
                            <span>{{ $rel->created_at->diffForHumans() }}</span>
                            @if($rel->duration)
                                <span>• {{ $rel->duration }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Remove hover effect for subscribed button, or use a subtle color */
    .subscribed-btn {
        background: #232323 !important;
        color: #fff !important;
        border: 1px solid #333;
        transition: background 0.2s, color 0.2s;
    }
    .subscribed-btn:hover, .subscribed-btn:focus {
        background: #232323 !important;
        color: #fff !important;
        /* Optionally, add a very subtle effect: */
        box-shadow: 0 0 0 2px #33333355;
    }
</style>
@endpush