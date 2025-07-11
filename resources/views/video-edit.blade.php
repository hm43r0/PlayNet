@extends('layouts.app')

@section('title', 'Edit Video')

@section('content')
<div class="w-full max-w-4xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <div class="glass-card rounded-xl shadow-lg p-4 sm:p-6 lg:p-8">
        <h1 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-white text-responsive-xl">Edit Video</h1>
        
        @if($errors->any())
            <div class="glass-notification rounded-xl p-3 sm:p-4 mb-4 border border-red-500/20">
                <div class="text-red-400 text-sm sm:text-base">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <form method="POST" action="{{ route('videos.update', $video) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4 sm:mb-6">
                <label for="title" class="block text-sm font-medium mb-2 text-white">Title *</label>
                <input type="text" id="title" name="title" required maxlength="255"
                       class="w-full px-3 sm:px-4 py-2 sm:py-3 glass-input rounded-lg text-white focus:outline-none text-sm sm:text-base placeholder-[#606060]"
                       value="{{ old('title', $video->title) }}" placeholder="Enter video title">
            </div>
            
            <div class="mb-4 sm:mb-6">
                <label for="description" class="block text-sm font-medium mb-2 text-white">Description</label>
                <textarea id="description" name="description" rows="4" maxlength="1000"
                          class="w-full px-3 sm:px-4 py-2 sm:py-3 glass-input rounded-lg text-white focus:outline-none text-sm sm:text-base placeholder-[#606060]"
                          placeholder="Enter video description">{{ old('description', $video->description) }}</textarea>
            </div>
            
            <div class="mb-6 sm:mb-8">
                <label for="thumbnail" class="block text-sm font-medium mb-2 text-white">Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                       class="w-full px-3 sm:px-4 py-2 sm:py-3 glass-input rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:glass-button file:text-white hover:file:bg-blue-700 file:transition">
                @if($video->thumbnail_path)
                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="Current Thumbnail" class="mt-3 w-32 sm:w-40 rounded-lg border border-[rgba(255,255,255,0.1)]">
                @endif
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <a href="{{ route('videos.index') }}"
                   class="flex-1 px-4 sm:px-6 py-2 sm:py-3 glass-button rounded-lg transition text-center text-white font-semibold text-sm sm:text-base touch-manipulation">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 rounded-lg transition text-white font-semibold text-sm sm:text-base touch-manipulation">
                    Update Video
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
