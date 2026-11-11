@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional')
@section('page_title', 'Pusat Operasional Kargo')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-sage-500 text-white p-6 sm:p-8 shadow-[0_18px_45px_-15px_rgba(135,152,128,0.45)]">
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-16 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/20 backdrop-blur-xs text-white">
                        Peran: {{ ucfirst(Auth::user()->role) }}
                    </span>
                    <span class="text-xs text-sage-100 font-medium">Terautentikasi melalui Sanctum/Session</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Selamat datang kembali, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-sage-50">
                    Portal Visibilitas CargoTrace &mdash; {{ Auth::user()->company_name ?: 'Jaringan Rantai Pasok Global' }}
                </p>
            </div>

            <!-- Quick Track Input Widget -->
            <div id="quick-track" class="w-full md:w-auto">
                <form id="trackForm" onsubmit="event.preventDefault(); quickTrack();" class="flex items-center gap-2 bg-white/15 backdrop-blur-sm p-1.5 rounded-2xl border border-white/20">
                    <input id="trackInput" type="text" placeholder="CT-YYYYMMDD-XXXX"
                           class="w-full sm:w-60 px-3.5 py-2.5 bg-white rounded-xl text-xs font-mono placeholder-espresso-400 text-espresso-900 focus:outline-none focus:ring-2 focus:ring-apricot-400 transition-all">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/30 transition-all whitespace-nowrap cursor-pointer">
                        Lacak Kargo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Operational Stat Cards (Glassmorphism) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-6 rounded-3xl glass-card-strong">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Pengiriman Aktif</p>
                <div class="w-9 h-9 rounded-2xl bg-sage-100/90 text-sage-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-espresso-900">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-sage-600 mt-1">Siklus pengiriman berjalan</p>
        </div>

        <div class="p-6 rounded-3xl glass-card-strong">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Dalam Perjalanan</p>
                <div class="w-9 h-9 rounded-2xl bg-apricot-100/90 text-apricot-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-apricot-600">{{ $stats['in_transit'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-espresso-500 mt-1">Antar titik pelabuhan/hub</p>
        </div>

        <div class="p-6 rounded-3xl glass-card-strong">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Keterlambatan</p>
                <div class="w-9 h-9 rounded-2xl bg-rose-100/90 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-rose-600">{{ $stats['delayed'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-espresso-500 mt-1">Terdeteksi aturan ETA</p>
        </div>

        <div class="p-6 rounded-3xl glass-card-strong">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Terkirim (30 Hari)</p>
                <div class="w-9 h-9 rounded-2xl bg-sage-100/90 text-sage-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-sage-700">{{ $stats['delivered'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-sage-600 mt-1">Status selesai diterima</p>
        </div>
    </div>

    <!-- Worldwide Combined KPIs Row (SEA→Europe, Asia→NA, SEA→Australia) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl glass-sage flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-sage-700">Ketepatan Tiba (On-Time Rate)</p>
                <p class="text-xl font-extrabold text-espresso-900 mt-0.5">{{ $kpis['on_time_delivery_rate'] ?? 100 }}%</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-white/80 text-[10px] font-bold text-sage-700">Rute Eropa</span>
        </div>

        <div class="p-4 rounded-2xl glass-card border-apricot-200/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-apricot-600">Rata-Rata Keterlambatan</p>
                <p class="text-xl font-extrabold text-espresso-900 mt-0.5">{{ $kpis['average_delay_hours'] ?? 0 }} jam</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-white/80 text-[10px] font-bold text-apricot-600">Rute Amerika</span>
        </div>

        <div class="p-4 rounded-2xl glass-card flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-espresso-600">Waktu Penyelesaian Kendala</p>
                <p class="text-xl font-extrabold text-espresso-900 mt-0.5">{{ $kpis['avg_exception_resolution_hours'] ?? 0 }} jam</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-white/80 text-[10px] font-bold text-espresso-700">Rute Australia</span>
        </div>

        <div class="p-4 rounded-2xl glass-card border-rose-200/80 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Kendala Terbuka (Exceptions)</p>
                <p class="text-xl font-extrabold text-rose-700 mt-0.5">{{ $kpis['open_exceptions_count'] ?? 0 }} aktif</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-white/80 text-[10px] font-bold text-rose-600">Perlu Tindak</span>
        </div>
    </div>

    <!-- Live Analytics Grid (Chart.js powered by Database) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Chart 1: Shipments by Status -->
        <div class="p-6 rounded-3xl glass-card-strong flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Distribusi Status Pengiriman</h3>
                    <p class="text-[11px] text-espresso-500">Proporsi status aktif &amp; selesai</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-sage-100/90 text-[10px] font-bold text-sage-700">Total: {{ $stats['total'] ?? 0 }}</span>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
                <p id="statusChartEmpty" class="hidden text-xs text-espresso-400 font-semibold text-center">Belum ada data status pengiriman.</p>
            </div>
        </div>

        <!-- Chart 2: Shipments by Carrier -->
        <div class="p-6 rounded-3xl glass-card-strong flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Volume per Ekspedisi</h3>
                    <p class="text-[11px] text-espresso-500">Muatan aktif tiap mitra angkutan</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-apricot-100/90 text-[10px] font-bold text-apricot-600">Mitra Aktif</span>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="carrierChart"></canvas>
                <p id="carrierChartEmpty" class="hidden text-xs text-espresso-400 font-semibold text-center">Belum ada data volume ekspedisi.</p>
            </div>
        </div>

        <!-- Chart 3: Exception Breakdown -->
        <div class="p-6 rounded-3xl glass-card-strong flex flex-col justify-between space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Analisis Kendala Rute</h3>
                    <p class="text-[11px] text-espresso-500">CUSTOMS, WEATHER, DOCS &amp; lainnya</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-rose-100/90 text-[10px] font-bold text-rose-700">Monitoring SLA</span>
            </div>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="exceptionChart"></canvas>
                <p id="exceptionChartEmpty" class="hidden text-xs text-espresso-400 font-semibold text-center">Tidak ada catatan kendala saat ini.</p>
            </div>
        </div>
    </div>

    @if(!empty($openExceptions) && $openExceptions->isNotEmpty())
        <div class="p-5 rounded-3xl glass-card border-rose-200 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-rose-700 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    Peringatan Kendala Operasional Aktif
                </h3>
                <span class="text-[11px] font-mono text-espresso-500">{{ $openExceptions->count() }} terbuka</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 text-xs">
                @foreach($openExceptions as $ex)
                    <a href="{{ route('shipments.show', $ex->shipment->tracking_number) }}" class="p-3 rounded-2xl bg-rose-50/50 hover:bg-rose-50 border border-rose-200/80 flex items-center justify-between gap-3 transition-colors">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-espresso-900">{{ $ex->shipment->tracking_number }}</span>
                                <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white text-[9px] font-bold uppercase">{{ $ex->type }}</span>
                            </div>
                            <p class="text-espresso-700 truncate mt-0.5">{{ $ex->description }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-white text-[10px] font-bold text-rose-600 shrink-0">{{ $ex->severity }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quick Tracking Modal / Result Drawer -->
    <div id="trackResultModal" class="hidden rounded-3xl glass-card-strong p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-cream-200 pb-3">
            <h3 class="text-sm font-extrabold text-espresso-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sage-500 animate-ping"></span>
                <span>Hasil Pelacakan Pengiriman Langsung</span>
            </h3>
            <button onclick="document.getElementById('trackResultModal').classList.add('hidden')" class="px-3 py-1 rounded-full bg-cream-100 hover:bg-cream-200 text-xs font-bold text-espresso-600 cursor-pointer">&times; Tutup</button>
        </div>
        <div id="trackResultBody" class="text-xs text-espresso-800 font-mono"></div>
    </div>

    <!-- SHIPMENTS MANIFEST TABLE SECTION -->
    <div id="shipments-section" class="rounded-3xl glass-card-strong overflow-hidden">
        <div class="p-6 border-b border-cream-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-extrabold text-espresso-900">Daftar Pengiriman Terkini</h2>
                <p class="text-xs text-espresso-500 mt-0.5">
                    @if(Auth::user()->isCustomer())
                        Menampilkan pengiriman yang terhubung langsung dengan akun pelanggan Anda.
                    @else
                        Konsol operasional utama untuk memantau seluruh pengiriman pelanggan.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="px-3.5 py-1.5 rounded-full bg-cream-100 text-xs font-bold text-espresso-700">Total: <strong class="text-espresso-900">{{ $shipments->total() }}</strong></span>
                <a href="{{ route('shipments.index') }}" class="px-4 py-1.5 rounded-full bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold transition-colors">
                    Buka Semua Daftar &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Nomor Resi</th>
                        <th scope="col" class="px-5 py-3.5">Pelanggan</th>
                        <th scope="col" class="px-5 py-3.5">Ekspedisi</th>
                        <th scope="col" class="px-5 py-3.5">Asal &rarr; Tujuan</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Estimasi Tiba</th>
                        <th scope="col" class="px-5 py-3.5">Muatan</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-espresso-800">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-cream-50/80 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-sage-700 whitespace-nowrap">
                                {{ $shipment->tracking_number }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-espresso-900">{{ $shipment->customer?->name ?: 'N/A' }}</div>
                                <div class="text-[11px] text-espresso-500">{{ $shipment->customer?->company_name }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-800 font-semibold">
                                    {{ $shipment->carrier?->name ?: 'Belum Ditentukan' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-espresso-900 font-semibold">{{ $shipment->origin?->name }} &rarr; {{ $shipment->destination?->name }}</div>
                                <div class="text-[11px] text-espresso-500">
                                    @if($shipment->currentLocation)
                                        Posisi saat ini: <span class="text-sage-700 font-semibold">{{ $shipment->currentLocation->name }}</span>
                                    @else
                                        Menunggu keberangkatan awal
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase
                                    @if($shipment->status === 'DELIVERED') bg-sage-100 text-sage-700
                                    @elseif($shipment->status === 'IN_TRANSIT') bg-sage-500 text-white
                                    @elseif($shipment->status === 'BOOKED') bg-apricot-100 text-apricot-600
                                    @elseif($shipment->status === 'EXCEPTION') bg-rose-100 text-rose-700
                                    @else bg-cream-200 text-espresso-800
                                    @endif">
                                    {{ $shipment->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-espresso-600 font-medium">
                                {{ $shipment->estimated_delivery_at?->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-700 font-semibold text-[11px]">
                                    {{ $shipment->items->count() }} barang
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('shipments.show', $shipment->tracking_number) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-sm transition-all">
                                    <span>Periksa</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-espresso-500">
                                Belum ada data pengiriman di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="p-4 border-t border-cream-200 bg-cream-50/50">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

    <!-- CARRIERS & MASTER NODES PREVIEW -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Carriers Card -->
        <div id="carriers-section" class="p-6 rounded-3xl glass-card-strong space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Ekspedisi Laut &amp; Udara Aktif</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-cream-100 text-[11px] font-mono font-bold text-espresso-600">{{ $carriersCount ?? 5 }} terdaftar</span>
                    @if(Auth::user()->can('viewAny', \App\Models\Carrier::class))
                        <a href="{{ route('carriers.index') }}" class="px-3 py-1 rounded-full bg-apricot-100 hover:bg-apricot-500 hover:text-white text-apricot-600 text-[11px] font-bold transition-colors">Kelola &rarr;</a>
                    @endif
                </div>
            </div>
            <p class="text-xs text-espresso-500">Jalur pelayaran dan kargo udara global dengan kode standar serta URL pelacakan otomatis.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">A.P. Moller - Maersk</span>
                    <span class="font-mono font-bold text-apricot-600 text-[10px]">MAEU</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">MSC Mediterranean</span>
                    <span class="font-mono font-bold text-apricot-600 text-[10px]">MSCU</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">CMA CGM Group</span>
                    <span class="font-mono font-bold text-apricot-600 text-[10px]">CMDU</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">DHL Global Forwarding</span>
                    <span class="font-mono font-bold text-apricot-600 text-[10px]">DHLG</span>
                </div>
            </div>
        </div>

        <!-- Locations Card -->
        <div id="locations-section" class="p-6 rounded-3xl glass-card-strong space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Pelabuhan &amp; Hub Logistik Global</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-sage-100 text-[11px] font-mono font-bold text-sage-700">{{ $locationsCount ?? 8 }} hub</span>
                    <a href="{{ route('locations.index') }}" class="px-3 py-1 rounded-full bg-sage-100 hover:bg-sage-500 hover:text-white text-sage-700 text-[11px] font-bold transition-colors">Lihat Semua &rarr;</a>
                </div>
            </div>
            <p class="text-xs text-espresso-500">Hub pelabuhan standar UN/LOCODE dilengkapi koordinat lintang dan bujur siap untuk peta interaktif.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">Port of Tanjung Priok</span>
                    <span class="font-mono font-bold text-sage-700 text-[10px]">IDTPP</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">Port of Singapore</span>
                    <span class="font-mono font-bold text-sage-700 text-[10px]">SGSIN</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">Port of Rotterdam</span>
                    <span class="font-mono font-bold text-sage-700 text-[10px]">NLRTM</span>
                </div>
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200 flex items-center justify-between">
                    <span class="font-bold text-espresso-900">Soekarno-Hatta Cargo</span>
                    <span class="font-mono font-bold text-sage-700 text-[10px]">IDCGK</span>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $chartsData = [
        'status' => $charts['status'] ?? [],
        'carriers' => $charts['carriers'] ?? [],
        'exceptions' => $charts['exceptions'] ?? [],
    ];
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartPayload = {{ Illuminate\Support\Js::from($chartsData) }};

    const renderDashboardCharts = () => {
        if (typeof window.Chart === 'undefined') return;

        Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
        Chart.defaults.color = '#635952';

        // 1. Shipments by Status (Doughnut)
        const statusEntries = Object.entries(chartPayload.status || {});
        const statusCanvas = document.getElementById('statusChart');
        const statusEmpty = document.getElementById('statusChartEmpty');
        if (statusCanvas) {
            if (statusEntries.length === 0) {
                statusCanvas.classList.add('hidden');
                if (statusEmpty) statusEmpty.classList.remove('hidden');
            } else {
                new Chart(statusCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: statusEntries.map(([k]) => k.replace(/_/g, ' ')),
                        datasets: [{
                            data: statusEntries.map(([, v]) => v),
                            backgroundColor: ['#879880', '#F2994A', '#6D7F66', '#E4C39A', '#E11D48', '#A3B19D', '#2C2724'],
                            borderWidth: 2,
                            borderColor: 'rgba(255,255,255,0.85)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10, weight: 'bold' } } }
                        }
                    }
                });
            }
        }

        // 2. Shipments by Carrier (Bar)
        const carrierEntries = Object.entries(chartPayload.carriers || {});
        const carrierCanvas = document.getElementById('carrierChart');
        const carrierEmpty = document.getElementById('carrierChartEmpty');
        if (carrierCanvas) {
            if (carrierEntries.length === 0) {
                carrierCanvas.classList.add('hidden');
                if (carrierEmpty) carrierEmpty.classList.remove('hidden');
            } else {
                new Chart(carrierCanvas, {
                    type: 'bar',
                    data: {
                        labels: carrierEntries.map(([k]) => k),
                        datasets: [{
                            label: 'Jumlah Pengiriman',
                            data: carrierEntries.map(([, v]) => v),
                            backgroundColor: 'rgba(135, 152, 128, 0.85)',
                            borderRadius: 10,
                            maxBarThickness: 32,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(227,219,205,0.45)' } },
                            x: { ticks: { font: { size: 10, weight: '600' } }, grid: { display: false } }
                        }
                    }
                });
            }
        }

        // 3. Exception Breakdown (Horizontal Bar)
        const excEntries = Object.entries(chartPayload.exceptions || {});
        const excCanvas = document.getElementById('exceptionChart');
        const excEmpty = document.getElementById('exceptionChartEmpty');
        if (excCanvas) {
            if (excEntries.length === 0) {
                excCanvas.classList.add('hidden');
                if (excEmpty) excEmpty.classList.remove('hidden');
            } else {
                new Chart(excCanvas, {
                    type: 'bar',
                    data: {
                        labels: excEntries.map(([k]) => k.replace(/_/g, ' ')),
                        datasets: [{
                            label: 'Total Kasus',
                            data: excEntries.map(([, v]) => v),
                            backgroundColor: 'rgba(242, 153, 74, 0.85)',
                            borderRadius: 10,
                            maxBarThickness: 26,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: 'rgba(227,219,205,0.45)' } },
                            y: { ticks: { font: { size: 10, weight: '600' } }, grid: { display: false } }
                        }
                    }
                });
            }
        }
    };

    if (typeof window.Chart !== 'undefined') {
        renderDashboardCharts();
    } else {
        window.addEventListener('load', renderDashboardCharts);
    }
});

function quickInspect(trackingNo) {
    document.getElementById('trackInput').value = trackingNo;
    quickTrack();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function quickTrack() {
    const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));
    const input = document.getElementById('trackInput').value.trim();
    if (!input) return;

    const modal = document.getElementById('trackResultModal');
    const body = document.getElementById('trackResultBody');
    modal.classList.remove('hidden');
    body.innerHTML = '<span class="text-espresso-500">Memeriksa endpoint REST API (/api/v1/tracking/' + esc(input) + ')...</span>';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
        const data = await res.json();

        if (res.ok && data.success) {
            const s = data.data;
            body.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs font-sans">
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Nomor Resi</span>
                        <strong class="text-sage-700 font-mono text-sm">${esc(s.tracking_number)}</strong>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Status</span>
                        <span class="text-apricot-600 font-extrabold">${esc(s.status)}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Ekspedisi</span>
                        <span class="text-espresso-900 font-bold">${s.carrier ? esc(s.carrier.name) : 'Tidak Ada'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Pelabuhan Asal</span>
                        <span class="text-espresso-900 font-bold">${s.origin ? esc(s.origin.name) : 'Tidak Ada'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Pelabuhan Tujuan</span>
                        <span class="text-espresso-900 font-bold">${s.destination ? esc(s.destination.name) : 'Tidak Ada'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Estimasi Tiba</span>
                        <span class="text-espresso-800 font-semibold">${s.estimated_delivery_at ? esc(new Date(s.estimated_delivery_at).toLocaleString('id-ID')) : 'Menunggu'}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-cream-200 flex justify-end">
                    <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md transition-all">
                        <span>Buka Timeline Lengkap</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            `;
        } else {
            body.innerHTML = `<span class="text-rose-600 font-sans font-semibold">${esc(data.message) || 'Pengiriman tidak ditemukan untuk nomor resi tersebut.'}</span>`;
        }
    } catch (e) {
        body.innerHTML = `<span class="text-rose-600 font-sans font-semibold">Gagal mengambil data pengiriman.</span>`;
    }
}
</script>
@endsection
