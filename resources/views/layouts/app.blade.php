<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PlayNet')</title>
    <!-- Tailwind CSS CDN - Consider local installation for production -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Optional: If you want to use the Roboto font like original YouTube -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* Custom Tailwind Configuration (if using CDN, extend as needed) */
        /* If you install Tailwind locally, these go in tailwind.config.js */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Roboto', sans-serif;
            /* Using Roboto as per original design */
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-[#0f0f0f] text-white font-roboto @stack('body_class')">
    <div x-data="sidebarApp()" x-init="init()">
        <!-- Header (full width, fixed at top) -->
        <header
            class="flex items-center justify-between px-6 py-2 bg-[#0f0f0f] sticky top-0 z-30 border-b border-[#222] h-14 w-full">
            <div class="flex items-center gap-4 min-w-[220px]">
                <button class="p-2 hover:bg-[#222] rounded-full"
                    @click="sidebarOpen = !sidebarOpen">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <a href="/" class="flex items-center space-x-0.5">
                    <img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" alt="YouTube" class="h-5">
                    <span class="text-xl ml-1 font-bold">YouTube</span><sup class="text-xs ml-0.5 text-[#aaa]">PK</sup>
                </a>
            </div>
            <form class="flex flex-1 justify-center max-w-[600px] mx-8" method="GET" action="{{ route('home') }}">
                <div class="flex w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                        class="flex-1 rounded-l-full px-4 py-2 bg-[#121212] text-white focus:outline-none focus:ring-1 focus:ring-blue-500 text-base border border-[#303030] border-r-0 placeholder-[#606060]">
                    <button type="submit"
                        class="bg-[#222] rounded-r-full px-6 py-2 text-white border border-l-0 border-[#303030] hover:bg-[#3a3a3a]">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <button
                    class="bg-[#222] p-2 rounded-full ml-3 hover:bg-[#3a3a3a] w-10 h-10 flex items-center justify-center">
                    <i class="fa-solid fa-microphone text-xl"></i>
                </button>
            </form>
            <div class="flex items-center gap-4 min-w-[180px] justify-end relative">
                <button
                    class="border border-[#222] rounded-full px-4 py-1.5 flex items-center space-x-2 text-sm hover:bg-[#222] transition">
                    <i class="fa-solid fa-plus-circle text-lg"></i>
                    <span>Create</span>
                </button>
                <button class="p-2 hover:bg-[#222] rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fa-regular fa-bell text-xl"></i>
                </button>
                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    @auth
                        <button @click="open = !open"
                            class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-semibold focus:outline-none">
                            {{ strtoupper(auth()->user()->name[0]) }}
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-80 bg-[#232323] rounded-xl shadow-lg z-50 border border-[#333]"
                            style="display: none;" x-transition>
                            
                            <!-- Current Account -->
                            <div class="p-4 border-b border-[#333]">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-full bg-[#444] flex items-center justify-center text-xl font-bold">
                                        {{ strtoupper(auth()->user()->name[0]) }}</div>
                                    <div>
                                        <div class="font-semibold text-base leading-tight">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-[#aaa]">
                                            {{ '@' . (auth()->user()->username ?? Str::slug(auth()->user()->name)) }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-blue-400 cursor-pointer">View your channel</div>
                            </div>
                            
                            <!-- Linked Accounts -->
                            @if(auth()->user()->getAllLinkedAccounts()->count() > 0)
                                <div class="border-b border-[#333]">
                                    <div class="px-4 py-2 text-xs text-[#aaa] font-medium">Switch Account</div>
                                    @foreach(auth()->user()->getAllLinkedAccounts() as $linkedUser)
                                        <form method="POST" action="{{ route('account.switch', $linkedUser) }}" class="inline-block w-full">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-3 hover:bg-[#181818] flex items-center gap-3 text-white">
                                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold">
                                                    {{ strtoupper($linkedUser->name[0]) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-sm">{{ $linkedUser->name }}</div>
                                                    <div class="text-xs text-[#aaa]">{{ $linkedUser->email }}</div>
                                                </div>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Account Management -->
                            <div class="border-b border-[#333]">
                                <a href="{{ route('account.link.form') }}" 
                                   class="w-full text-left px-4 py-3 hover:bg-[#181818] flex items-center gap-3 text-white text-sm">
                                    <i class="fa-solid fa-plus w-5"></i>
                                    <span>Add another account</span>
                                </a>
                            </div>
                            
                            <!-- Sign Out -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-3 hover:bg-[#181818] flex items-center gap-3 text-white text-base rounded-b-xl">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Sign out</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-8 h-8 rounded-full bg-[#333] flex items-center justify-center text-base font-bold focus:outline-none">
                            <i class="fa-regular fa-user text-xl"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </header>
        <div class="flex">
            <!-- Sidebar for Video Pages (Fixed Overlay) -->
            <template x-if="isVideoPage">
                <div>
                    <!-- Backdrop for mobile/video page sidebar -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 z-40" x-show="sidebarOpen"
                        @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" style="display: none;"></div>
                    <!-- Fixed sidebar for video pages -->
                    <aside x-show="sidebarOpen"
                        class="fixed top-0 left-0 w-60 bg-[#0f0f0f] h-full flex flex-col pt-16 z-50 border-r border-[#222] transition-all duration-200"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="-translate-x-60 opacity-0"
                        x-transition:enter-end="translate-x-0 opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="translate-x-0 opacity-100"
                        x-transition:leave-end="-translate-x-60 opacity-0" @click.away="sidebarOpen = false"
                        style="display: none;">
                        <nav class="flex-1 flex flex-col">
                            @include('partials.sidebar')
                        </nav>
                    </aside>
                </div>
            </template>
            
            <!-- Sidebar for Regular Pages (Static) -->
            <aside x-show="sidebarOpen && !isVideoPage"
                class="w-60 bg-[#181818] h-[calc(100vh-56px)] flex-col pt-2 z-20 border-r border-[#222] transition-all duration-200 sticky top-14 lg:flex hidden"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-x-10" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-10">
                <nav class="flex-1 flex flex-col">
                    @include('partials.sidebar')
                </nav>
            </aside>
            
            <!-- Main Content Slot -->
            <main class="flex-1 px-8 pt-8">
                @yield('content')
            </main>
        </div>
    </div>
    <!-- Alpine.js script, defer loading -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function sidebarApp() {
            return {
                sidebarOpen: true,
                isVideoPage: false,
                init() {
                    this.isVideoPage = document.body.classList.contains('video-player-page');
                    if (this.isVideoPage) {
                        this.sidebarOpen = false;
                    } else {
                        this.sidebarOpen = true;
                    }
                }
            }
        }
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarApp', sidebarApp);
        });
    </script>
</body>

</html>
