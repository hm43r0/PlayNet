@extends('layouts.app')

@section('title', 'YouTube')

@section('content')
<div class="flex flex-wrap gap-6 justify-start">
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
@endsection
