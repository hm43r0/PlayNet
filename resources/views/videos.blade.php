@extends('layouts.app')

@section('title', 'Your Videos')

@section('content')
<div x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }} }" class="w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-responsive-xl">Your Videos</h1>
        <button @click="showForm = !showForm" class="glass-button rounded-full px-4 sm:px-6 py-2 sm:py-3 text-white font-semibold flex items-center gap-2 transition text-sm sm:text-base touch-manipulation">
            <i class="fa-solid fa-upload text-sm sm:text-base"></i>
            <span>Upload Video</span>
        </button>
    </div>
    
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 sm:mb-6 glass-notification rounded-xl px-4 sm:px-6 py-3 sm:py-4 text-center text-green-400 border border-green-500/20">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Upload Form Popup -->
    <div x-show="showForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center glass-overlay" style="display: none;">
        <div @click.away="showForm = false" class="relative glass-card rounded-xl p-4 sm:p-6 lg:p-8 shadow-2xl w-full max-w-sm sm:max-w-md lg:max-w-lg mx-4">
            <button @click="showForm = false" class="absolute top-3 sm:top-4 right-3 sm:right-4 text-gray-400 hover:text-white text-xl sm:text-2xl focus:outline-none touch-manipulation">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6 text-white">Upload Video</h2>
            <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-4 glass-notification rounded-xl px-4 py-3 border border-red-500/20">
                        <ul class="list-disc pl-5 space-y-1 text-red-400 text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 text-white">Video Title</label>
                    <input name="title" type="text" value="{{ old('title') }}" class="w-full px-3 sm:px-4 py-2 rounded-lg glass-input text-white focus:outline-none text-sm sm:text-base placeholder-[#606060]" placeholder="Enter video title" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 text-white">Description</label>
                    <textarea name="description" class="w-full px-3 sm:px-4 py-2 rounded-lg glass-input text-white focus:outline-none text-sm sm:text-base placeholder-[#606060]" rows="4" placeholder="Enter video description">{{ old('description') }}</textarea>
                </div>
                
                <div class="mb-4 flex flex-col lg:flex-row gap-4 sm:gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold mb-2 text-white">Video File</label>
                        <input name="video" type="file" class="block w-full text-xs sm:text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:glass-button file:text-white hover:file:bg-blue-700 file:transition" accept="video/*" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold mb-2 text-white">Thumbnail</label>
                        <input name="thumbnail" type="file" class="block w-full text-xs sm:text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:glass-button file:text-white hover:file:bg-blue-700 file:transition" accept="image/*" required>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 mt-6">
                    <button type="button" @click="showForm = false" class="glass-button rounded-lg px-4 sm:px-6 py-2 sm:py-3 text-white font-semibold order-2 sm:order-1 text-sm sm:text-base touch-manipulation">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg font-semibold transition order-1 sm:order-2 text-sm sm:text-base touch-manipulation">Upload</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Videos Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
        @forelse($videos as $video)
            <div class="relative group">
                <x-video-card
                    :video="$video"
                    thumbnail="{{ asset('storage/' . $video->thumbnail_path) }}"
                    duration="{{ $video->duration }}"
                    title="{{ $video->title }}"
                    channel="{{ $video->user->name ?? 'Unknown' }}"
                    meta="{{ $video->created_at->diffForHumans() }}"
                />
                <div class="absolute top-2 right-2 flex gap-1 opacity-0 group-hover:opacity-100 transition z-10">
                    <a href="{{ route('videos.edit', $video) }}" class="glass-button rounded-full p-2 text-blue-400 hover:bg-blue-600/20 transition touch-manipulation" title="Edit video">
                        <i class="fa-solid fa-edit text-xs"></i>
                    </a>
                    <form method="POST" action="{{ route('videos.destroy', $video) }}" onsubmit="return confirm('Are you sure you want to delete this video?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="glass-button rounded-full p-2 text-red-400 hover:bg-red-600/20 transition touch-manipulation" title="Delete video">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-400 py-8 sm:py-12 text-sm sm:text-base">No videos uploaded yet.</div>
        @endforelse
    </div>
</div>
@endsection