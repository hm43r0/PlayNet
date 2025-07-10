@extends('layouts.app')

@section('title', 'Your Videos')

@section('content')
<div x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }} }" class="w-full max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">Your Videos</h1>
        <button @click="showForm = !showForm" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 transition">
            <i class="fa-solid fa-upload"></i>
            <span>Upload Video</span>
        </button>
    </div>
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-600 text-white px-6 py-3 rounded-lg shadow text-center">
            {{ session('success') }}
        </div>
    @endif
    <!-- Upload Form Popup -->
    <div x-show="showForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60" style="display: none;">
        <div @click.away="showForm = false" class="relative bg-[#181818] rounded-xl p-8 shadow-2xl border border-[#222] w-full max-w-lg mx-4">
            <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl focus:outline-none">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="text-xl font-bold mb-6 text-white">Upload Video</h2>
            <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data">
                @csrf
                <!-- Validation Errors -->
                @if($errors->any())
                    <div class="mb-4 bg-red-600 text-white px-4 py-3 rounded-lg">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Video Title</label>
                    <input name="title" type="text" value="{{ old('title') }}" class="w-full px-4 py-2 rounded bg-[#222] text-white border border-[#333] focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter video title" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">Description</label>
                    <textarea name="description" class="w-full px-4 py-2 rounded bg-[#222] text-white border border-[#333] focus:outline-none focus:ring-2 focus:ring-blue-500" rows="4" placeholder="Enter video description">{{ old('description') }}</textarea>
                </div>
                <div class="mb-4 flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold mb-2">Video File</label>
                        <input name="video" type="file" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" accept="video/*" required>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold mb-2">Thumbnail</label>
                        <input name="thumbnail" type="file" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700" accept="image/*" required>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" @click="showForm = false" class="bg-[#333] hover:bg-[#222] text-white px-6 py-2 rounded-lg font-semibold mr-3">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">Upload</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Placeholder for user's videos grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
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
                    <a href="{{ route('videos.edit', $video) }}" class="bg-blue-600 hover:bg-blue-700 p-2 rounded-full" title="Edit video">
                        <i class="fa-solid fa-edit text-xs"></i>
                    </a>
                    <form method="POST" action="{{ route('videos.destroy', $video) }}" onsubmit="return confirm('Are you sure you want to delete this video?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 p-2 rounded-full" title="Delete video">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-400 py-12">No videos uploaded yet.</div>
        @endforelse
    </div>
</div>
<!-- Alpine.js for x-data and x-show -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection