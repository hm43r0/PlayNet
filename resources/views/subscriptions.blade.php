@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Subscriptions</h1>
    @if($channels->isEmpty())
        <div class="text-gray-400 text-lg">You are not subscribed to any channels yet.</div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($channels as $channel)
                <a href="#" class="flex items-center gap-4 bg-[#181818] rounded-xl p-4 hover:bg-[#232323] transition">
                    <img src="https://api.dicebear.com/7.x/thumbs/svg?seed={{ urlencode($channel->name) }}" alt="avatar" class="w-12 h-12 rounded-full bg-[#222] border border-[#333]">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-base text-white truncate">{{ $channel->name }}</div>
                        <div class="text-xs text-[#aaa]">{{ '@' . ($channel->username ?? Str::slug($channel->name)) }}</div>
                        <div class="text-xs text-[#aaa] mt-1">{{ $channel->subscribers_count }} subscribers</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
