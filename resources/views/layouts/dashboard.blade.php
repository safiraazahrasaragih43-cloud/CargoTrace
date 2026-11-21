@php
    $user = auth()->user();
    $isStaffConsole = $user && ($user->isAdmin() || $user->isOperator());
    $totalShipmentsCount = $user && $user->isCustomer()
        ? \App\Models\Shipment::where('customer_id', $user->id)->count()
        : \App\Models\Shipment::count();
    $totalCarriersCount = $carriersCount ?? \App\Models\Carrier::count();
    $totalLocationsCount = $locationsCount ?? \App\Models\Location::count();
    $unreadNotifCount = $user
        ? \App\Models\AppNotification::where('user_id', $user->id)->whereNull('read_at')->count()
        : 0;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream-50 text-espresso-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CargoTrace - @yield('title', $isStaffConsole ? __('Konsol Operasional') : __('Portal Pelacakan Kargo'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="{{ $isStaffConsole ? 'h-full flex overflow-hidden' : 'min-h-full flex flex-col overflow-x-hidden' }} font-sans antialiased selection:bg-apricot-500 selection:text-white bg-cream-50 text-espresso-900 relative">

    <!-- Ambient Soft Glassmorphism Background Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-24 right-10 w-[440px] h-[440px] rounded-full bg-sage-200/45 blur-[100px]"></div>
        <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] rounded-full bg-apricot-200/35 blur-[100px]"></div>
    </div>

    @if($isStaffConsole)
        <!-- ========================================================= -->
        <!-- ADMIN & STAFF CONSOLE LAYOUT (GLASS SIDEBAR + TOP BAR)    -->
        <!-- ========================================================= -->
        <div id="mobileSidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-espresso-900/30 z-40 hidden md:hidden backdrop-blur-xs"></div>

        <!-- LEFT GLASS SIDEBAR (ADMIN & STAFF CONSOLE) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar flex flex-col transform -translate-x-full md:translate-x-0 md:static md:inset-0 transition-transform duration-300 ease-in-out shrink-0">
            <!-- Brand Header -->
            <div class="h-16 px-6 border-b border-white/70 flex items-center justify-between shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-base font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-sage-600">
                            {{ $user->isAdmin() ? __('Konsol Admin') : __('Konsol Staff') }}
                        </span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="md:hidden text-espresso-500 hover:text-espresso-900 p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Sidebar Navigation Items -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
                <!-- Group 1: Operasional & Pelacakan -->
                <div>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">{{ __('Operasional') }} &amp; {{ __('Pelacakan Publik') }}</p>
                    <nav class="mt-2.5 space-y-1.5">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>{{ __('Dashboard Operasional') }}</span>
                        </a>
                        <a href="{{ route('shipments.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('shipments.*') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('shipments.*') ? 'text-white' : 'text-sage-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>{{ __('Daftar Pengiriman') }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ request()->routeIs('shipments.*') ? 'bg-white/25 text-white' : 'bg-sage-100 text-sage-700' }}">{{ $totalShipmentsCount }}</span>
                        </a>
                        <a href="{{ route('tracking.page') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('tracking.page') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <svg class="w-4 h-4 {{ request()->routeIs('tracking.page') ? 'text-white' : 'text-sage-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>{{ __('Lacak Kargo Cepat') }}</span>
                        </a>
                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('notifications.*') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('notifications.*') ? 'text-white' : 'text-apricot-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span>{{ __('Notifikasi Sistem') }}</span>
                            </div>
                            @if($unreadNotifCount > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ request()->routeIs('notifications.*') ? 'bg-white/25 text-white' : 'bg-rose-500 text-white' }}">{{ $unreadNotifCount }}</span>
                            @endif
                        </a>
                    </nav>
                </div>

                <!-- Group 2: Master Data & Staff Operations -->
                <div>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">{{ __('Data Master & Rute') }}</p>
                    <nav class="mt-2.5 space-y-1.5">
                        @if($user->can('viewAny', \App\Models\Carrier::class))
                            <a href="{{ route('carriers.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('carriers.*') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ request()->routeIs('carriers.*') ? 'text-white' : 'text-apricot-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>{{ __('Ekspedisi & Maskapai') }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ request()->routeIs('carriers.*') ? 'bg-white/25 text-white' : 'bg-white/80 text-espresso-600' }}">{{ $totalCarriersCount }}</span>
                            </a>
                        @endif
                        <a href="{{ route('locations.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('locations.*') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ request()->routeIs('locations.*') ? 'text-white' : 'text-sage-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span>{{ __('Pelabuhan & Lokasi') }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ request()->routeIs('locations.*') ? 'bg-white/25 text-white' : 'bg-white/80 text-espresso-600' }}">{{ $totalLocationsCount }}</span>
                        </a>
                    </nav>
                </div>

                <!-- Group 3: Administrasi Sistem (Admin Only) -->
                @if($user->isAdmin())
                <div>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">{{ __('Administrasi Sistem') }}</p>
                    <nav class="mt-2.5 space-y-1.5">
                        <a href="{{ route('audit_logs.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold {{ request()->routeIs('audit_logs.*') ? 'bg-apricot-500 text-white shadow-md shadow-apricot-500/25' : 'text-espresso-600 hover:text-espresso-900 hover:bg-white/70' }} transition-all">
                            <svg class="w-4 h-4 {{ request()->routeIs('audit_logs.*') ? 'text-white' : 'text-espresso-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>{{ __('Log Audit Sistem') }}</span>
                        </a>
                    </nav>
                </div>
                @endif

                <!-- Group 4: System & API -->
                <div>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-espresso-400">{{ __('Sistem & Integrasi') }}</p>
                    <nav class="mt-2.5 space-y-1.5">
                        <a href="{{ url('/api/v1/health') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-white/70 transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>{{ __('Status REST API') }}</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                        </a>
                        <a href="{{ url('/') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-semibold text-espresso-600 hover:text-espresso-900 hover:bg-white/70 transition-colors">
                            <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>{{ __('Halaman Depan') }}</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Sidebar User Card & Sign Out -->
            <div class="p-4 border-t border-white/70 bg-white/40 shrink-0">
                <div class="p-3.5 rounded-2xl bg-white/80 backdrop-blur-md border border-white shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 font-extrabold text-xs flex items-center justify-center uppercase shrink-0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-espresso-900 truncate">{{ $user->name }}</p>
                                <p class="text-[10px] text-espresso-500 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-cream-200/60">
                        @include('components.language-switcher')

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700 transition-colors cursor-pointer">
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT VIEWPORT (ADMIN & STAFF) -->
        <div class="relative z-10 flex-1 flex flex-col min-w-0 h-full overflow-hidden">
            <header class="h-16 px-4 sm:px-8 border-b border-white/80 bg-white/55 backdrop-blur-xl flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="md:hidden text-espresso-600 hover:text-espresso-900 p-2 rounded-xl bg-white/80 border border-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-extrabold text-espresso-900">@yield('page_title', __('Ringkasan Operasional'))</span>
                        <span class="hidden sm:inline-block text-[11px] font-medium text-espresso-400">| {{ __('Sistem Visibilitas & Pelacakan') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    @include('components.language-switcher')

                    <a href="{{ route('notifications.index') }}" title="{{ __('Notifikasi') }}" class="relative p-2 rounded-full bg-white/80 hover:bg-white border border-white text-espresso-700 transition-colors shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($unreadNotifCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-bold flex items-center justify-center">{{ $unreadNotifCount }}</span>
                        @endif
                    </a>
                    <a href="{{ url('/') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-espresso-700 hover:text-espresso-900 bg-white/80 border border-white hover:bg-white transition-colors shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span>{{ __('Halaman Depan') }}</span>
                    </a>
                    <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sage-100/90 text-sage-700 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-sage-600 animate-pulse"></span>
                        <span>{{ __('Sistem Normal') }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6">
                @yield('content')
            </main>
        </div>

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
    @else
        <!-- ========================================================= -->
        <!-- CUSTOMER / PUBLIC PORTAL LAYOUT (GLASS TOPBAR, NO SIDEBAR)-->
        <!-- ========================================================= -->
        <header id="customerTopbar" class="sticky top-0 z-50 border-b border-white/80 bg-white/65 backdrop-blur-2xl shadow-[0_6px_26px_-10px_rgba(44,39,36,0.08)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between gap-4">
                <!-- Brand -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                            CT
                        </div>
                        <div>
                            <span class="text-base font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                            <span class="block text-[10px] font-bold uppercase tracking-widest text-sage-600">
                                {{ auth()->check() ? __('Portal Klien') : __('Pelacakan Publik') }}
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center Horizontal Topbar Navigation (Desktop) -->
                <nav class="hidden md:flex items-center gap-1.5 bg-cream-100/75 p-1.5 rounded-full border border-white">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            {{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('shipments.index') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('shipments.*') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            <span>{{ __('Pengiriman Saya') }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ request()->routeIs('shipments.*') ? 'bg-white/25 text-white' : 'bg-sage-100 text-sage-700' }}">{{ $totalShipmentsCount }}</span>
                        </a>
                        <a href="{{ route('tracking.page') }}" class="px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('tracking.page') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            {{ __('Lacak Kargo') }}
                        </a>
                        <a href="{{ route('locations.index') }}" class="px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('locations.*') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            {{ __('Pelabuhan & Hub') }}
                        </a>
                        <a href="{{ route('notifications.index') }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('notifications.*') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            <span>{{ __('Notifikasi') }}</span>
                            @if($unreadNotifCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold {{ request()->routeIs('notifications.*') ? 'bg-white/25 text-white' : 'bg-rose-500 text-white' }}">{{ $unreadNotifCount }}</span>
                            @endif
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="px-4 py-1.5 rounded-full text-xs font-bold text-espresso-700 hover:text-espresso-900 hover:bg-white/80 transition-all">
                            {{ __('Beranda') }}
                        </a>
                        <a href="{{ route('tracking.page') }}" class="px-4 py-1.5 rounded-full text-xs font-bold {{ request()->routeIs('tracking.page') ? 'bg-apricot-500 text-white shadow-sm' : 'text-espresso-700 hover:text-espresso-900 hover:bg-white/80' }} transition-all">
                            {{ __('Lacak Kargo Cepat') }}
                        </a>
                    @endauth
                </nav>

                <!-- Right Profile & Actions -->
                <div class="flex items-center gap-2.5 shrink-0">
                    @include('components.language-switcher')

                    @auth
                        <a href="{{ url('/') }}" class="hidden lg:inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold text-espresso-700 bg-white/80 hover:bg-white border border-white transition-colors">
                            {{ __('Beranda') }}
                        </a>

                        <div class="hidden sm:flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full bg-white/80 border border-white shadow-xs">
                            <div class="w-7 h-7 rounded-full bg-sage-500 text-white font-extrabold text-[11px] flex items-center justify-center uppercase">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span class="text-xs font-bold text-espresso-900 max-w-[120px] truncate">{{ $user->name }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 rounded-full bg-rose-50 hover:bg-rose-100 border border-rose-200/70 text-[11px] font-bold text-rose-600 transition-colors cursor-pointer">
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-full text-xs font-bold text-espresso-800 bg-white/80 hover:bg-white border border-white shadow-xs transition-colors">
                            {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-md shadow-apricot-500/20 transition-all">
                            {{ __('Daftar Akun') }}
                        </a>
                    @endauth

                    <!-- Mobile Menu Toggle -->
                    <button type="button" onclick="toggleTopbarMenu()" class="md:hidden p-2 rounded-xl bg-white/80 border border-white text-espresso-700 hover:text-espresso-900 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Responsive Topbar Drawer -->
            <div id="customerMobileMenu" class="hidden md:hidden border-t border-white/70 bg-white/85 backdrop-blur-xl px-4 py-3 space-y-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-2xl text-xs font-bold {{ request()->routeIs('dashboard') ? 'bg-apricot-500 text-white' : 'text-espresso-800 hover:bg-cream-100' }}">{{ __('Dashboard') }}</a>
                    <a href="{{ route('shipments.index') }}" class="flex items-center justify-between px-4 py-2 rounded-2xl text-xs font-bold {{ request()->routeIs('shipments.*') ? 'bg-apricot-500 text-white' : 'text-espresso-800 hover:bg-cream-100' }}">
                        <span>{{ __('Pengiriman Saya') }}</span>
                        <span class="font-mono text-[10px]">{{ $totalShipmentsCount }}</span>
                    </a>
                    <a href="{{ route('tracking.page') }}" class="block px-4 py-2 rounded-2xl text-xs font-bold {{ request()->routeIs('tracking.page') ? 'bg-apricot-500 text-white' : 'text-espresso-800 hover:bg-cream-100' }}">{{ __('Lacak Kargo') }}</a>
                    <a href="{{ route('locations.index') }}" class="block px-4 py-2 rounded-2xl text-xs font-bold {{ request()->routeIs('locations.*') ? 'bg-apricot-500 text-white' : 'text-espresso-800 hover:bg-cream-100' }}">{{ __('Pelabuhan & Hub') }}</a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2 rounded-2xl text-xs font-bold {{ request()->routeIs('notifications.*') ? 'bg-apricot-500 text-white' : 'text-espresso-800 hover:bg-cream-100' }}">
                        <span>{{ __('Notifikasi') }}</span>
                        @if($unreadNotifCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold">{{ $unreadNotifCount }}</span>
                        @endif
                    </a>
                    <div class="pt-2 border-t border-cream-200 flex items-center justify-between px-2">
                        <span class="text-xs font-bold text-espresso-700">{{ $user->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full bg-rose-50 text-xs font-bold text-rose-600 cursor-pointer">{{ __('Keluar') }}</button>
                        </form>
                    </div>
                @else
                    <a href="{{ url('/') }}" class="block px-4 py-2 rounded-2xl text-xs font-bold text-espresso-800 hover:bg-cream-100">{{ __('Beranda') }}</a>
                    <a href="{{ route('tracking.page') }}" class="block px-4 py-2 rounded-2xl text-xs font-bold text-espresso-800 hover:bg-cream-100">{{ __('Lacak Kargo Cepat') }}</a>
                @endauth
            </div>
        </header>

        <!-- Main Topbar Page Body -->
        <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-6">
            @yield('content')
        </main>

        <script>
        function toggleTopbarMenu() {
            const menu = document.getElementById('customerMobileMenu');
            if (menu) menu.classList.toggle('hidden');
        }
        </script>
    @endif
</body>
</html>
