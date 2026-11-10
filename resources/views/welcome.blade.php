<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream-50 text-espresso-900 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - Global Shipment Visibility & Logistics Tracking Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans antialiased selection:bg-apricot-500 selection:text-white bg-cream-50 text-espresso-900">

    <!-- Top Sticky Navigation Bar -->
    <header class="border-b border-cream-200/80 bg-cream-50/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                        <span class="hidden sm:inline-block text-[10px] uppercase px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-700 font-bold ml-1.5">v1.0</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-espresso-600">
                <a href="#tracking" class="hover:text-espresso-900 transition-colors">Live Tracking</a>
                <a href="#features" class="hover:text-espresso-900 transition-colors">Features</a>
                <a href="#roles" class="hover:text-espresso-900 transition-colors">Role Matrix</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-lg shadow-apricot-500/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard ({{ Auth::user()->name }})</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full text-xs font-bold text-espresso-800 bg-white hover:bg-cream-100 border border-cream-200 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-lg shadow-apricot-500/25 transition-all">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION (Warm Editorial & Sage Organic Showcase) -->
    <section id="tracking" class="relative overflow-hidden pt-10 pb-16 lg:pt-16 lg:pb-24">
        <!-- Soft Organic Blobs -->
        <div class="absolute -top-20 -right-24 w-[480px] h-[480px] bg-sage-200/35 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -left-24 w-[380px] h-[380px] bg-apricot-100/60 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- LEFT COLUMN: Headline, Search Bar & Sample Filter Tiles -->
                <div class="lg:col-span-7 space-y-7">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sage-100 text-sage-700 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-sage-600 animate-pulse"></span>
                        Global Logistics Visibility &middot; Laravel 12
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-espresso-900 leading-[1.12]">
                            Discover seamless visibility for your <span class="text-sage-600">global cargo</span>
                        </h1>
                        <p class="text-base sm:text-lg text-espresso-600 max-w-xl leading-relaxed">
                            Track ocean containers, air freight, and overland corridors in calm precision—powered by deterministic ETA logic and an immutable event ledger.
                        </p>
                    </div>

                    <!-- Nestopia-style Search + Sage Filter Square Bar -->
                    <form onsubmit="event.preventDefault(); heroTrack();" class="max-w-xl flex flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-espresso-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input id="heroTrackInput" type="text"
                                   placeholder="Search tracking number (e.g. CT-20261101-9821)"
                                   class="w-full pl-11 pr-4 py-3.5 bg-white border border-cream-200 rounded-2xl text-sm font-mono text-espresso-900 placeholder-espresso-400 shadow-[0_6px_20px_-6px_rgba(44,39,36,0.06)] focus:outline-none focus:ring-2 focus:ring-sage-500 transition-all">
                        </div>
                        <button type="submit" class="px-6 py-3.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white font-bold text-sm shadow-lg shadow-sage-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            <span>Track Cargo</span>
                        </button>
                    </form>

                    <!-- Category / Sample Consignment Tiles (Inspired by Nestopia icon tiles) -->
                    <div class="space-y-2.5">
                        <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Quick Sample Consignments</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl">
                            <button type="button" onclick="setHeroTrack('CT-20261101-9821')" class="group p-3 rounded-2xl bg-sage-500 text-white text-left shadow-md shadow-sage-500/20 hover:bg-sage-600 transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="text-[11px] font-bold opacity-90">In Transit</div>
                                <div class="text-[11px] font-mono font-bold truncate">CT-20261101-9821</div>
                            </button>

                            <button type="button" onclick="setHeroTrack('CT-20261105-4412')" class="group p-3 rounded-2xl bg-white hover:bg-cream-100 border border-cream-200 text-left shadow-xs transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-apricot-100 text-apricot-600 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Booked</div>
                                <div class="text-[11px] font-mono font-bold text-espresso-900 truncate">CT-20261105-4412</div>
                            </button>

                            <button type="button" onclick="setHeroTrack('CT-20261020-1109')" class="group p-3 rounded-2xl bg-white hover:bg-cream-100 border border-cream-200 text-left shadow-xs transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-sage-100 text-sage-700 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Delivered</div>
                                <div class="text-[11px] font-mono font-bold text-espresso-900 truncate">CT-20261020-1109</div>
                            </button>

                            <a href="{{ route('dashboard') }}" class="group p-3 rounded-2xl bg-white hover:bg-apricot-50 border border-cream-200 hover:border-apricot-200 text-left shadow-xs transition-all">
                                <div class="w-8 h-8 rounded-xl bg-cream-100 text-espresso-800 group-hover:bg-apricot-500 group-hover:text-white flex items-center justify-center mb-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Console</div>
                                <div class="text-[11px] font-bold text-apricot-600 truncate">Open Portal &rarr;</div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Nestopia-Inspired Sage Banner + Floating Checkpoint Showcase -->
                <div class="lg:col-span-5 space-y-5">
                    <!-- Main Sage Organic Showcase Card (like the "Get 50% OFF" rounded sage card) -->
                    <div class="relative overflow-hidden rounded-[2rem] bg-sage-500 p-7 text-white shadow-[0_20px_50px_-15px_rgba(135,152,128,0.45)]">
                        <div class="absolute -right-12 -bottom-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
                        <div class="absolute right-6 top-6 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-[11px] font-bold">
                            Live Corridor
                        </div>

                        <div class="relative z-10 space-y-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-sage-100">Featured Route</p>
                            <h2 class="text-3xl font-extrabold leading-tight">
                                Jakarta &rarr; Rotterdam<br>
                                <span class="text-sage-100 font-semibold text-xl">Ocean Freight Express</span>
                            </h2>

                            <!-- 360 Orbital Route Pedestal Ring (Inspired by Center Lounge Chair Card) -->
                            <div class="my-4 py-5 px-4 rounded-3xl bg-cream-50 text-espresso-900 shadow-inner relative">
                                <div class="flex items-center justify-between text-xs mb-3">
                                    <span class="font-bold text-apricot-600">Maersk Line &bull; MAEU</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-700 font-bold text-[10px]">IN_TRANSIT</span>
                                </div>

                                <!-- Orbital Ellipse Graphic -->
                                <div class="relative flex items-center justify-between py-4 px-2">
                                    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 h-10 border border-apricot-400/50 rounded-[100%] pointer-events-none"></div>
                                    <div class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        IDTPP &middot; Origin
                                    </div>
                                    <div class="relative z-10 w-9 h-9 rounded-full bg-apricot-500 text-white flex items-center justify-center shadow-md shadow-apricot-500/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                    <div class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        SGSIN &middot; Hub
                                    </div>
                                </div>

                                <div class="mt-2 pt-3 border-t border-cream-200 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-espresso-500 font-semibold uppercase">Sample Consignment</p>
                                        <p class="text-sm font-extrabold font-mono text-espresso-900">CT-20261101-9821</p>
                                    </div>
                                    <button type="button" onclick="setHeroTrack('CT-20261101-9821')" class="px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md shadow-apricot-500/25 transition-all cursor-pointer">
                                        Inspect Live
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mini KPI Pill Row -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-espresso-900">100%</p>
                            <p class="text-[11px] font-medium text-espresso-500">State Integrity</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-sage-600">3 Roles</p>
                            <p class="text-[11px] font-medium text-espresso-500">RBAC Access</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-apricot-600">REST v1</p>
                            <p class="text-[11px] font-medium text-espresso-500">Sanctum API</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Live Tracking Result Container -->
            <div id="heroResultModal" class="hidden mt-10 text-left p-6 sm:p-8 rounded-3xl bg-white border border-cream-200 shadow-[0_20px_50px_-15px_rgba(44,39,36,0.12)] space-y-5">
                <div class="flex items-center justify-between border-b border-cream-200 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-sage-500 animate-ping"></span>
                        <span class="text-base font-extrabold text-espresso-900">Live Consignment Tracking Detail</span>
                    </div>
                    <button onclick="document.getElementById('heroResultModal').classList.add('hidden')" class="px-3 py-1 rounded-full bg-cream-100 hover:bg-cream-200 text-xs font-bold text-espresso-600 transition-colors cursor-pointer">&times; Close</button>
                </div>
                <div id="heroResultContent"></div>
            </div>
        </div>
    </section>

    <!-- CORE FEATURES SECTION -->
    <section id="features" class="py-16 lg:py-20 border-t border-cream-200/80 bg-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3 py-1 rounded-full bg-apricot-100 text-apricot-600 text-[11px] font-bold uppercase tracking-wider">Engineered for Excellence</span>
                <p class="text-3xl font-extrabold text-espresso-900">Mission-Critical Supply Chain Features</p>
                <p class="text-sm text-espresso-600">Built on clean Service-Layer architecture, deterministic rules, and verifiable security.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-sage-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Strict State Transitions</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Deterministic lifecycle states (CREATED &rarr; BOOKED &rarr; PICKED_UP &rarr; IN_TRANSIT &rarr; DELIVERED). Terminal states cannot be reversed, enforced in the Service layer.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-apricot-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Append-Only Timeline Ledger</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Every container milestone is logged with exact timestamps and GPS coordinates. Checkpoints are immutable to preserve full chain-of-custody integrity.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-sage-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-cream-100 text-espresso-800 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Role-Based Access Control</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Admin, Logistics Operator, and Customer roles with dedicated Laravel Policies. Customers are strictly scoped to inspect only their authorized consignments.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ARCHITECTURE & ROLE MATRIX SECTION -->
    <section id="roles" class="py-16 lg:py-20 border-t border-cream-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold uppercase tracking-wider">Security &amp; Permissions</span>
                <p class="text-3xl font-extrabold text-espresso-900">Enterprise Role Matrix</p>
                <p class="text-sm text-espresso-600">Strict authorization enforced at both HTTP Middleware and Policy layers.</p>
            </div>

            <div class="rounded-3xl bg-white border border-cream-200 overflow-hidden shadow-[0_16px_40px_-14px_rgba(44,39,36,0.08)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                        <thead class="bg-sage-50 text-espresso-600 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Capability / Resource</th>
                                <th class="px-6 py-4 text-center">Administrator</th>
                                <th class="px-6 py-4 text-center">Logistics Operator</th>
                                <th class="px-6 py-4 text-center">Customer</th>
                                <th class="px-6 py-4 text-center">Public (Guest)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-100 text-espresso-800">
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">User Management &amp; System RBAC</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Full Access</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Carrier &amp; Port Locations Master Data</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Full CRUD</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; View / Update</span></td>
                                <td class="px-6 py-4 text-center text-espresso-500">&#10003; View Only</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Shipment Booking &amp; Creation</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Create Any</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Create Any</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Shipment Timeline &amp; Visibility</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; All Shipments</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; All Shipments</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-bold">&#10003; Own Only</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; Public Track #</span></td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Event &amp; Checkpoint Logging</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Permitted</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Permitted</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Denied</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-cream-200 bg-cream-100/60 py-10 text-espresso-500 text-xs mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-xs">CT</div>
                <span class="text-espresso-900 font-extrabold">CargoTrace Visibility Platform</span>
                <span>&copy; {{ date('Y') }} UAS Pemrograman Web</span>
            </div>
            <div class="flex items-center gap-6 font-semibold">
                <a href="{{ route('login') }}" class="hover:text-espresso-900 transition-colors">Sign In</a>
                <a href="{{ route('register') }}" class="hover:text-espresso-900 transition-colors">Register</a>
                <a href="{{ url('/api/v1/health') }}" target="_blank" class="hover:text-espresso-900 transition-colors font-mono">API Health</a>
            </div>
        </div>
    </footer>

    <!-- Script for Hero Live Tracking -->
    <script>
    function setHeroTrack(trackingNo) {
        document.getElementById('heroTrackInput').value = trackingNo;
        heroTrack();
    }

    async function heroTrack() {
        const input = document.getElementById('heroTrackInput').value.trim();
        if (!input) return;

        const modal = document.getElementById('heroResultModal');
        const content = document.getElementById('heroResultContent');
        modal.classList.remove('hidden');
        content.innerHTML = '<p class="text-espresso-500 text-sm">Querying real-time shipment manifest...</p>';

        try {
            const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
            const data = await res.json();

            if (res.ok && data.success) {
                const s = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-cream-50 p-4 rounded-2xl border border-cream-200">
                            <div>
                                <span class="text-xs text-espresso-500 font-semibold">Tracking Reference:</span>
                                <p class="text-lg font-extrabold font-mono text-sage-700">${s.tracking_number}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    ${s.status === 'DELIVERED' ? 'bg-sage-100 text-sage-700' :
                                      (s.status === 'IN_TRANSIT' ? 'bg-sage-500 text-white' :
                                      'bg-apricot-100 text-apricot-600')}">
                                    ${s.status}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Assigned Carrier</span>
                                <strong class="text-espresso-900">${s.carrier ? s.carrier.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Port of Origin</span>
                                <strong class="text-espresso-900">${s.origin ? s.origin.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Destination Port</span>
                                <strong class="text-espresso-900">${s.destination ? s.destination.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Est. Delivery</span>
                                <strong class="text-espresso-900">${s.estimated_delivery_at ? new Date(s.estimated_delivery_at).toLocaleDateString() : 'Pending'}</strong>
                            </div>
                        </div>

                        ${s.items && s.items.length ? `
                            <div class="p-4 rounded-2xl bg-cream-50 border border-cream-200 space-y-2">
                                <span class="text-xs font-bold text-espresso-600 uppercase">Itemized Manifest (${s.items.length} items)</span>
                                <ul class="divide-y divide-cream-200 text-xs">
                                    ${s.items.map(it => `
                                        <li class="py-1.5 flex justify-between">
                                            <span class="text-espresso-900 font-medium">${it.description} &times; ${it.quantity}</span>
                                            <span class="font-mono text-espresso-600">${it.weight_kg} kg | $${Number(it.value_amount).toLocaleString()}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        ` : ''}

                        <div class="pt-2 flex justify-end">
                            <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all">
                                <span>View Full Shipment Timeline</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `<p class="text-rose-600 font-medium text-sm">${data.message || 'Consignment not found for tracking number.'}</p>`;
            }
        } catch (e) {
            content.innerHTML = `<p class="text-rose-600 font-medium text-sm">Failed to query tracking data. Check connection.</p>`;
        }
    }
    </script>
</body>
</html>
