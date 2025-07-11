@extends('layouts.app')

@section('title', $video->title)

@push('body_class')
    video-player-page
@endpush

@section('content')
    <div class="flex flex-col xl:flex-row gap-3 sm:gap-4 md:gap-6 lg:gap-8 max-w-full 2xl:max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8 w-full overflow-x-hidden">
        <!-- Main Video Player -->
        <div class="flex-1 min-w-0 w-full">
            <div class="glass-card rounded-xl sm:rounded-2xl shadow-2xl overflow-hidden w-full max-w-full">
                <div class="w-full bg-black aspect-video flex items-center justify-center relative">
                    <video controls poster="{{ asset('storage/' . $video->thumbnail_path) }}"
                        class="w-full h-auto max-h-[200px] xs:max-h-[250px] sm:max-h-[320px] md:max-h-[420px] lg:max-h-[520px] xl:max-h-[600px] bg-black rounded-t-xl sm:rounded-t-2xl">
                        <source src="{{ asset('storage/' . $video->video_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                
                <div class="p-3 sm:p-4 md:p-6 lg:p-8 w-full max-w-full">
                    <h1 class="text-base sm:text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold mb-3 sm:mb-4 text-white break-words w-full max-w-full leading-tight">
                        {{ $video->title }}
                    </h1>
                    
                    <!-- Channel/Action Row -->
                    <div class="flex flex-col gap-3 sm:gap-4 mb-4 sm:mb-6 w-full max-w-full">
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
                        }"
                            class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 w-full max-w-full">
                            
                            <!-- Channel Info -->
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 w-full max-w-full">
                                <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ urlencode($video->user->name ?? 'U') }}"
                                    alt="avatar"
                                    class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 border-2 border-white/20 shadow-lg flex-shrink-0">
                                <div class="min-w-0 flex-1 w-full max-w-full">
                                    <div class="flex items-center gap-1 sm:gap-2 w-full max-w-full">
                                        <span class="font-semibold text-white text-sm sm:text-base md:text-lg truncate max-w-[120px] sm:max-w-[200px] md:max-w-[300px]">{{ $video->user->name ?? 'Unknown' }}</span>
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 6.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="text-[#aaa] text-xs sm:text-sm">
                                        <span x-text="subscriberCount"></span> subscribers
                                    </div>
                                </div>
                                
                                @if (auth()->check() && auth()->id() !== $video->user->id)
                                    <div class="flex-shrink-0">
                                        <button @click="subscribe()"
                                            :class="subscribed ?
                                                'glass-button text-white' :
                                                'bg-white text-black hover:bg-gray-200 border border-white/20'"
                                            class="font-semibold px-3 sm:px-4 md:px-6 py-1.5 sm:py-2 md:py-2.5 rounded-full transition text-xs sm:text-sm md:text-base min-w-[80px] sm:min-w-[100px] md:min-w-[120px] touch-manipulation">
                                            <span x-show="!subscribed">Subscribe</span>
                                            <span x-show="subscribed">Subscribed</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Action Buttons Row -->
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 md:gap-4 w-full" x-data="{
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
                            <!-- Like/Dislike Buttons -->
                            <div class="flex items-center glass-light rounded-full overflow-hidden">
                                <form @submit.prevent="toggleLike()">
                                    <button type="submit"
                                        :class="liked && !disliked ? 'bg-blue-600/80 text-white' : 'glass-button text-[#aaa] hover:text-blue-400'"
                                        class="flex items-center px-3 sm:px-4 py-2 sm:py-2.5 font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-500 group touch-manipulation">
                                        <svg :class="liked && !disliked ? 'text-white fill-white' : 'text-[#aaa] fill-[#aaa] group-hover:text-blue-500 group-hover:fill-blue-500'"
                                            class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mr-1 sm:mr-2 transition-colors duration-150" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M8 11H3v10h5V11zm10.77 0H14.54l1.52-4.94A2.003 2.003 0 0 0 14.38 4c-.58 0-1.14.24-1.52.65L7 11v10h10.43c1.06 0 1.98-.67 2.19-1.61l1.34-6c.23-1.24-.82-2.39-2.23-2.39zM7 20H4v-8h3v8zm12.98-6.83l-1.34 6c-.1.47-.61.82-1.21.82H8V11.39l5.6-6.06c.19-.21.48-.33.78-.33.26 0 .5.11.63.3.07.1.15.26.09.47L13.58 10.71l-.4 1.29h4.23c.41 0 .8.17 1.03.46.12.15.25.4.18.72z" />
                                        </svg>
                                        <span class="text-xs sm:text-sm md:text-base font-bold" x-text="likeCount"></span>
                                    </button>
                                </form>
                                <div class="w-px h-6 bg-white/20"></div>
                                <form @submit.prevent="toggleDislike()">
                                    <button type="submit"
                                        :class="disliked && !liked ? 'bg-blue-600/80 text-white' : 'glass-button text-[#aaa] hover:text-blue-400'"
                                        class="flex items-center px-3 sm:px-4 py-2 sm:py-2.5 font-semibold transition focus:outline-none focus:ring-2 focus:ring-blue-500 group touch-manipulation">
                                        <svg :class="disliked && !liked ? 'text-white fill-white' : 'text-[#aaa] fill-[#aaa] group-hover:text-blue-500 group-hover:fill-blue-500'"
                                            class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 transition-colors duration-150" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M17 4H6.57c-1.07 0-1.98.67-2.19 1.61l-1.34 6C2.77 12.85 3.82 14 5.23 14h4.23l-1.52 4.94C7.62 19.97 8.46 21 9.62 21c.58 0 1.14-.24 1.52-.65L17 14h4V4h-4zm-6.6 15.67c-.19.21-.48.33-.78.33-.26 0-.5-.11-.63-.3-.07-.1-.15-.26-.09-.47l1.52-4.94.4-1.29H5.23c-.41 0-.8-.17-1.03-.46-.12-.15-.25-.4-.18-.72l1.34-6c.1-.47.61-.82 1.21-.82H16v8.61l-5.6 6.06zM20 13h-3V5h3v8z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Save Button -->
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
                                            this.videoSavedToPlaylists = data.playlist_ids || data;
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
                            }" class="relative" x-init="checkVideoInPlaylists()">
                                <button @click="saveMenuOpen = true; loadPlaylists()"
                                    class="flex items-center gap-1 sm:gap-2 glass-button text-white px-3 sm:px-4 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition touch-manipulation">
                                    <i :class="isVideoSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark'"
                                        class="text-sm sm:text-base md:text-lg"></i>
                                    <span x-text="isVideoSaved ? 'Saved' : 'Save'" class="hidden sm:inline"></span>
                                </button>
                                
                                <!-- Save Popup Modal -->
                                <div x-show="saveMenuOpen" @click.away="saveMenuOpen = false"
                                    class="fixed inset-0 glass-overlay z-50 flex items-center justify-center p-4"
                                    x-transition style="display: none;">
                                    <div class="glass-notification rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md">
                                        <div class="flex items-center justify-between p-4 sm:p-6 border-b border-white/10">
                                            <h3 class="text-base sm:text-lg font-semibold text-white">Save video to...</h3>
                                            <button @click="saveMenuOpen = false"
                                                class="text-[#aaa] hover:text-white glass-button p-2 rounded-full touch-manipulation">
                                                <i class="fa-solid fa-xmark text-lg sm:text-xl"></i>
                                            </button>
                                        </div>
                                        <div class="p-4 sm:p-6 max-h-64 sm:max-h-80 overflow-y-auto">
                                            <!-- Watch Later -->
                                            <button @click="saveToWatchLater()"
                                                class="w-full text-left px-3 py-3 glass-notification-item rounded-lg flex items-center gap-3 text-white mb-2 touch-manipulation">
                                                <i class="fa-regular fa-clock text-base sm:text-lg w-5"></i>
                                                <span class="text-sm sm:text-base">Watch Later</span>
                                                <i x-show="isVideoInWatchLater()"
                                                    class="fa-solid fa-check text-blue-400 ml-auto"></i>
                                            </button>
                                            <div class="border-t border-white/10 my-3"></div>
                                            
                                            <!-- Loading State -->
                                            <div x-show="loadingPlaylists"
                                                class="px-3 py-4 text-[#aaa] text-sm text-center">
                                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>
                                                Loading playlists...
                                            </div>
                                            
                                            <!-- Playlists -->
                                            <template x-for="playlist in playlists" :key="playlist.id">
                                                <button @click="togglePlaylist(playlist.id)"
                                                    class="w-full text-left px-3 py-3 glass-notification-item rounded-lg flex items-center gap-3 text-white mb-1 touch-manipulation">
                                                    <i class="fa-solid fa-list text-base sm:text-lg w-5"></i>
                                                    <span x-text="playlist.name" class="flex-1 text-sm sm:text-base truncate"></span>
                                                    <i x-show="isVideoInPlaylist(playlist.id)"
                                                        class="fa-solid fa-check text-blue-400"></i>
                                                </button>
                                            </template>
                                            
                                            <!-- No Playlists -->
                                            <div x-show="!loadingPlaylists && playlists.length === 0"
                                                class="px-3 py-4 text-[#aaa] text-sm text-center">
                                                No playlists found
                                            </div>
                                            <div class="border-t border-white/10 my-3"></div>
                                            
                                            <!-- Create New Playlist -->
                                            <a href="{{ route('playlists') }}"
                                                class="w-full text-left px-3 py-3 glass-notification-item rounded-lg flex items-center gap-3 text-white block touch-manipulation">
                                                <i class="fa-solid fa-plus text-base sm:text-lg w-5"></i>
                                                <span class="text-sm sm:text-base">Create new playlist</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Share Button -->
                            <div x-data="{ open: false, copied: false }" class="relative">
                                <button @click="open = true"
                                    class="flex items-center gap-1 sm:gap-2 glass-button text-white px-3 sm:px-4 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition touch-manipulation">
                                    <i class="fa-solid fa-share text-sm sm:text-base md:text-lg"></i>
                                    <span class="hidden sm:inline">Share</span>
                                </button>
                                
                                <div x-show="open" @click.away="open = false"
                                    class="fixed inset-0 z-50 flex items-center justify-center glass-overlay p-4"
                                    x-transition style="display: none;">
                                    <div class="glass-notification rounded-xl sm:rounded-2xl shadow-2xl p-4 sm:p-6 w-full max-w-xs sm:max-w-sm relative flex flex-col items-center">
                                        <button @click="open = false"
                                            class="absolute top-2 right-2 text-[#aaa] hover:text-white glass-button p-2 rounded-full touch-manipulation">
                                            <i class="fa-solid fa-xmark text-lg sm:text-xl"></i>
                                        </button>
                                        <div class="font-semibold text-base sm:text-lg text-white mb-4 sm:mb-6">Share</div>
                                        <div class="flex flex-col gap-2 sm:gap-3 w-full">
                                            <button
                                                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 1500)"
                                                class="flex items-center gap-2 sm:gap-3 glass-notification-item text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg w-full justify-center touch-manipulation">
                                                <i class="fa-solid fa-link text-sm sm:text-base"></i>
                                                <span class="text-sm sm:text-base">Copy link</span>
                                                <span x-show="copied" class="ml-2 text-green-400 text-xs"
                                                    x-transition>Copied!</span>
                                            </button>
                                            <a :href="'https://wa.me/?text=' + encodeURIComponent(window.location.href)"
                                                target="_blank"
                                                class="flex items-center gap-2 sm:gap-3 glass-notification-item text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg w-full justify-center touch-manipulation">
                                                <i class="fa-brands fa-whatsapp text-green-400 text-sm sm:text-base"></i>
                                                <span class="text-sm sm:text-base">WhatsApp</span>
                                            </a>
                                            <a :href="'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href)"
                                                target="_blank"
                                                class="flex items-center gap-2 sm:gap-3 glass-notification-item text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg w-full justify-center touch-manipulation">
                                                <i class="fa-brands fa-facebook text-blue-500 text-sm sm:text-base"></i>
                                                <span class="text-sm sm:text-base">Facebook</span>
                                            </a>
                                            <a :href="'https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.location.href)"
                                                target="_blank"
                                                class="flex items-center gap-2 sm:gap-3 glass-notification-item text-white px-3 sm:px-4 py-2 sm:py-3 rounded-lg w-full justify-center touch-manipulation">
                                                <i class="fa-brands fa-x-twitter text-sm sm:text-base"></i>
                                                <span class="text-sm sm:text-base">Twitter/X</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- More Options Button -->
                            <button class="flex items-center gap-1 glass-button text-white px-2 sm:px-3 py-2 sm:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition touch-manipulation">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="2" />
                                    <circle cx="19" cy="12" r="2" />
                                    <circle cx="5" cy="12" r="2" />
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Video Stats -->
                        <div class="flex items-center gap-2 sm:gap-3 text-[#aaa] text-xs sm:text-sm mt-2 flex-wrap w-full max-w-full">
                            <span class="font-medium">{{ $video->formatted_views }} views</span>
                            <span>•</span>
                            <span>{{ $video->created_at->diffForHumans() }}</span>
                            @if ($video->duration)
                                <span>•</span>
                                <span>{{ $video->duration }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Video Description -->
                    <div class="text-white text-sm sm:text-base mb-4 sm:mb-6 break-words w-full max-w-full leading-relaxed">
                        {{ $video->description }}
                    </div>
                </div>
                
                <!-- Comments Section -->
                <script>
                    window.commentsData = {
                        comments: {!! json_encode($video->comments()->with('user')->latest()->get()) !!},
                        user: {!! json_encode(auth()->user()) !!}
                    };
                </script>
                
                <div class="glass-card rounded-xl sm:rounded-2xl shadow-2xl mt-4 sm:mt-6 p-3 sm:p-4 md:p-6 w-full max-w-full"
                    id="comments-section" x-data="{
                        comments: window.commentsData.comments,
                        newComment: '',
                        posting: false,
                        user: window.commentsData.user,
                        csrf: '{{ csrf_token() }}',
                        videoId: {{ $video->id }},
                        postComment() {
                            if (!this.newComment.trim()) return;
                            this.posting = true;
                            fetch('/video/' + this.videoId + '/comments', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': this.csrf,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ body: this.newComment })
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.comment) {
                                        this.comments.unshift(data.comment);
                                        this.newComment = '';
                                    }
                                })
                                .finally(() => this.posting = false);
                        },
                        deleteComment(id) {
                            if (!confirm('Delete this comment?')) return;
                            fetch('/comments/' + id, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': this.csrf,
                                        'Accept': 'application/json',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.success) {
                                        this.comments = this.comments.filter(c => c.id !== id);
                                    }
                                });
                        },
                        formatTime(dateStr) {
                            const date = new Date(dateStr);
                            const now = new Date();
                            const diff = (now - date) / 1000;
                            if (diff < 60) return 'just now';
                            if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
                            if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
                            if (diff < 604800) return Math.floor(diff / 86400) + ' days ago';
                            return date.toLocaleDateString();
                        },
                        canDelete(comment) {
                            return this.user && comment.user_id === this.user.id;
                        }
                    }">
                    
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6 w-full max-w-full flex-wrap">
                        <span class="text-lg sm:text-xl font-semibold text-white">Comments</span>
                        <span class="text-[#aaa] text-sm sm:text-base">(<span x-text="comments.length"></span>)</span>
                    </div>
                    
                    <!-- Comment Form -->
                    <template x-if="user">
                        <form @submit.prevent="postComment"
                            class="flex flex-col sm:flex-row items-start gap-3 sm:gap-4 mb-6 sm:mb-8 w-full max-w-full">
                            <img :src="user.avatar_url || `https://api.dicebear.com/7.x/thumbs/svg?seed=${encodeURIComponent(user.name)}`"
                                class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 border-2 border-white/20 shadow-lg mt-1 flex-shrink-0">
                            <div class="flex-1 w-full max-w-full">
                                <textarea x-model="newComment" rows="2" maxlength="1000" placeholder="Add a public comment..."
                                    class="w-full glass-input text-white rounded-lg px-3 sm:px-4 py-2 sm:py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none break-words text-sm sm:text-base"></textarea>
                                <div class="flex justify-end mt-2 sm:mt-3 w-full">
                                    <button type="submit" :disabled="posting || !newComment.trim()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 sm:px-6 py-2 sm:py-2.5 rounded-full transition disabled:opacity-60 disabled:cursor-not-allowed w-full sm:w-auto text-sm sm:text-base touch-manipulation">
                                        <span x-show="!posting">Comment</span>
                                        <span x-show="posting"><i class='fa fa-spinner fa-spin'></i></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </template>
                    
                    <template x-if="!user">
                        <div class="mb-6 sm:mb-8 text-sm sm:text-base">
                            <a href="{{ route('login') }}" class="text-blue-400 hover:underline">Sign in</a> to comment
                        </div>
                    </template>
                    
                    <!-- Comments List -->
                    <div class="flex flex-col gap-4 sm:gap-6 w-full max-w-full" x-show="comments.length">
                        <template x-for="comment in comments" :key="comment.id">
                            <div class="flex gap-2 sm:gap-3 group relative w-full max-w-full">
                                <img :src="comment.user.avatar_url || `https://api.dicebear.com/7.x/thumbs/svg?seed=${encodeURIComponent(comment.user.name)}`"
                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 border-2 border-white/20 shadow-lg flex-shrink-0">
                                <div class="flex-1 min-w-0 w-full max-w-full">
                                    <div class="flex items-center gap-2 w-full max-w-full flex-wrap">
                                        <span class="font-semibold text-white text-xs sm:text-sm truncate max-w-[120px] sm:max-w-[200px]"
                                            x-text="comment.user.name"></span>
                                        <span class="text-xs text-[#aaa]" x-text="formatTime(comment.created_at)"></span>
                                    </div>
                                    <div class="text-white text-sm sm:text-base mt-1 whitespace-pre-line break-words w-full max-w-full leading-relaxed"
                                        x-text="comment.body">
                                    </div>
                                </div>
                                <button x-show="user && canDelete(comment)" @click="deleteComment(comment.id)"
                                    class="absolute top-0 right-0 text-[#aaa] hover:text-red-400 opacity-0 group-hover:opacity-100 transition glass-button p-1.5 sm:p-2 rounded-full touch-manipulation"
                                    title="Delete">
                                    <i class="fa-solid fa-trash text-xs sm:text-sm"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    <div x-show="!comments.length" class="text-[#aaa] text-center py-6 sm:py-8 text-sm sm:text-base">
                        No comments yet. Be the first to comment!
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Videos Sidebar -->
        <div class="w-full xl:w-[350px] 2xl:w-[400px] flex-shrink-0 mt-4 sm:mt-6 xl:mt-0 max-w-full">
            <div class="flex flex-col gap-3 sm:gap-4 w-full max-w-full">
                <div class="flex flex-wrap gap-2 mb-2 sm:mb-4 w-full max-w-full">
                    <span class="glass-button text-white text-xs sm:text-sm px-3 py-1.5 rounded-full font-semibold">All</span>
                    <span class="glass-button text-white text-xs sm:text-sm px-3 py-1.5 rounded-full font-semibold">From the series</span>
                    <span class="glass-button text-white text-xs sm:text-sm px-3 py-1.5 rounded-full font-semibold truncate max-w-[120px] sm:max-w-none">
                        From {{ $video->user->name ?? 'Uploader' }}
                    </span>
                </div>
                
                @php
                    $related = \App\Models\Video::where('id', '!=', $video->id)->latest()->take(8)->get();
                @endphp
                
                @foreach ($related as $rel)
                    <a href="{{ route('video.show', $rel->id) }}"
                        class="flex gap-2 sm:gap-3 group glass-notification-item rounded-lg p-2 sm:p-3 transition w-full max-w-full touch-manipulation">
                        <div class="w-20 h-12 sm:w-24 sm:h-14 md:w-32 md:h-20 rounded-lg overflow-hidden bg-[#222] flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('storage/' . $rel->thumbnail_path) }}" alt="{{ $rel->title }}"
                                class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
                        </div>
                        <div class="flex-1 min-w-0 w-full max-w-full">
                            <div class="font-semibold text-xs sm:text-sm md:text-base text-white leading-tight mb-1 line-clamp-2 w-full max-w-full"
                                title="{{ $rel->title }}">{{ $rel->title }}</div>
                            <div class="text-[#aaa] text-xs sm:text-sm truncate w-full max-w-full">
                                {{ $rel->user->name ?? 'Unknown' }}
                            </div>
                            <div class="text-[#aaa] text-xs flex items-center gap-1 sm:gap-2 mt-1 w-full max-w-full">
                                <span>{{ $rel->created_at->diffForHumans() }}</span>
                                @if ($rel->duration)
                                    <span>•</span>
                                    <span>{{ $rel->duration }}</span>
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
        /* Glassmorphism Styles for Video Player */
        .glass-card {
            background: rgba(18, 18, 18, 0.6);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .glass-light {
            background: rgba(35, 35, 35, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .glass-button {
            background: rgba(34, 34, 34, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-button:hover {
            background: rgba(58, 58, 58, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .glass-input {
            background: rgba(18, 18, 18, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(48, 48, 48, 0.8);
        }

        .glass-input:focus {
            background: rgba(18, 18, 18, 0.8);
            border: 1px solid rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        .glass-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .glass-notification {
            background: rgba(35, 35, 35, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(51, 51, 51, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .glass-notification-item {
            background: rgba(24, 24, 24, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .glass-notification-item:hover {
            background: rgba(24, 24, 24, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .glass-card {
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            }
            
            .glass-button:hover {
                transform: none;
            }
            
            .glass-button:active {
                transform: scale(0.98);
            }
        }

        /* Touch-friendly hover states */
        @media (hover: none) {
            .glass-button:hover {
                transform: none;
            }
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(15, 15, 15, 0.5);
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
@endpush
