@extends('layouts.app')

@section('title', 'Edit Playlist')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-[#181818] rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">Edit Playlist</h1>
        
        @if(session('success'))
            <div class="bg-green-600 text-white p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if($errors->any())
            <div class="bg-red-600 text-white p-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('playlist.update', $playlist) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-2">Name *</label>
                <input type="text" id="name" name="name" required maxlength="255"
                       class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                       value="{{ old('name', $playlist->name) }}" placeholder="Enter playlist name">
            </div>
            
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium mb-2">Description</label>
                <textarea id="description" name="description" rows="4" maxlength="1000"
                          class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                          placeholder="Enter playlist description">{{ old('description', $playlist->description) }}</textarea>
            </div>
            
            <div class="mb-6">
                <label for="visibility" class="block text-sm font-medium mb-2">Visibility</label>
                <select id="visibility" name="visibility" required
                        class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="private" {{ old('visibility', $playlist->visibility) === 'private' ? 'selected' : '' }}>Private</option>
                    <option value="unlisted" {{ old('visibility', $playlist->visibility) === 'unlisted' ? 'selected' : '' }}>Unlisted</option>
                    <option value="public" {{ old('visibility', $playlist->visibility) === 'public' ? 'selected' : '' }}>Public</option>
                </select>
                <p class="text-xs text-[#aaa] mt-1">
                    <strong>Private:</strong> Only you can view<br>
                    <strong>Unlisted:</strong> Anyone with the link can view<br>
                    <strong>Public:</strong> Anyone can search and view
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('playlist.show', $playlist) }}"
                   class="flex-1 px-4 py-2 border border-[#333] rounded hover:bg-[#222] transition text-center">
                    Cancel
                </a>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded transition">
                    Update Playlist
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
