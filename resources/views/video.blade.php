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
                            <button @click="subscribe()" :class="subscribed ? 'bg-[#232323] text-white' : 'bg-white text-black'" class="font-semibold px-5 py-2 rounded-full hover:bg-gray-200 transition text-sm min-w-[110px]">
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
                            <div x-data="{ open: false, copied: false }" class="relative">
                                <button @click="open = true" class="flex items-center gap-1 bg-[#232323] hover:bg-[#333] text-white px-4 py-2 rounded-full text-sm font-semibold transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 12v.01"/><path d="M8 12v.01"/><path d="M12 12v.01"/><path d="M16 12v.01"/><path d="M20 12v.01"/></svg>
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