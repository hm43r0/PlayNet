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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        /* Custom Tailwind Configuration (if using CDN, extend as needed) */
        /* If you install Tailwind locally, these go in tailwind.config.js */
        html,
        body {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Roboto', sans-serif;
            /* Using Roboto as per original design */
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%);
            min-height: 100vh;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Glassmorphism Styles */
        .glass {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-light {
            background: rgba(35, 35, 35, 0.8);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .glass-card {
            background: rgba(18, 18, 18, 0.6);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .glass-button {
            background: rgba(34, 34, 34, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .glass-button:hover {
            background: rgba(58, 58, 58, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .glass-input {
            background: rgba(18, 18, 18, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(48, 48, 48, 0.8);
        }

        .glass-input:focus {
            background: rgba(18, 18, 18, 0.8);
            border: 1px solid rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        .glass-overlay {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .glass-notification {
            background: rgba(35, 35, 35, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(51, 51, 51, 0.8);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .glass-notification-item {
            background: rgba(24, 24, 24, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .glass-notification-item:hover {
            background: rgba(24, 24, 24, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Animated background for body */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-20px) rotate(1deg);
            }

            66% {
                transform: translateY(10px) rotate(-1deg);
            }
        }
    </style>
</head>

<body class="text-white font-roboto @stack('body_class')">
    <div x-data="sidebarApp()" x-init="init()">
        <!-- Header (full width, fixed at top) -->
        <header x-data="{ mobileSearch: false }"
            class="flex items-center justify-between px-2 md:px-6 py-2 glass sticky top-0 z-30 h-14 w-full">
            <div class="flex items-center gap-2 md:gap-4 min-w-0 md:min-w-[220px]">
                <button class="p-2 glass-button rounded-full" @click="sidebarOpen = !sidebarOpen">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <a href="/" class="flex items-center space-x-0.5">
                    <img src="{{ asset('images/logo.png') }}" alt="PlayNet" class="h-8 md:h-[6.25rem]">
                </a>
            </div>
            <!-- Desktop Search Bar -->
            <form class="flex flex-1 justify-center max-w-[600px] mx-8 hidden md:flex" method="GET"
                action="{{ route('home') }}">
                <div class="flex w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                        class="flex-1 rounded-l-full px-4 py-2 glass-input text-white focus:outline-none text-base placeholder-[#606060]">
                    <button type="submit" class="glass-button rounded-r-full px-6 py-2 text-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
            <!-- Mobile Search Overlay -->
            <div x-show="mobileSearch" class="fixed inset-0 z-50 glass-overlay flex items-start pt-4 px-4"
                style="display: none;" x-transition>
                <form class="flex w-full max-w-2xl mx-auto" method="GET" action="{{ route('home') }}">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                        class="flex-1 rounded-l-full px-4 py-2 glass-input text-white focus:outline-none text-base placeholder-[#606060]">
                    <button type="submit" class="glass-button rounded-r-full px-6 py-2 text-white">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <button type="button" class="ml-2 p-2 text-white glass-button rounded-full"
                        @click="mobileSearch = false">
                        <i class="fa-solid fa-times text-2xl"></i>
                    </button>
                </form>
            </div>
            <div class="flex items-center gap-2 md:gap-4 min-w-0 md:min-w-[180px] justify-end relative">
                <!-- Mobile Search Icon -->
                <button class="md:hidden p-2 glass-button rounded-full mx-2" @click="mobileSearch = true">
                    <i class="fa-solid fa-magnifying-glass text-xl"></i>
                </button>
                <!-- Hide Create button on mobile -->
                <button
                    class="glass-button rounded-full px-4 py-1.5 flex items-center space-x-2 text-sm transition hidden md:flex">
                    <i class="fa-solid fa-plus-circle text-lg"></i>
                    <span>Create</span>
                </button>
                <!-- Notification Bell with Unread Count -->
                @auth
                    @php
                        $unreadNotifications = auth()
                            ->user()
                            ->notifications()
                            ->whereNull('read_at')
                            ->latest()
                            ->take(10)
                            ->get();
                    @endphp
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="p-2 glass-button rounded-full w-10 h-10 flex items-center justify-center relative">
                            <i class="fa-regular fa-bell text-xl"></i>
                            @if ($unreadNotifications->count() > 0)
                                <span
                                    class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 shadow-lg">{{ $unreadNotifications->count() }}</span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-96 glass-notification rounded-xl z-50" style="display: none;"
                            x-transition>
                            <div class="p-4 border-b border-[rgba(255,255,255,0.1)] font-semibold text-lg text-white">
                                Notifications</div>
                            <div class="max-h-96 overflow-y-auto divide-y divide-[rgba(255,255,255,0.1)]">
                                @forelse($unreadNotifications as $notification)
                                    <div
                                        class="p-4 glass-notification-item flex gap-3 items-center transition-all duration-200">
                                        <i
                                            class="fa-solid {{ notificationIcon($notification->data['type'] ?? null) }} text-xl"></i>
                                        <div class="flex-1 min-w-0">
                                            @if (($notification->data['type'] ?? null) === 'video_upload')
                                                <div class="text-white font-medium truncate">New video from
                                                    {{ $notification->data['uploader'] ?? 'Channel' }}</div>
                                                <div class="text-[#aaa] text-sm truncate">
                                                    {{ $notification->data['title'] ?? '' }}</div>
                                                <a href="{{ route('video.show', $notification->data['video_id'] ?? 0) }}"
                                                    class="text-blue-400 text-xs hover:underline">Watch now</a>
                                            @elseif(($notification->data['type'] ?? null) === 'subscribed')
                                                <div class="text-white font-medium truncate">
                                                    {{ $notification->data['subscriber_name'] ?? 'Someone' }} subscribed to
                                                    your channel</div>
                                            @elseif(($notification->data['type'] ?? null) === 'like')
                                                <div class="text-white font-medium truncate">
                                                    {{ $notification->data['liker_name'] ?? 'Someone' }} liked your video
                                                </div>
                                                <a href="{{ route('video.show', $notification->data['video_id'] ?? 0) }}"
                                                    class="text-blue-400 text-xs hover:underline">View video</a>
                                            @elseif(($notification->data['type'] ?? null) === 'comment')
                                                <div class="text-white font-medium truncate">
                                                    {{ $notification->data['commenter_name'] ?? 'Someone' }} commented on
                                                    your video</div>
                                                <div class="text-[#aaa] text-xs truncate">
                                                    "{{ Str::limit($notification->data['body'] ?? '', 60) }}"</div>
                                                <a href="{{ route('video.show', $notification->data['video_id'] ?? 0) }}"
                                                    class="text-blue-400 text-xs hover:underline">View comment</a>
                                            @else
                                                <div class="text-white font-medium truncate">You have a new notification
                                                </div>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                            @csrf
                                            <button class="ml-2 text-[#aaa] hover:text-green-400 p-1 glass-button rounded"
                                                title="Mark as read"><i class="fa-solid fa-check"></i></button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="p-6 text-[#aaa] text-center">No new notifications</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endauth
                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    @auth
                        <button @click="open = !open"
                            class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-sm font-semibold focus:outline-none shadow-lg">
                            {{ strtoupper(auth()->user()->name[0]) }}
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-80 glass-notification rounded-xl z-50" style="display: none;"
                            x-transition>
                            <!-- Current Account -->
                            <div class="p-4 border-b border-[rgba(255,255,255,0.1)]">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-xl font-bold shadow-lg">
                                        {{ strtoupper(auth()->user()->name[0]) }}</div>
                                    <div>
                                        <div class="font-semibold text-base leading-tight">{{ auth()->user()->name }}
                                        </div>
                                        <div class="text-xs text-[#aaa]">
                                            {{ '@' . (auth()->user()->username ?? Str::slug(auth()->user()->name)) }}</div>
                                    </div>
                                </div>
                                <div
                                    class="mt-2 text-xs text-blue-400 cursor-pointer hover:text-blue-300 transition-colors">
                                    View your channel</div>
                            </div>
                            <!-- Linked Accounts -->
                            @if (auth()->user()->getAllLinkedAccounts()->count() > 0)
                                <div class="border-b border-[rgba(255,255,255,0.1)]">
                                    <div class="px-4 py-2 text-xs text-[#aaa] font-medium">Switch Account</div>
                                    @foreach (auth()->user()->getAllLinkedAccounts() as $linkedUser)
                                        <form method="POST" action="{{ route('account.switch', $linkedUser) }}"
                                            class="inline-block w-full">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-left px-4 py-3 glass-notification-item flex items-center gap-3 text-white transition-all duration-200">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-sm font-bold shadow-lg">
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
                            <div class="border-b border-[rgba(255,255,255,0.1)]">
                                <a href="{{ route('account.link.form') }}"
                                    class="w-full text-left px-4 py-3 glass-notification-item flex items-center gap-3 text-white text-sm transition-all duration-200">
                                    <i class="fa-solid fa-plus w-5"></i>
                                    <span>Add another account</span>
                                </a>
                            </div>
                            <!-- Sign Out -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-3 glass-notification-item flex items-center gap-3 text-white text-base rounded-b-xl transition-all duration-200">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Sign out</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="w-8 h-8 rounded-full glass-button flex items-center justify-center text-base font-bold focus:outline-none">
                            <i class="fa-regular fa-user text-xl"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </header>
        <div class="flex">
            <!-- Unified Sidebar (Fixed Overlay for All Pages) -->
            <div>
                <!-- Backdrop for mobile/overlay sidebar -->
                <div class="fixed inset-0 glass-overlay z-40" x-show="sidebarOpen" @click="sidebarOpen = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                </div>
                <!-- Fixed sidebar for all pages -->
                <aside x-show="sidebarOpen"
                    class="fixed top-0 left-0 w-4/5 max-w-xs md:w-60 glass h-full flex flex-col pt-16 z-50 transition-all duration-200"
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
            <!-- Main Content Slot -->
            <main class="flex-1 p-2 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>
    <!-- Alpine.js script, defer loading -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function sidebarApp() {
            return {
                sidebarOpen: false, // Changed from true to false - hide by default
                isVideoPage: false,
                init() {
                    this.isVideoPage = document.body.classList.contains('video-player-page');
                    // Remove the conditional logic - all pages start with sidebar closed
                    this.sidebarOpen = false;
                }
            }
        }
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebarApp', sidebarApp);
        });
    </script>
    @php
        function notificationIcon($type)
        {
            if ($type === 'video_upload') {
                return 'fa-video text-blue-400';
            }
            if ($type === 'subscribed') {
                return 'fa-user-plus text-green-400';
            }
            if ($type === 'like') {
                return 'fa-thumbs-up text-pink-400';
            }
            if ($type === 'comment') {
                return 'fa-comment text-yellow-400';
            }
            return 'fa-bell text-gray-400';
        }
    @endphp
</body>

</html>
