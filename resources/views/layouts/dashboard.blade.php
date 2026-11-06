<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CargoTrace') }} - @yield('title', 'Admin Dashboard')</title>

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex font-sans antialiased selection:bg-indigo-500 selection:text-white bg-slate-950 overflow-hidden">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobileSidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/80 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col transition-transform duration-200 -translate-x-full md:translate-x-0">
        <!-- Sidebar Brand -->
        <div class="h-16 px-6 border-b border-slate-800/80 flex items-center justify-between shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-sky-400 flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                    CT
                </div>
                <div>
                    <span class="text-lg font-black tracking-tight text-white">CargoTrace</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 ml-1">v1.0</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Items -->
        <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
            <!-- Group: Operations -->
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Logistics & Tracking</p>
                <nav class="mt-2 space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white shadow-md shadow-indigo-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Operations Dashboard</span>
                    </a>
                    <a href="#shipments-section" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Shipments Manifest</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-800 text-slate-300">{{ isset($shipments) && method_exists($shipments, 'total') ? $shipments->total() : \App\Models\Shipment::count() }}</span>
                    </a>
                    <a href="#quick-track" onclick="document.getElementById('trackInput').focus();" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Live Quick Track</span>
                    </a>
                </nav>
            </div>

            <!-- Group: Master Data (Admin/Operator) -->
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Master Data</p>
                <nav class="mt-2 space-y-1">
                    <a href="#carriers-section" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span>Carriers & Lines</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-800 text-slate-300">{{ $carriersCount ?? 5 }}</span>
                    </a>
                    <a href="#locations-section" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span>Ports & Locations</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-slate-800 text-slate-300">{{ $locationsCount ?? 8 }}</span>
                    </a>
                </nav>
            </div>

            <!-- Group: System & API -->
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Developer & Integration</p>
                <nav class="mt-2 space-y-1">
                    <a href="{{ url('/api/v1/health') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            <span>REST API Health</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </a>
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>Public Landing Page</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar User Card & Sign Out -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60 shrink-0">
            @auth
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-bold text-xs flex items-center justify-center uppercase">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        @if(Auth::user()->isAdmin()) bg-rose-500/10 text-rose-400 border border-rose-500/20
                        @elseif(Auth::user()->isOperator()) bg-amber-500/10 text-amber-400 border border-amber-500/20
                        @else bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                        @endif">
                        {{ Auth::user()->role }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[11px] font-semibold text-rose-400 hover:text-rose-300 transition-colors">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800/80 text-center space-y-2">
                <p class="text-xs text-slate-400">Viewing as Public Guest</p>
                <a href="{{ route('login') }}" class="block w-full py-1.5 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md transition-all">
                    Sign In
                </a>
            </div>
            @endauth
        </div>
    </aside>

    <!-- MAIN CONTENT VIEWPORT -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- Top App Bar -->
        <header class="h-16 px-4 sm:px-8 border-b border-slate-800/80 bg-slate-950/80 backdrop-blur flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-slate-900 border border-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-white">@yield('page_title', 'Operations Overview')</span>
                    <span class="hidden sm:inline-block text-[11px] text-slate-500">| Logistics Visibility & Tracking</span>
                </div>
            </div>

            <!-- Top Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-900 border border-slate-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Landing Page
                </a>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
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
