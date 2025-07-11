@extends('layouts.app')

@section('title', 'Subscriptions')

@section('content')
<div class="w-full max-w-6xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <h1 class="text-2xl sm:text-3xl font-bold mb-6 sm:mb-8 text-white text-responsive-xl">Subscriptions</h1>
    
    @if($channels->isEmpty())
        <div class="glass-card rounded-xl p-6 sm:p-8 md:p-12 text-center">
            <i class="fa-solid fa-user-plus text-4xl sm:text-5xl md:text-6xl text-blue-400 mb-4 sm:mb-6"></i>
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-4 text-responsive-lg">No Subscriptions Yet</h2>
            <p class="text-gray-400 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 text-responsive-base">You are not subscribed to any channels yet.</p>
            <a href="{{ route('home') }}" class="glass-button rounded-full px-6 sm:px-8 py-3 sm:py-4 text-white font-semibold text-sm sm:text-base transition touch-manipulation">
                Browse Channels
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            @foreach($channels as $channel)
                <a href="#" class="glass-card rounded-xl p-4 sm:p-6 transition-all duration-300 hover:scale-105 hover:shadow-xl block touch-manipulation">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-lg sm:text-xl font-bold text-white shadow-lg flex-shrink-0">
                            {{ strtoupper($channel->name[0]) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-sm sm:text-base text-white truncate">{{ $channel->name }}</div>
                            <div class="text-xs sm:text-sm text-[#aaa] truncate">{{ '@' . ($channel->username ?? Str::slug($channel->name)) }}</div>
                            <div class="text-xs sm:text-sm text-[#aaa] mt-1">{{ $channel->subscribers_count }} subscribers</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
