@extends('layouts.app')

@section('title', 'Edit Video')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-[#181818] rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">Edit Video</h1>
        @if($errors->any())
            <div class="bg-red-600 text-white p-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('videos.update', $video) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium mb-2">Title *</label>
                <input type="text" id="title" name="title" required maxlength="255"
                       class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                       value="{{ old('title', $video->title) }}" placeholder="Enter video title">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-2">Description</label>
                <textarea id="description" name="description" rows="4" maxlength="1000"
                          class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                          placeholder="Enter video description">{{ old('description', $video->description) }}</textarea>
            </div>
            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium mb-2">Thumbnail</label>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                       class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded">
                @if($video->thumbnail_path)
                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="Current Thumbnail" class="mt-2 w-40 rounded">
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('videos.index') }}"
                   class="flex-1 px-4 py-2 border border-[#333] rounded hover:bg-[#222] transition text-center">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
                    Update Video
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
