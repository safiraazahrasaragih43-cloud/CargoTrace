<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream-50 text-espresso-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CargoTrace - @yield('title', 'Operations Console')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex font-sans antialiased selection:bg-apricot-500 selection:text-white bg-cream-50 text-espresso-900 overflow-hidden">

    <!-- Mobile Backdrop -->
    <div id="mobileSidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-espresso-900/30 z-40 hidden md:hidden backdrop-blur-xs"></div>

    <!-- LEFT SIDEBAR -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-68 bg-white border-r border-cream-200/80 flex flex-col transform -translate-x-full md:translate-x-0 md:static md:inset-0 transition-transform duration-300 ease-in-out shrink-0">
        <!-- Brand Header -->
        <div class="h-16 px-6 border-b border-cream-200/70 flex items-center justify-between shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                    CT
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-sage-600">Control Panel</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden text-espresso-500 hover:text-espresso-900 p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Items -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
            <!-- Group: Operations -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">Logistics &amp; Tracking</p>
                <nav class="mt-2.5 space-y-1.5">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-cream-100' }} transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Operations Dashboard</span>
                    </a>
                    <a href="{{ route('dashboard') }}#shipments-section" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Shipments Manifest</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-sage-100 text-sage-700">{{ isset($shipments) && method_exists($shipments, 'total') ? $shipments->total() : \App\Models\Shipment::count() }}</span>
                    </a>
                    <a href="{{ route('dashboard') }}#quick-track" onclick="if(document.getElementById('trackInput')) document.getElementById('trackInput').focus();" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <svg class="w-4 h-4 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Live Quick Track</span>
                    </a>
                </nav>
            </div>

            <!-- Group: Master Data -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">Master Data</p>
                <nav class="mt-2.5 space-y-1.5">
                    <a href="{{ route('dashboard') }}#carriers-section" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-apricot-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Carriers &amp; Lines</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cream-100 text-espresso-600">{{ $carriersCount ?? 5 }}</span>
                    </a>
                    <a href="{{ route('dashboard') }}#locations-section" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>Ports &amp; Locations</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cream-100 text-espresso-600">{{ $locationsCount ?? 8 }}</span>
                    </a>
                </nav>
            </div>

            <!-- Group: System & API -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">Developer &amp; Integration</p>
                <nav class="mt-2.5 space-y-1.5">
                    <a href="{{ url('/api/v1/health') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            <span>REST API Health</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                    </a>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 transition-colors">
                        <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Public Landing Page</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar User Card & Sign Out -->
        <div class="p-4 border-t border-cream-200/70 bg-cream-50/70 shrink-0">
            @auth
            <div class="p-3.5 rounded-2xl bg-white border border-cream-200 shadow-xs space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 font-extrabold text-xs flex items-center justify-center uppercase">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-espresso-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-espresso-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        @if(Auth::user()->isAdmin()) bg-apricot-100 text-apricot-600
                        @elseif(Auth::user()->isOperator()) bg-sage-100 text-sage-700
                        @else bg-cream-200 text-espresso-800
                        @endif">
                        {{ Auth::user()->role }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 transition-colors cursor-pointer">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="p-3.5 rounded-2xl bg-white border border-cream-200 text-center space-y-2.5 shadow-xs">
                <p class="text-xs font-medium text-espresso-500">Viewing as Public Guest</p>
                <a href="{{ route('login') }}" class="block w-full py-2 px-4 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md shadow-apricot-500/20 transition-all">
                    Sign In
                </a>
            </div>
            @endauth
        </div>
    </aside>

    <!-- MAIN CONTENT VIEWPORT -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top App Bar -->
        <header class="h-16 px-4 sm:px-8 border-b border-cream-200/80 bg-cream-50/90 backdrop-blur flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-espresso-600 hover:text-espresso-900 p-2 rounded-xl bg-white border border-cream-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-extrabold text-espresso-900">@yield('page_title', 'Operations Overview')</span>
                    <span class="hidden sm:inline-block text-[11px] font-medium text-espresso-400">| Logistics Visibility &amp; Tracking</span>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-2.5">
                <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-espresso-700 hover:text-espresso-900 bg-white border border-cream-200 hover:bg-cream-100 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Landing Page</span>
                </a>
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sage-100 text-sage-700 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-sage-600 animate-pulse"></span>
                    <span>System Operational</span>
                </div>
            </div>
        </header>

        <!-- Main Scrollable Body -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
            @yield('content')
        </main>
    </div>

    <!-- Toggle Sidebar JS -->
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobileSidebarBackdrop');
        const isHidden = sidebar.classList.contains('-translate-x-full');

        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }
    </script>
</body>
</html>
