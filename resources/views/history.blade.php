@extends('layouts.app')

@section('title', 'History')

@section('content')
<div class="max-w-7xl mx-auto flex flex-col md:flex-row gap-8 mt-8">
    <!-- Left: Videos List -->
    <div class="flex-1 min-w-0">
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
                                <div class="flex gap-4 group hover:bg-[#232323] rounded-lg p-2 transition relative">
                                    <a href="{{ route('video.show', $video->id) }}" class="flex gap-4 flex-1">
                                        <div class="w-32 h-20 rounded-lg overflow-hidden bg-[#222] flex items-center justify-center">
                                            <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-full object-cover object-center group-hover:opacity-90 transition">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-semibold text-sm text-white leading-tight mb-1 truncate" title="{{ $video->title }}">{{ $video->title }}</div>
                                            <div class="text-[#aaa] text-xs truncate">{{ $video->user->name ?? 'Unknown' }}</div>
                                            <div class="text-[#aaa] text-xs flex items-center gap-2 mt-1">
                                                <span>{{ $item->created_at ? $item->created_at->diffForHumans() : 'Unknown time' }}</span>
                                                @if($video->duration)
                                                    <span>• {{ $video->duration }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <!-- Right: Search & Options -->
    <div class="w-full md:w-96 flex-shrink-0" x-data="{ manageOpen: false, from: '', to: '' }">
        <div class="bg-[#181818] rounded-xl p-6 flex flex-col gap-6 sticky top-24">
            <form method="GET" action="" class="flex items-center gap-2 mb-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search watch history" class="w-full px-4 py-2 rounded-lg bg-[#232323] text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"><i class="fa fa-search"></i></button>
                @if(request('search'))
                    <a href="{{ route('history') }}" class="ml-2 text-sm text-[#aaa] hover:text-white">Clear</a>
                @endif
            </form>
            <form method="POST" action="{{ route('history.clear') }}" onsubmit="return confirm('Are you sure you want to clear all watch history?');" class="mb-0">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-left px-4 py-3 rounded-lg bg-[#232323] hover:bg-[#333] text-white font-semibold transition w-full">
                    <i class="fa fa-trash"></i> Clear all watch history
                </button>
            </form>
            @php
                $paused = session('history_paused', auth()->user() ? (auth()->user()->history_paused ?? false) : false);
            @endphp
            <form method="POST" action="{{ route('history.pause.toggle') }}" class="mb-0">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-left px-4 py-3 rounded-lg bg-[#232323] hover:bg-[#333] text-white font-semibold transition w-full">
                    <i class="fa fa-pause"></i>
                    {{ $paused ? 'Resume watch history' : 'Pause watch history' }}
                </button>
            </form>
            @if($paused)
                <div class="text-yellow-400 text-sm mt-2">Watch history is currently paused. New videos will not be added to your history.</div>
            @endif
            <button @click="manageOpen = true" class="flex items-center gap-2 text-left px-4 py-3 rounded-lg bg-[#232323] hover:bg-[#333] text-white font-semibold transition"><i class="fa fa-gear"></i> Manage all history</button>
        </div>
        <!-- Manage History Popup -->
        <div x-show="manageOpen" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" x-transition>
            <div class="bg-[#232323] rounded-lg shadow-xl border border-[#333] w-full max-w-md mx-4 p-6 relative">
                <button @click="manageOpen = false" class="absolute top-3 right-3 text-[#aaa] hover:text-white"><i class="fa fa-xmark text-2xl"></i></button>
                <h3 class="text-lg font-semibold text-white mb-4">Clear history by date range</h3>
                <form method="POST" action="{{ route('history.clear.range') }}">
                    @csrf
                    <div class="flex flex-col gap-4">
                        <label class="text-white flex items-center gap-2">From:
                            <div class="relative w-full">
                                <input type="date" name="from" x-model="from" class="mt-1 px-3 py-2 rounded bg-[#181818] text-white w-full pr-10 cursor-pointer" @focus="showFromPicker = true" @click="showFromPicker = true" />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[#aaa] cursor-pointer" @click.prevent="
                                    $el.previousElementSibling.showPicker ? $el.previousElementSibling.showPicker() : $el.previousElementSibling.focus();
                                "><i class="fa fa-calendar"></i></span>
                            </div>
                        </label>
                        <label class="text-white flex items-center gap-2">To:
                            <div class="relative w-full">
                                <input type="date" name="to" x-model="to" class="mt-1 px-3 py-2 rounded bg-[#181818] text-white w-full pr-10 cursor-pointer" @focus="showToPicker = true" @click="showToPicker = true" />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[#aaa] cursor-pointer" @click.prevent="
                                    $el.previousElementSibling.showPicker ? $el.previousElementSibling.showPicker() : $el.previousElementSibling.focus();
                                "><i class="fa fa-calendar"></i></span>
                            </div>
                        </label>
                        <button type="submit" class="mt-2 px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition">Clear History</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
