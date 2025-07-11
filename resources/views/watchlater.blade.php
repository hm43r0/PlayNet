@extends('layouts.app')

@section('title', 'Watch Later')

@section('content')
<div class="w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
    <div class="glass-card rounded-xl p-6 sm:p-8 md:p-12 text-center">
        <i class="fa-regular fa-clock text-4xl sm:text-5xl md:text-6xl text-blue-400 mb-4 sm:mb-6"></i>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4 text-responsive-xl">Watch Later</h1>
        <p class="text-gray-400 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 text-responsive-base">Videos you save for later will appear here</p>
        <a href="{{ route('home') }}" class="glass-button rounded-full px-6 sm:px-8 py-3 sm:py-4 text-white font-semibold text-sm sm:text-base transition touch-manipulation">
            Browse Videos
        </a>
    </div>
</div>
@endsection
