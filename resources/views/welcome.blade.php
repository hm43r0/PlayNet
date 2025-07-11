@extends('layouts.app')

@section('title', 'PlayNet')

@section('content')
<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6 md:gap-8">
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
            <div class="col-span-full text-center text-gray-400 py-8 sm:py-12 text-sm sm:text-base">No videos uploaded yet.</div>
        @endforelse
    </div>
</div>
@endsection
