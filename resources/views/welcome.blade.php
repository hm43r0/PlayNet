@extends('layouts.app')

@section('title', 'YouTube')

@section('content')
<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse($videos as $video)
            <x-video-card
                :video="$video"
                thumbnail="{{ asset('storage/' . $video->thumbnail_path) }}"
                duration="{{ $video->duration }}"
                title="{{ $video->title }}"
                channel="{{ $video->user->name ?? 'Unknown' }}"
                meta="{{ $video->created_at->diffForHumans() }}"
            />
        @empty
            <div class="col-span-full text-center text-gray-400 py-12">No videos uploaded yet.</div>
        @endforelse
    </div>
</div>
@endsection
