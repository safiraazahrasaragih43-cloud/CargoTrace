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
                <a href="#tracking" class="hover:text-espresso-900 transition-colors">Lacak Langsung</a>
                <a href="#features" class="hover:text-espresso-900 transition-colors">Fitur Utama</a>
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
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full text-xs font-bold text-espresso-800 bg-white hover:bg-cream-100 border border-cream-200 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-lg shadow-apricot-500/25 transition-all">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
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
                        Visibilitas Logistik Global &middot; Laravel 12
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-espresso-900 leading-[1.12]">
                            Pantau pergerakan <span class="text-sage-600">kargo global</span> Anda dengan mudah
                        </h1>
                        <p class="text-base sm:text-lg text-espresso-600 max-w-xl leading-relaxed">
                            Lacak kontainer laut, kargo udara, dan jalur distribusi darat secara akurat&mdash;dilengkapi kalkulasi ETA deterministik serta riwayat pengiriman yang permanen.
                        </p>
                    </div>

                    <!-- Search + Sage Button Bar -->
                    <form onsubmit="event.preventDefault(); heroTrack();" class="max-w-xl flex flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-espresso-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input id="heroTrackInput" type="text"
                                   placeholder="Masukkan nomor resi (cth: CT-20261101-9821)"
                                   class="w-full pl-11 pr-4 py-3.5 bg-white border border-cream-200 rounded-2xl text-sm font-mono text-espresso-900 placeholder-espresso-400 shadow-[0_6px_20px_-6px_rgba(44,39,36,0.06)] focus:outline-none focus:ring-2 focus:ring-sage-500 transition-all">
                        </div>
                        <button type="submit" class="px-6 py-3.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white font-bold text-sm shadow-lg shadow-sage-500/25 transition-all flex items-center justify-center gap-2 cursor-pointer shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            <span>Lacak Kargo</span>
                        </button>
                    </form>

                    <!-- Sample Consignment Tiles -->
                    <div class="space-y-2.5">
                        <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Coba Contoh Nomor Resi</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl">
                            <button type="button" onclick="setHeroTrack('CT-20261101-9821')" class="group p-3 rounded-2xl bg-sage-500 text-white text-left shadow-md shadow-sage-500/20 hover:bg-sage-600 transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="text-[11px] font-bold opacity-90">Dalam Perjalanan</div>
                                <div class="text-[11px] font-mono font-bold truncate">CT-20261101-9821</div>
                            </button>

                            <button type="button" onclick="setHeroTrack('CT-20261105-4412')" class="group p-3 rounded-2xl bg-white hover:bg-cream-100 border border-cream-200 text-left shadow-xs transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-apricot-100 text-apricot-600 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Terjadwal</div>
                                <div class="text-[11px] font-mono font-bold text-espresso-900 truncate">CT-20261105-4412</div>
                            </button>

                            <button type="button" onclick="setHeroTrack('CT-20261020-1109')" class="group p-3 rounded-2xl bg-white hover:bg-cream-100 border border-cream-200 text-left shadow-xs transition-all cursor-pointer">
                                <div class="w-8 h-8 rounded-xl bg-sage-100 text-sage-700 flex items-center justify-center mb-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Terkirim</div>
                                <div class="text-[11px] font-mono font-bold text-espresso-900 truncate">CT-20261020-1109</div>
                            </button>

                            <a href="{{ route('dashboard') }}" class="group p-3 rounded-2xl bg-white hover:bg-apricot-50 border border-cream-200 hover:border-apricot-200 text-left shadow-xs transition-all">
                                <div class="w-8 h-8 rounded-xl bg-cream-100 text-espresso-800 group-hover:bg-apricot-500 group-hover:text-white flex items-center justify-center mb-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                </div>
                                <div class="text-[11px] font-bold text-espresso-500">Konsol Admin</div>
                                <div class="text-[11px] font-bold text-apricot-600 truncate">Buka Portal &rarr;</div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Sage Showcase Card -->
                <div class="lg:col-span-5 space-y-5">
                    <div class="relative overflow-hidden rounded-[2rem] bg-sage-500 p-7 text-white shadow-[0_20px_50px_-15px_rgba(135,152,128,0.45)]">
                        <div class="absolute -right-12 -bottom-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
                        <div class="absolute right-6 top-6 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-[11px] font-bold">
                            Jalur Aktif
                        </div>

                        <div class="relative z-10 space-y-4">
                            <p class="text-xs font-bold uppercase tracking-widest text-sage-100">Rute Unggulan</p>
                            <h2 class="text-3xl font-extrabold leading-tight">
                                Jakarta &rarr; Rotterdam<br>
                                <span class="text-sage-100 font-semibold text-xl">Angkutan Laut Ekspres</span>
                            </h2>

                            <!-- Orbital Showcase -->
                            <div class="my-4 py-5 px-4 rounded-3xl bg-cream-50 text-espresso-900 shadow-inner relative">
                                <div class="flex items-center justify-between text-xs mb-3">
                                    <span class="font-bold text-apricot-600">Maersk Line &bull; MAEU</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-700 font-bold text-[10px]">IN_TRANSIT</span>
                                </div>

                                <div class="relative flex items-center justify-between py-4 px-2">
                                    <div class="absolute inset-x-4 top-1/2 -translate-y-1/2 h-10 border border-apricot-400/50 rounded-[100%] pointer-events-none"></div>
                                    <div class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        IDTPP &middot; Asal
                                    </div>
                                    <div class="relative z-10 w-9 h-9 rounded-full bg-apricot-500 text-white flex items-center justify-center shadow-md shadow-apricot-500/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </div>
                                    <div class="relative z-10 bg-white px-3 py-1.5 rounded-full border border-cream-200 shadow-xs text-[11px] font-bold">
                                        SGSIN &middot; Transit
                                    </div>
                                </div>

                                <div class="mt-2 pt-3 border-t border-cream-200 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-espresso-500 font-semibold uppercase">Contoh Resi</p>
                                        <p class="text-sm font-extrabold font-mono text-espresso-900">CT-20261101-9821</p>
                                    </div>
                                    <button type="button" onclick="setHeroTrack('CT-20261101-9821')" class="px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md shadow-apricot-500/25 transition-all cursor-pointer">
                                        Periksa Langsung
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mini KPI Pill Row -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-espresso-900">100%</p>
                            <p class="text-[11px] font-medium text-espresso-500">Integritas Status</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-sage-600">3 Peran</p>
                            <p class="text-[11px] font-medium text-espresso-500">Akses RBAC</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-white border border-cream-200 text-center shadow-xs">
                            <p class="text-xl font-extrabold text-apricot-600">REST v1</p>
                            <p class="text-[11px] font-medium text-espresso-500">API Sanctum</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Live Tracking Result Container -->
            <div id="heroResultModal" class="hidden mt-10 text-left p-6 sm:p-8 rounded-3xl bg-white border border-cream-200 shadow-[0_20px_50px_-15px_rgba(44,39,36,0.12)] space-y-5">
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

    <!-- CORE FEATURES SECTION -->
    <section id="features" class="py-16 lg:py-20 border-t border-cream-200/80 bg-white/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3 py-1 rounded-full bg-apricot-100 text-apricot-600 text-[11px] font-bold uppercase tracking-wider">Dirancang untuk Keandalan</span>
                <p class="text-3xl font-extrabold text-espresso-900">Fitur Utama Manajemen Rantai Pasok</p>
                <p class="text-sm text-espresso-600">Dibangun dengan arsitektur Service-Layer yang bersih, aturan bisnis ketat, dan keamanan terverifikasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-sage-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Transisi Status Ketat</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Siklus hidup pengiriman terstruktur (CREATED &rarr; BOOKED &rarr; PICKED_UP &rarr; IN_TRANSIT &rarr; DELIVERED). Status akhir tidak dapat dikembalikan ke awal.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-apricot-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Catatan Riwayat Permanen</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Setiap titik perjalanan kontainer dicatat beserta stempel waktu dan koordinat GPS secara permanen untuk menjaga keaslian riwayat pengiriman.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-7 rounded-3xl bg-white border border-cream-200 shadow-[0_10px_30px_-15px_rgba(44,39,36,0.08)] hover:border-sage-400 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-cream-100 text-espresso-800 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-espresso-900">Kontrol Akses Berbasis Peran</h3>
                    <p class="text-xs text-espresso-600 leading-relaxed">
                        Pemisahan peran Admin, Operator Logistik, dan Pelanggan dengan Laravel Policies. Pelanggan hanya diizinkan melihat pengiriman miliknya sendiri.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ROLE MATRIX SECTION -->
    <section id="roles" class="py-16 lg:py-20 border-t border-cream-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2.5">
                <span class="inline-block px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold uppercase tracking-wider">Keamanan &amp; Hak Akses</span>
                <p class="text-3xl font-extrabold text-espresso-900">Matriks Peran Pengguna</p>
                <p class="text-sm text-espresso-600">Otorisasi berlapis yang diterapkan langsung pada Middleware HTTP maupun Policy Layer.</p>
            </div>

            <div class="rounded-3xl bg-white border border-cream-200 overflow-hidden shadow-[0_16px_40px_-14px_rgba(44,39,36,0.08)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                        <thead class="bg-sage-50 text-espresso-600 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Fitur / Sumber Daya</th>
                                <th class="px-6 py-4 text-center">Administrator</th>
                                <th class="px-6 py-4 text-center">Operator Logistik</th>
                                <th class="px-6 py-4 text-center">Pelanggan</th>
                                <th class="px-6 py-4 text-center">Publik (Tamu)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-100 text-espresso-800">
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Manajemen Pengguna &amp; Sistem RBAC</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Akses Penuh</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Data Master Ekspedisi &amp; Lokasi Pelabuhan</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; CRUD Penuh</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; Lihat / Ubah</span></td>
                                <td class="px-6 py-4 text-center text-espresso-500">&#10003; Lihat Lokasi</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Pemesanan &amp; Pembuatan Pengiriman</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Buat Semua</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Buat Semua</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Timeline &amp; Visibilitas Pengiriman</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Semua Resi</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Semua Resi</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-bold">&#10003; Milik Sendiri</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-bold">&#10003; Resi Publik</span></td>
                            </tr>
                            <tr class="hover:bg-cream-50/70">
                                <td class="px-6 py-4 font-bold text-espresso-900">Pencatatan Titik Checkpoint Perjalanan</td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Diizinkan</span></td>
                                <td class="px-6 py-4 text-center"><span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold">&#10003; Diizinkan</span></td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
                                <td class="px-6 py-4 text-center text-rose-500 font-semibold">&#10005; Ditolak</td>
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
        content.innerHTML = '<p class="text-espresso-500 text-sm">Memeriksa data pengiriman secara real-time...</p>';

        try {
            const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
            const data = await res.json();

            if (res.ok && data.success) {
                const s = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-cream-50 p-4 rounded-2xl border border-cream-200">
                            <div>
                                <span class="text-xs text-espresso-500 font-semibold">Referensi Nomor Resi:</span>
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
                                <span class="text-espresso-500 block font-semibold mb-0.5">Ekspedisi</span>
                                <strong class="text-espresso-900">${s.carrier ? s.carrier.name : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Pelabuhan Asal</span>
                                <strong class="text-espresso-900">${s.origin ? s.origin.name : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Pelabuhan Tujuan</span>
                                <strong class="text-espresso-900">${s.destination ? s.destination.name : 'Tidak Ada'}</strong>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                                <span class="text-espresso-500 block font-semibold mb-0.5">Estimasi Tiba</span>
                                <strong class="text-espresso-900">${s.estimated_delivery_at ? new Date(s.estimated_delivery_at).toLocaleDateString('id-ID') : 'Menunggu'}</strong>
                            </div>
                        </div>

                        ${s.items && s.items.length ? `
                            <div class="p-4 rounded-2xl bg-cream-50 border border-cream-200 space-y-2">
                                <span class="text-xs font-bold text-espresso-600 uppercase">Rincian Muatan (${s.items.length} barang)</span>
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
                                <span>Lihat Timeline Pengiriman</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `<p class="text-rose-600 font-medium text-sm">${data.message || 'Nomor resi pengiriman tidak ditemukan.'}</p>`;
            }
        } catch (e) {
            content.innerHTML = `<p class="text-rose-600 font-medium text-sm">Gagal memeriksa data pengiriman. Periksa koneksi Anda.</p>`;
        }
    }
    </script>
</body>
</html>
