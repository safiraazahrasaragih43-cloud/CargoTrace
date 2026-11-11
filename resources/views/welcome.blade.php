<!DOCTYPE html>
<html lang="id" class="h-full bg-cream-50 text-espresso-900 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - Platform Visibilitas &amp; Pelacakan Pengiriman Global</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-full flex flex-col font-sans antialiased selection:bg-apricot-500 selection:text-white bg-cream-50 text-espresso-900 overflow-x-hidden">

    <!-- Ambient Soft Glassmorphism Background Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -right-32 w-[560px] h-[560px] rounded-full bg-sage-200/50 blur-[110px]"></div>
        <div class="absolute top-1/3 -left-32 w-[480px] h-[480px] rounded-full bg-apricot-200/45 blur-[110px]"></div>
        <div class="absolute -bottom-32 right-1/4 w-[460px] h-[460px] rounded-full bg-sage-100/70 blur-[110px]"></div>
    </div>

    <!-- Top Sticky Glass Navigation Bar -->
    <header class="sticky top-0 z-50 border-b border-white/80 bg-white/65 backdrop-blur-2xl shadow-[0_4px_24px_-8px_rgba(44,39,36,0.06)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-base shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                        <span class="hidden sm:inline-block text-[10px] uppercase px-2.5 py-0.5 rounded-full bg-sage-100/90 text-sage-700 font-bold ml-1.5">Global v1.0</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-espresso-600">
                <a href="#tracking" class="hover:text-espresso-900 transition-colors">Lacak Langsung</a>
                <a href="#bento-features" class="hover:text-espresso-900 transition-colors">Bento Arsitektur</a>
                <a href="#roles" class="hover:text-espresso-900 transition-colors">Matriks Peran</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-lg shadow-apricot-500/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard ({{ Auth::user()->name }})</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full text-xs font-bold text-espresso-800 bg-white/80 hover:bg-white border border-white shadow-xs transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-lg shadow-apricot-500/25 transition-all">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION WITH INTERACTIVE ROUTE SELECTION -->
    <section id="tracking" class="relative z-10 pt-10 pb-14 lg:pt-16 lg:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                <!-- LEFT: Headline + Glass Search Bar + Hero Corridor Selection -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-card text-sage-700 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-sage-600 animate-pulse"></span>
                        Platform Rantai Pasok Dunia &middot; Laravel 12 + Glass UI
                    </div>

                    <div class="space-y-3.5">
                        <h1 class="text-4xl sm:text-5xl lg:text-[3.35rem] font-extrabold tracking-tight text-espresso-900 leading-[1.1]">
                            Visibilitas cerdas untuk <span class="text-sage-600">kargo global</span> Anda
                        </h1>
                        <p class="text-base sm:text-lg text-espresso-600 max-w-xl leading-relaxed">
                            Pantau rute Asia Tenggara&ndash;Eropa, Asia&ndash;Amerika Utara, hingga Australia secara real-time dengan aturan ETA deterministik, peta interaktif, dan manajemen kendala terpusat.
                        </p>
                    </div>

                    <!-- Glassmorphic Search Tracking Bar -->
                    <form onsubmit="event.preventDefault(); heroTrack();" class="max-w-xl p-2 rounded-3xl glass-card-strong flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-espresso-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input id="heroTrackInput" type="text"
                                   placeholder="Masukkan nomor resi (cth: CT-20261101-9821)"
                                   class="w-full pl-11 pr-4 py-3 bg-white/70 border border-cream-200/80 rounded-2xl text-sm font-mono text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 transition-all">
                        </div>
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white font-bold text-sm shadow-lg shadow-sage-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                            <span>Lacak Kargo</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <!-- HERO SELECTION: 3 Global Corridors Interactive Selector -->
                    <div class="space-y-2.5 pt-1">
                        <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Pilih Skenario Rute Dunia (Hero Selection)</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-2xl">
                            <button type="button" onclick="selectHeroCorridor('europe')" id="heroCard-europe"
                                    class="hero-selector-card p-3.5 rounded-3xl text-left transition-all cursor-pointer glass-sage text-white">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-full bg-white/20 text-[10px] font-bold">Batas 12j</span>
                                    <span class="font-mono text-[10px] opacity-90">IDTPP &rarr; NLRTM</span>
                                </div>
                                <div class="text-xs font-extrabold">SEA &rarr; Eropa</div>
                                <div class="text-[11px] font-mono opacity-90 mt-0.5 truncate">CT-20261101-9821</div>
                            </button>

                            <button type="button" onclick="selectHeroCorridor('america')" id="heroCard-america"
                                    class="hero-selector-card p-3.5 rounded-3xl text-left transition-all cursor-pointer glass-card hover:bg-white/90">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-full bg-apricot-100 text-apricot-600 text-[10px] font-bold">Batas 18j</span>
                                    <span class="font-mono text-[10px] text-espresso-500">CNSHA &rarr; USLAX</span>
                                </div>
                                <div class="text-xs font-extrabold text-espresso-900">Asia &rarr; Amerika</div>
                                <div class="text-[11px] font-mono text-sage-700 mt-0.5 truncate">CT-20261105-4412</div>
                            </button>

                            <button type="button" onclick="selectHeroCorridor('australia')" id="heroCard-australia"
                                    class="hero-selector-card p-3.5 rounded-3xl text-left transition-all cursor-pointer glass-card hover:bg-white/90">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="px-2 py-0.5 rounded-full bg-sage-100 text-sage-700 text-[10px] font-bold">Batas 24j</span>
                                    <span class="font-mono text-[10px] text-espresso-500">SGSIN &rarr; AUSYD</span>
                                </div>
                                <div class="text-xs font-extrabold text-espresso-900">SEA &rarr; Australia</div>
                                <div class="text-[11px] font-mono text-sage-700 mt-0.5 truncate">CT-20261020-1109</div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Dynamic Glass Showcase Card driven by Hero Selection -->
                <div class="lg:col-span-5">
                    <div id="heroShowcaseCard" class="relative overflow-hidden rounded-[2.25rem] glass-sage p-7 text-white transition-all">
                        <div class="absolute -right-12 -bottom-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
                        <div id="heroShowcaseBadge" class="absolute right-6 top-6 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-bold">
                            Koridor Eropa
                        </div>

                        <div class="relative z-10 space-y-4">
                            <p id="heroShowcaseSubtitle" class="text-xs font-bold uppercase tracking-widest text-sage-100">Rute Terpilih</p>
                            <h2 id="heroShowcaseTitle" class="text-3xl font-extrabold leading-tight">
                                Jakarta &rarr; Rotterdam<br>
                                <span class="text-sage-100 font-semibold text-lg">Fokus: Customs Hold &amp; On-Time</span>
                            </h2>

                            <!-- Frosted White Glass Inner Orbital Preview -->
                            <div class="my-4 py-5 px-4 rounded-3xl bg-white/85 backdrop-blur-xl border border-white text-espresso-900 shadow-lg relative">
                                <div class="flex items-center justify-between text-xs mb-3">
                                    <span id="heroShowcaseCarrier" class="font-bold text-apricot-600">Maersk Line &bull; MAEU</span>
                                    <span id="heroShowcaseStatus" class="px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-700 font-bold text-[10px]">IN_TRANSIT</span>
                                </div>

                                <div class="relative flex items-center justify-between py-4 px-2">
                                    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 h-10 border border-apricot-400/60 rounded-[100%] pointer-events-none"></div>
                                    <div id="heroShowcaseOrigin" class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        IDTPP &middot; Asal
                                    </div>
                                    <div class="relative z-10 w-9 h-9 rounded-full bg-apricot-500 text-white flex items-center justify-center shadow-md shadow-apricot-500/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                    <div id="heroShowcaseDest" class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        NLRTM &middot; Tujuan
                                    </div>
                                </div>

                                <div class="mt-2 pt-3 border-t border-cream-200 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-espresso-500 font-semibold uppercase">Nomor Resi Aktif</p>
                                        <p id="heroShowcaseTracking" class="text-sm font-extrabold font-mono text-espresso-900">CT-20261101-9821</p>
                                    </div>
                                    <button type="button" onclick="inspectSelectedHero()" class="px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md shadow-apricot-500/25 transition-all cursor-pointer">
                                        Periksa Langsung
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Live Tracking Result Container (Glass Drawer) -->
            <div id="heroResultModal" class="hidden mt-8 text-left p-6 sm:p-8 rounded-3xl glass-card-strong space-y-5">
                <div class="flex items-center justify-between border-b border-cream-200 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-sage-500 animate-ping"></span>
                        <span class="text-base font-extrabold text-espresso-900">Detail Pelacakan Pengiriman Langsung</span>
                    </div>
                    <button onclick="document.getElementById('heroResultModal').classList.add('hidden')" class="px-3 py-1 rounded-full bg-cream-100 hover:bg-cream-200 text-xs font-bold text-espresso-600 transition-colors cursor-pointer">&times; Tutup</button>
                </div>
                <div id="heroResultContent"></div>
            </div>
        </div>
    </section>

    <!-- BENTO GRID SHOWCASE SECTION -->
    <section id="bento-features" class="relative z-10 py-14 lg:py-20 border-t border-white/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3.5 py-1 rounded-full bg-apricot-100/90 text-apricot-600 text-[11px] font-bold uppercase tracking-wider">Bento Grid System Overview</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-espresso-900">Arsitektur Modern &amp; Fitur Wajib Terpadu</h2>
                <p class="text-sm text-espresso-600">Dirancang dengan Service Layer Pattern, Glassmorphism UI, dan integrasi peta dunia secara utuh.</p>
            </div>

            <!-- 12-COLUMN BENTO GRID -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <!-- BENTO 1 (Large 7 cols): Global Corridors & Interactive Map -->
                <div class="md:col-span-7 md:row-span-2 p-7 rounded-[2rem] glass-card-strong flex flex-col justify-between space-y-6 relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-sage-200/40 blur-2xl pointer-events-none"></div>
                    <div class="space-y-3 relative z-10">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold">Rute Dunia Terpadu</span>
                            <span class="font-mono text-[11px] text-espresso-500">Leaflet.js + OpenStreetMap</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-espresso-900">Pemetaan Multi-Koridor Lintas Benua</h3>
                        <p class="text-xs text-espresso-600 leading-relaxed max-w-lg">
                            Visualisasi posisi kontainer dari Tanjung Priok, Singapura, Shanghai hingga Rotterdam, Los Angeles, dan Sydney lengkap dengan garis rute, popup koordinat, serta kondisi cuaca lokasi real-time.
                        </p>
                    </div>

                    <!-- Mini Interactive Bento Corridor Preview Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 relative z-10">
                        <div class="p-4 rounded-2xl bg-white/75 border border-white shadow-xs space-y-1.5">
                            <span class="text-[10px] font-bold text-sage-700 uppercase">SEA &rarr; Eropa</span>
                            <p class="text-sm font-extrabold text-espresso-900">Batas 12 Jam</p>
                            <p class="text-[11px] text-espresso-500">Customs Hold &amp; Ketepatan Tiba</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/75 border border-white shadow-xs space-y-1.5">
                            <span class="text-[10px] font-bold text-apricot-600 uppercase">Asia &rarr; Amerika</span>
                            <p class="text-sm font-extrabold text-espresso-900">Batas 18 Jam</p>
                            <p class="text-[11px] text-espresso-500">Weather Delay &amp; Rata Delay</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/75 border border-white shadow-xs space-y-1.5">
                            <span class="text-[10px] font-bold text-espresso-800 uppercase">SEA &rarr; Australia</span>
                            <p class="text-sm font-extrabold text-espresso-900">Batas 24 Jam</p>
                            <p class="text-[11px] text-espresso-500">Doc Issue &amp; Waktu Solusi</p>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-between relative z-10 border-t border-cream-200/70">
                        <span class="text-xs font-semibold text-espresso-600">Dilengkapi analitik Chart.js langsung dari MySQL</span>
                        <a href="{{ route('tracking.page') }}" class="px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md transition-all">
                            Buka Peta &amp; Lacak &rarr;
                        </a>
                    </div>
                </div>

                <!-- BENTO 2 (5 cols): Deterministic ETA & Severity Tiers -->
                <div class="md:col-span-5 p-6 rounded-[2rem] glass-sage text-white space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-white/20 text-[11px] font-bold">EtaService Engine</span>
                        <svg class="w-5 h-5 text-sage-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold">Aturan Keterlambatan Deterministik</h3>
                    <div class="grid grid-cols-3 gap-2 text-center text-[11px]">
                        <div class="p-2.5 rounded-2xl bg-white/15 backdrop-blur-xs">
                            <div class="font-extrabold">0–6 jam</div>
                            <div class="text-sage-100 text-[10px]">LOW</div>
                        </div>
                        <div class="p-2.5 rounded-2xl bg-white/20 backdrop-blur-xs">
                            <div class="font-extrabold">&gt;6–24 jam</div>
                            <div class="text-sage-100 text-[10px]">MEDIUM</div>
                        </div>
                        <div class="p-2.5 rounded-2xl bg-apricot-500/90 backdrop-blur-xs shadow-sm">
                            <div class="font-extrabold">&gt;24 jam</div>
                            <div class="text-white text-[10px]">HIGH</div>
                        </div>
                    </div>
                </div>

                <!-- BENTO 3 (5 cols): Strict Lifecycle State Machine -->
                <div class="md:col-span-5 p-6 rounded-[2rem] glass-card-strong space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full bg-apricot-100 text-apricot-600 text-[11px] font-bold">Validasi Status Ketat</span>
                        <span class="font-mono text-[10px] text-espresso-500">Service Layer</span>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Siklus Pengiriman Anti-Mundur</h3>
                    <div class="flex items-center gap-1.5 flex-wrap text-[10px] font-mono font-bold">
                        <span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800">CREATED</span>
                        <span class="text-espresso-400">&rarr;</span>
                        <span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600">BOOKED</span>
                        <span class="text-espresso-400">&rarr;</span>
                        <span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700">PICKED_UP</span>
                        <span class="text-espresso-400">&rarr;</span>
                        <span class="px-2.5 py-1 rounded-full bg-sage-500 text-white">IN_TRANSIT</span>
                        <span class="text-espresso-400">&rarr;</span>
                        <span class="px-2.5 py-1 rounded-full bg-espresso-900 text-white">DELIVERED</span>
                    </div>
                </div>

                <!-- BENTO 4 (4 cols): External HTTP APIs -->
                <div class="md:col-span-4 p-6 rounded-[2rem] glass-card space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-espresso-900">Weather &amp; Geocoding API</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Integrasi Laravel HTTP Client dengan Open-Meteo &amp; Nominatim lengkap dengan proteksi timeout, retry, dan caching aman.
                    </p>
                </div>

                <!-- BENTO 5 (4 cols): Notifications & Audit Logs -->
                <div class="md:col-span-4 p-6 rounded-[2rem] glass-card space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-espresso-900">Notifikasi &amp; Audit Trail</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Notifikasi otomatis ke pelanggan saat ada delay atau kendala, plus log audit permanen setiap aksi operator &amp; admin.
                    </p>
                </div>

                <!-- BENTO 6 (4 cols): Sanctum REST API + Security -->
                <div class="md:col-span-4 p-6 rounded-[2rem] glass-card space-y-3">
                    <div class="w-10 h-10 rounded-2xl bg-cream-200 text-espresso-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-base font-extrabold text-espresso-900">RBAC, Sanctum &amp; PHPUnit</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Otorisasi Policy untuk Admin, Staff Operator, &amp; Customer dengan REST API JSON terstandar dan cakupan tes otomatis lengkap.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ROLE MATRIX SECTION -->
    <section id="roles" class="relative z-10 py-14 lg:py-20 border-t border-white/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold uppercase tracking-wider">Keamanan &amp; Hak Akses</span>
                <p class="text-3xl font-extrabold text-espresso-900">Matriks Peran Pengguna</p>
                <p class="text-sm text-espresso-600">Otorisasi berlapis yang diterapkan langsung pada Middleware HTTP maupun Policy Layer.</p>
            </div>

            <div class="rounded-3xl glass-card-strong overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                        <thead class="bg-sage-50/80 text-espresso-600 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Fitur / Sumber Daya</th>
                                <th class="px-6 py-4 text-center">Administrator</th>
                                <th class="px-6 py-4 text-center">Staff Operator Logistik</th>
                                <th class="px-6 py-4 text-center">Pelanggan</th>
                                <th class="px-6 py-4 text-center">Publik (Tamu)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-100 text-espresso-800">
                            <tr class="hover:bg-white/60">
                                <td class="px-6 py-4 font-bold text-espresso-900">Manajemen Pengguna &amp; Log Audit Sistem</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Akses Penuh</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-white/60">
                                <td class="px-6 py-4 font-bold text-espresso-900">Data Master Ekspedisi &amp; Lokasi Pelabuhan</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; CRUD Penuh</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; Lihat / Ubah</span></td>
                                <td class="px-6 py-4 text-center text-espresso-500">&#10003; Lihat Lokasi</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-white/60">
                                <td class="px-6 py-4 font-bold text-espresso-900">Pembuatan Resi, ETA, &amp; Penanganan Kendala</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Diizinkan</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Diizinkan</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-white/60">
                                <td class="px-6 py-4 font-bold text-espresso-900">Timeline, Peta Rute, &amp; Notifikasi Pengiriman</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Semua Resi</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Semua Resi</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-bold">&#10003; Milik Sendiri</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; Resi Publik</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="relative z-10 border-t border-white/80 bg-white/50 backdrop-blur-xl py-10 text-espresso-500 text-xs mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-xs">CT</div>
                <span class="text-espresso-900 font-extrabold">Platform Visibilitas CargoTrace</span>
                <span>&copy; {{ date('Y') }} UAS Pemrograman Web</span>
            </div>
            <div class="flex items-center gap-6 font-semibold">
                <a href="{{ route('login') }}" class="hover:text-espresso-900 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-espresso-900 transition-colors">Daftar</a>
                <a href="{{ url('/api/v1/health') }}" target="_blank" class="hover:text-espresso-900 transition-colors font-mono">Status API</a>
            </div>
        </div>
    </footer>

    <!-- Script for Hero Selection + Live Tracking -->
    <script>
    const heroCorridors = {
        europe: {
            tracking: 'CT-20261101-9821',
            badge: 'Koridor Eropa (12j)',
            title: 'Jakarta &rarr; Rotterdam<br><span class="text-sage-100 font-semibold text-lg">Fokus: Customs Hold &amp; On-Time</span>',
            carrier: 'A.P. Moller - Maersk &bull; MAEU',
            status: 'IN_TRANSIT (+16j)',
            origin: 'IDTPP &middot; Jakarta',
            dest: 'NLRTM &middot; Rotterdam'
        },
        america: {
            tracking: 'CT-20261105-4412',
            badge: 'Koridor Amerika (18j)',
            title: 'Shanghai &rarr; Los Angeles<br><span class="text-sage-100 font-semibold text-lg">Fokus: Weather Delay &amp; Avg Delay</span>',
            carrier: 'DHL Global Forwarding &bull; DHLG',
            status: 'EXCEPTION (+28j)',
            origin: 'CNSHA &middot; Shanghai',
            dest: 'USLAX &middot; Los Angeles'
        },
        australia: {
            tracking: 'CT-20261020-1109',
            badge: 'Koridor Australia (24j)',
            title: 'Singapura &rarr; Sydney<br><span class="text-sage-100 font-semibold text-lg">Fokus: Doc Issue &amp; Tepat Waktu</span>',
            carrier: 'MSC Mediterranean &bull; MSCU',
            status: 'DELIVERED',
            origin: 'SGSIN &middot; Singapura',
            dest: 'AUSYD &middot; Sydney'
        }
    };

    let selectedHeroTracking = heroCorridors.europe.tracking;

    function selectHeroCorridor(key) {
        const c = heroCorridors[key];
        if (!c) return;

        selectedHeroTracking = c.tracking;
        document.getElementById('heroTrackInput').value = c.tracking;
        document.getElementById('heroShowcaseBadge').textContent = c.badge;
        document.getElementById('heroShowcaseTitle').innerHTML = c.title;
        document.getElementById('heroShowcaseCarrier').innerHTML = c.carrier;
        document.getElementById('heroShowcaseStatus').textContent = c.status;
        document.getElementById('heroShowcaseOrigin').innerHTML = c.origin;
        document.getElementById('heroShowcaseDest').innerHTML = c.dest;
        document.getElementById('heroShowcaseTracking').textContent = c.tracking;

        ['europe', 'america', 'australia'].forEach(k => {
            const btn = document.getElementById('heroCard-' + k);
            if (!btn) return;
            if (k === key) {
                btn.className = 'hero-selector-card p-3.5 rounded-3xl text-left transition-all cursor-pointer glass-sage text-white';
                btn.querySelectorAll('.text-espresso-900, .text-sage-700, .text-espresso-500, .text-apricot-600').forEach(el => {
                    el.dataset.prev = el.className;
                    el.classList.add('text-white');
                });
            } else {
                btn.className = 'hero-selector-card p-3.5 rounded-3xl text-left transition-all cursor-pointer glass-card hover:bg-white/90 text-espresso-900';
            }
        });
    }

    function inspectSelectedHero() {
        document.getElementById('heroTrackInput').value = selectedHeroTracking;
        heroTrack();
    }

    function setHeroTrack(trackingNo) {
        document.getElementById('heroTrackInput').value = trackingNo;
        heroTrack();
    }

    async function heroTrack() {
        const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));
        const input = document.getElementById('heroTrackInput').value.trim();
        if (!input) return;

        const modal = document.getElementById('heroResultModal');
        const content = document.getElementById('heroResultContent');
        modal.classList.remove('hidden');
        content.innerHTML = '<p class="text-espresso-500 text-sm">Memeriksa data pengiriman secara real-time...</p>';

        try {
            const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
            const data = await res.json();

            if (res.ok && data.success) {
                const s = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-white/75 p-4 rounded-2xl border border-cream-200">
                            <div>
                                <span class="text-xs text-espresso-500 font-semibold">Referensi Nomor Resi:</span>
                                <p class="text-lg font-extrabold font-mono text-sage-700">${esc(s.tracking_number)}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    ${s.status === 'DELIVERED' ? 'bg-sage-100 text-sage-700' :
                                      (s.status === 'IN_TRANSIT' ? 'bg-sage-500 text-white' :
                                      'bg-apricot-100 text-apricot-600')}">
                                    ${esc(s.status)}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div class="p-3.5 rounded-2xl bg-white/75 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Ekspedisi</span>
                                <strong class="text-espresso-900">${s.carrier ? esc(s.carrier.name) : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white/75 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Pelabuhan Asal</span>
                                <strong class="text-espresso-900">${s.origin ? esc(s.origin.name) : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white/75 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Pelabuhan Tujuan</span>
                                <strong class="text-espresso-900">${s.destination ? esc(s.destination.name) : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-white/75 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Estimasi Tiba</span>
                                <strong class="text-espresso-900">${s.estimated_delivery_at ? esc(new Date(s.estimated_delivery_at).toLocaleDateString('id-ID')) : 'Menunggu'}</strong>
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end">
                            <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all">
                                <span>Buka Peta &amp; Timeline Lengkap</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `<p class="text-rose-600 font-medium text-sm">${esc(data.message) || 'Nomor resi pengiriman tidak ditemukan.'}</p>`;
            }
        } catch (e) {
            content.innerHTML = `<p class="text-rose-600 font-medium text-sm">Gagal memeriksa data pengiriman. Periksa koneksi Anda.</p>`;
        }
    }
    </script>
</body>
</html>
