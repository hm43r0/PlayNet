@extends('layouts.app')

@section('title', 'History')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">History</h1>
    @if(empty($grouped) || $grouped->isEmpty())
        <div class="text-gray-400 text-lg">No history yet.</div>
    @else
        <div class="flex flex-col gap-8">
            @foreach($grouped as $section => $videos)
                <div>
                    <div class="text-lg font-bold text-white mb-3 mt-6">{{ $section }}</div>
                    <div class="flex flex-col gap-4">
                        @foreach($videos as $item)
                            @php $video = $item->video; @endphp
                            <a href="{{ route('video.show', $video->id) }}" class="flex gap-4 group hover:bg-[#232323] rounded-lg p-2 transition">
                                <div class="w-32 h-20 rounded-lg overflow-hidden bg-[#222] flex items-center justify-center">
                                    <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-sm text-white leading-tight mb-1 truncate" title="{{ $video->title }}">{{ $video->title }}</div>
                                    <div class="text-[#aaa] text-xs truncate">{{ $video->user->name ?? 'Unknown' }}</div>
                                    <div class="text-[#aaa] text-xs flex items-center gap-2 mt-1">
                                        <span>{{ $item->created_at->diffForHumans() }}</span>
                                        @if($video->duration)
                                            <span>• {{ $video->duration }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
