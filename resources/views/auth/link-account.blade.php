@extends('layouts.app')

@section('title', 'Link Account')

@section('content')
<div class="max-w-md mx-auto mt-16">
    <div class="bg-[#181818] rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Link Another Account</h1>
        
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
        
        <form method="POST" action="{{ route('account.link') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                    value="{{ old('email') }}" placeholder="Enter email of account to link">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-3 py-2 bg-[#222] border border-[#333] rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Enter password for that account">
            </div>
            
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                Link Account
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-blue-400 hover:text-blue-300">
                ← Back to Home
            </a>
        </div>
        
        @if(auth()->user()->getAllLinkedAccounts()->count() > 0)
            <div class="mt-8 pt-6 border-t border-[#333]">
                <h3 class="text-lg font-semibold mb-4">Linked Accounts</h3>
                <div class="space-y-3">
                    @foreach(auth()->user()->getAllLinkedAccounts() as $linkedUser)
                        <div class="flex items-center justify-between bg-[#222] p-3 rounded">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper($linkedUser->name[0]) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $linkedUser->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $linkedUser->email }}</div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('account.unlink', $linkedUser) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm"
                                    onclick="return confirm('Are you sure you want to unlink this account?')">
                                    Unlink
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
