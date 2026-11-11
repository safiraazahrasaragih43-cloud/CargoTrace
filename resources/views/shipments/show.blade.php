@extends('layouts.dashboard')

@section('title', 'Pengiriman ' . $shipment->tracking_number)
@section('page_title', 'Detail Pengiriman & Timeline')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- TOP BAR / BREADCRUMB -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-cream-200">
        <div class="flex items-center gap-3.5">
            <a href="{{ route('dashboard') }}" class="p-2.5 rounded-full bg-white border border-cream-200 text-espresso-600 hover:text-espresso-900 hover:bg-cream-100 shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Detail Pelacakan Pengiriman</span>
                    <span class="text-xs text-espresso-400">&bull;</span>
                    <span class="text-xs font-mono font-bold text-apricot-600">{{ $shipment->carrier?->name ?? 'Ekspedisi' }} ({{ $shipment->carrier?->code ?? 'N/A' }})</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-espresso-900 font-mono tracking-tight flex items-center gap-3">
                    {{ $shipment->tracking_number }}
                    <button onclick="navigator.clipboard.writeText('{{ $shipment->tracking_number }}'); alert('Nomor resi berhasil disalin ke clipboard');" title="Salin nomor resi" class="text-espresso-400 hover:text-espresso-800 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Lifecycle Badge -->
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase
                @if($shipment->status === 'DELIVERED') bg-sage-100 text-sage-700
                @elseif($shipment->status === 'IN_TRANSIT') bg-sage-500 text-white
                @elseif($shipment->status === 'OUT_FOR_DELIVERY') bg-sage-200 text-sage-700
                @elseif($shipment->status === 'BOOKED') bg-apricot-100 text-apricot-600
                @elseif($shipment->status === 'PICKED_UP') bg-apricot-500 text-white
                @elseif($shipment->status === 'EXCEPTION') bg-rose-100 text-rose-700
                @elseif($shipment->status === 'CANCELLED') bg-rose-200 text-rose-800
                @else bg-cream-200 text-espresso-800
                @endif">
                <span class="w-1.5 h-1.5 rounded-full mr-2
                    @if($shipment->status === 'DELIVERED') bg-sage-600
                    @elseif($shipment->status === 'IN_TRANSIT') bg-white animate-pulse
                    @elseif($shipment->status === 'EXCEPTION') bg-rose-600 animate-ping
                    @else bg-current
                    @endif"></span>
                {{ $shipment->status }}
            </span>

            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()) && !$shipment->isTerminal())
                <button onclick="document.getElementById('etaModal').classList.remove('hidden')" class="px-4 py-2 rounded-full bg-white hover:bg-cream-100 border border-cream-200 text-espresso-800 text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Atur ETA
                </button>
                <button onclick="document.getElementById('exceptionModal').classList.remove('hidden')" class="px-4 py-2 rounded-full bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-bold shadow-xs transition-all cursor-pointer">
                    Laporkan Kendala
                </button>
                <button onclick="document.getElementById('eventModal').classList.remove('hidden')" class="px-5 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Checkpoint</span>
                </button>
            @endif
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if(session('status'))
        <div class="p-4 rounded-2xl bg-sage-100 border border-sage-200 text-sage-700 text-sm font-semibold flex items-center gap-3 shadow-xs">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm space-y-1">
            <div class="font-bold">Harap periksa kembali kesalahan berikut:</div>
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- OVERVIEW STATS & ROUTE PIPELINE -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Origin to Destination Route Card -->
        <div class="md:col-span-2 p-6 sm:p-7 rounded-3xl bg-white border border-cream-200/80 shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)] space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Jalur Transit Pengiriman</span>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold">
                        Koridor: {{ $etaEvaluation['corridor']['name'] ?? 'Global' }} (Batas: {{ $etaEvaluation['threshold_hours'] ?? 12 }}j)
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-cream-100 text-[11px] font-mono font-bold text-espresso-600">ID: #{{ $shipment->id }}</span>
                </div>
            </div>

            <div class="relative grid grid-cols-1 sm:grid-cols-3 items-center gap-4 py-4 px-3 rounded-3xl bg-cream-50 border border-cream-200/80">
                <!-- Origin -->
                <div class="space-y-1 text-center sm:text-left">
                    <div class="text-[10px] font-bold text-espresso-500 uppercase tracking-wider">Pelabuhan Asal</div>
                    <div class="text-base sm:text-lg font-extrabold text-espresso-900">{{ $shipment->origin?->name ?? 'N/A' }}</div>
                    <div class="flex items-center justify-center sm:justify-start gap-1.5 text-xs text-espresso-600">
                        <span class="font-mono font-bold text-sage-700">{{ $shipment->origin?->code ?? '---' }}</span>
                        <span>&middot;</span>
                        <span>{{ $shipment->origin?->country ?? '' }}</span>
                    </div>
                </div>

                <!-- Orbital Arrow Visual -->
                <div class="flex flex-col items-center justify-center space-y-1.5 py-2 sm:py-0 relative">
                    <div class="w-full flex items-center justify-center relative">
                        <div class="h-0.5 bg-apricot-200 flex-1"></div>
                        <div class="p-2.5 rounded-full bg-apricot-500 text-white shadow-md shadow-apricot-500/30 z-10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                        <div class="h-0.5 bg-apricot-200 flex-1"></div>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-apricot-600">Rute Global</span>
                </div>

                <!-- Destination -->
                <div class="space-y-1 text-center sm:text-right">
                    <div class="text-[10px] font-bold text-espresso-500 uppercase tracking-wider">Tujuan Akhir</div>
                    <div class="text-base sm:text-lg font-extrabold text-espresso-900">{{ $shipment->destination?->name ?? 'N/A' }}</div>
                    <div class="flex items-center justify-center sm:justify-end gap-1.5 text-xs text-espresso-600">
                        <span class="font-mono font-bold text-sage-700">{{ $shipment->destination?->code ?? '---' }}</span>
                        <span>&middot;</span>
                        <span>{{ $shipment->destination?->country ?? '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Location & Deterministic Delay Rule Reason -->
            <div class="space-y-2.5">
                <div class="p-4 rounded-2xl bg-sage-50 border border-sage-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-sage-600 animate-ping"></div>
                        <span class="text-espresso-600 font-semibold">Posisi Saat Ini:</span>
                        <span class="font-extrabold text-sage-700">{{ $shipment->currentLocation ? $shipment->currentLocation->name : 'Di Gudang Asal' }}</span>
                    </div>
                    <div class="text-espresso-500 font-mono text-[11px]">
                        @if($shipment->departure_at)
                            Berangkat: <strong class="text-espresso-900">{{ $shipment->departure_at->format('d M Y H:i') }}</strong>
                        @else
                            Menunggu keberangkatan
                        @endif
                    </div>
                </div>

                <!-- Deterministic Delay Evaluation Banner -->
                <div class="p-3.5 rounded-2xl border text-xs flex items-start gap-2.5
                    @if($shipment->delay_hours > 0) bg-rose-50/80 border-rose-200 text-rose-800
                    @else bg-cream-50 border-cream-200 text-espresso-700
                    @endif">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0 mt-0.5
                        @if($etaEvaluation['severity'] === 'HIGH') bg-rose-600 text-white
                        @elseif($etaEvaluation['severity'] === 'MEDIUM') bg-apricot-500 text-white
                        @elseif($etaEvaluation['severity'] === 'LOW') bg-apricot-100 text-apricot-600
                        @else bg-sage-100 text-sage-700
                        @endif">
                        {{ $etaEvaluation['severity'] }}
                    </span>
                    <div>
                        <span class="font-bold">Evaluasi Aturan ETA:</span>
                        <span>{{ $shipment->delay_reason ?? $etaEvaluation['reason'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule & Customer Meta -->
        <div class="p-6 sm:p-7 rounded-3xl bg-white border border-cream-200/80 shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)] space-y-4">
            <h3 class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Jadwal &amp; Kepemilikan</h3>

            <div class="space-y-3 text-xs">
                <div class="p-3 rounded-2xl bg-cream-50 border border-cream-200/70 space-y-1.5">
                    <div>
                        <div class="text-espresso-500 text-[11px] font-semibold">Target Tiba Awal (Expected):</div>
                        <div class="text-xs font-bold text-espresso-800">
                            {{ $shipment->expected_delivery_at ? $shipment->expected_delivery_at->format('d M Y · H:i') : ($shipment->estimated_delivery_at ? $shipment->estimated_delivery_at->format('d M Y · H:i') : 'Belum Ditetapkan') }}
                        </div>
                    </div>
                    <div class="pt-1.5 border-t border-cream-200/80">
                        <div class="text-espresso-500 text-[11px] font-semibold">Estimasi Terbaru (Estimated):</div>
                        <div class="text-sm font-extrabold {{ $shipment->delay_hours > 0 ? 'text-rose-600' : 'text-espresso-900' }}">
                            {{ $shipment->estimated_delivery_at ? $shipment->estimated_delivery_at->format('d M Y · H:i') : 'Menunggu Kalkulasi' }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between px-1">
                    <div>
                        <div class="text-espresso-500">Aktual Diterima:</div>
                        <div class="text-xs font-bold text-espresso-800 mt-0.5">
                            {{ $shipment->actual_delivery_at ? $shipment->actual_delivery_at->format('d M Y H:i') : 'Dalam Proses' }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-espresso-500">Selisih Waktu:</div>
                        <div class="text-xs font-mono font-bold mt-0.5">
                            @if($shipment->delay_hours > 0)
                                <span class="text-rose-600">+{{ $shipment->delay_hours }} jam (Terlambat)</span>
                            @else
                                <span class="text-sage-700">Tepat Waktu (0j)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-cream-200">
                    <div class="text-espresso-500">Pelanggan Penerima:</div>
                    <div class="text-sm font-extrabold text-espresso-900 mt-0.5">{{ $shipment->customer?->name ?? 'N/A' }}</div>
                    <div class="text-[11px] text-espresso-500">{{ $shipment->customer?->email ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: TIMELINE + EXCEPTIONS & CARGO ITEMS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT 2 COLS: TIMELINE + EXCEPTIONS -->
        <div class="lg:col-span-2 space-y-6">
            <!-- SHIPMENT EXCEPTIONS PANEL -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-cream-200/80 shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-espresso-900">Manajemen Kendala (Exceptions)</h2>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cream-100 text-[11px] font-mono font-bold text-espresso-600">{{ $exceptions->count() }} catatan</span>
                </div>

                @if($exceptions->isEmpty())
                    <p class="text-xs text-espresso-500 py-2">Tidak ada gangguan/exception operasional yang dilaporkan pada pengiriman ini.</p>
                @else
                    <div class="space-y-3">
                        @foreach($exceptions as $ex)
                            <div class="p-4 rounded-2xl border space-y-2.5 text-xs
                                {{ $ex->status === 'RESOLVED' ? 'bg-cream-50/70 border-cream-200' : 'bg-rose-50/50 border-rose-200' }}">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                            {{ $ex->status === 'RESOLVED' ? 'bg-sage-100 text-sage-700' : ($ex->status === 'INVESTIGATING' ? 'bg-apricot-100 text-apricot-600' : 'bg-rose-600 text-white') }}">
                                            {{ $ex->status }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full bg-white border border-cream-200 text-[10px] font-mono font-bold text-espresso-800">
                                            {{ $ex->type }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full bg-cream-100 text-[10px] font-bold text-espresso-700">
                                            Severity: {{ $ex->severity }}
                                        </span>
                                    </div>
                                    <span class="font-mono text-[11px] text-espresso-500">
                                        Terdeteksi: {{ $ex->detected_at->format('d M Y H:i') }}
                                    </span>
                                </div>

                                <p class="text-espresso-900 font-medium">{{ $ex->description }}</p>

                                @if($ex->resolution_notes)
                                    <div class="p-2.5 rounded-xl bg-white border border-cream-200 text-[11px] text-sage-700">
                                        <strong>Catatan Penyelesaian:</strong> {{ $ex->resolution_notes }}
                                        @if($ex->resolutionDurationHours() !== null)
                                            <span class="font-mono text-espresso-500 ml-1">({{ $ex->resolutionDurationHours() }} jam)</span>
                                        @endif
                                    </div>
                                @endif

                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()) && !$ex->isResolved())
                                    <div class="pt-2 border-t border-cream-200/80 flex flex-wrap items-center gap-2">
                                        @if($ex->status === 'OPEN')
                                            <form method="POST" action="{{ route('exceptions.update', $ex) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="INVESTIGATING">
                                                <button type="submit" class="px-3 py-1 rounded-full bg-apricot-100 hover:bg-apricot-500 hover:text-white text-apricot-600 text-[11px] font-bold transition-colors cursor-pointer">
                                                    Tandai Investigasi
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('exceptions.update', $ex) }}" class="flex items-center gap-1.5 flex-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="RESOLVED">
                                            <input type="text" name="resolution_notes" placeholder="Catatan penyelesaian kendala..." class="flex-1 px-3 py-1 bg-white border border-cream-200 rounded-full text-[11px] text-espresso-900 focus:outline-none focus:ring-1 focus:ring-sage-500">
                                            <button type="submit" class="px-3.5 py-1 rounded-full bg-sage-500 hover:bg-sage-600 text-white text-[11px] font-bold transition-colors cursor-pointer shrink-0">
                                                Selesaikan
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- VERTICAL TIMELINE LEDGER -->
            <div class="p-6 sm:p-7 rounded-3xl bg-white border border-cream-200/80 shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)] space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-cream-200">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 text-sage-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-espresso-900">Catatan Riwayat Pengiriman (Timeline)</h2>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-cream-100 text-[11px] font-mono font-bold text-espresso-600">{{ $timeline->count() }} checkpoint tercatat</span>
                </div>

                @if($timeline->isEmpty())
                    <div class="py-12 text-center text-espresso-500 space-y-2">
                        <svg class="w-8 h-8 mx-auto text-espresso-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm font-medium">Belum ada catatan riwayat checkpoint untuk pengiriman ini.</p>
                    </div>
                @else
                    <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-cream-200">
                        @foreach($timeline as $index => $event)
                            <div class="relative group">
                                <div class="absolute -left-6 sm:-left-8 top-1.5 w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white transition-all
                                    @if($event->status === 'DELIVERED') bg-sage-600 text-white shadow-md shadow-sage-500/30
                                    @elseif($event->status === 'IN_TRANSIT') bg-sage-500 text-white shadow-md shadow-sage-500/25
                                    @elseif($event->status === 'PICKED_UP') bg-apricot-500 text-white
                                    @elseif($event->status === 'EXCEPTION') bg-rose-500 text-white
                                    @elseif($event->status === 'CANCELLED') bg-rose-600 text-white
                                    @else bg-apricot-400 text-white
                                    @endif">
                                    @if($event->status === 'DELIVERED')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($event->status === 'EXCEPTION')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    @else
                                        <span class="text-[10px]">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                <div class="p-4 rounded-2xl bg-cream-50/80 border border-cream-200 hover:border-sage-300 transition-colors space-y-2">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase
                                                @if($event->status === 'DELIVERED') bg-sage-100 text-sage-700
                                                @elseif($event->status === 'IN_TRANSIT') bg-sage-500 text-white
                                                @elseif($event->status === 'EXCEPTION') bg-rose-100 text-rose-700
                                                @else bg-apricot-100 text-apricot-600
                                                @endif">
                                                {{ $event->status }}
                                            </span>
                                            <span class="text-sm font-extrabold text-espresso-900">{{ $event->location_name }}</span>
                                        </div>
                                        <div class="text-[11px] font-mono font-semibold text-espresso-500">
                                            {{ $event->event_time->format('d M Y · H:i:s') }} UTC
                                        </div>
                                    </div>

                                    <p class="text-xs text-espresso-800 leading-relaxed">{{ $event->description }}</p>

                                    <div class="pt-2 flex flex-wrap items-center justify-between gap-2 text-[11px] text-espresso-500 border-t border-cream-200/70">
                                        <div class="flex items-center gap-3">
                                            @if($event->latitude && $event->longitude)
                                                <span class="font-mono text-espresso-600 flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                    {{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}
                                                </span>
                                            @endif
                                            @if($event->location)
                                                <span class="font-semibold text-sage-700">Hub: {{ $event->location->code }}</span>
                                            @endif
                                        </div>

                                        @if($event->creator)
                                            <span class="text-espresso-500">Dicatat oleh: <strong class="text-espresso-900">{{ $event->creator->name }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- CARGO ITEMS MANIFEST (1 col) -->
        <div class="space-y-6">
            <div class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-cream-200">
                    <h3 class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Rincian Muatan Barang</h3>
                    <span class="px-2.5 py-0.5 rounded-full bg-sage-100 text-[11px] font-mono font-bold text-sage-700">{{ $shipment->items->count() }} barang</span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->items as $item)
                        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200 space-y-2 text-xs">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-extrabold text-espresso-900">{{ $item->description }}</span>
                                <span class="font-mono font-bold text-espresso-600 text-[11px]">{{ $item->quantity }} unit</span>
                            </div>
                            <div class="flex items-center justify-between text-espresso-600 text-[11px] pt-1.5 border-t border-cream-200/80">
                                <span>Berat: <strong class="text-espresso-900">{{ number_format($item->weight_kg, 1) }} kg</strong></span>
                                <span>Nilai: <strong class="text-apricot-600">${{ number_format($item->value_amount, 2) }}</strong></span>
                            </div>
                            @if($item->hs_code)
                                <div class="text-[10px] font-mono text-espresso-500">Kode HS: {{ $item->hs_code }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-espresso-500 py-3 text-center">Tidak ada data barang tercatat.</div>
                    @endforelse
                </div>

                @if($shipment->notes)
                    <div class="pt-3 border-t border-cream-200 space-y-1.5">
                        <span class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider">Catatan Operasional</span>
                        <p class="text-xs text-espresso-800 italic bg-cream-50 p-3 rounded-2xl border border-cream-200">{{ $shipment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- REST API ENDPOINT REFERENCE -->
            <div class="p-6 rounded-3xl bg-sage-500 text-white shadow-[0_14px_35px_-15px_rgba(135,152,128,0.45)] space-y-3 text-xs">
                <div class="flex items-center gap-2 font-extrabold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span>Akses REST API Developer</span>
                </div>
                <p class="text-sage-100 text-[11px]">Endpoint JSON pelacakan, ETA, dan kendala:</p>
                <div class="p-3 rounded-2xl bg-white/15 backdrop-blur-xs font-mono text-[10px] text-white break-all select-all space-y-1">
                    <div>GET /api/v1/tracking/{{ $shipment->tracking_number }}</div>
                    <div>GET /api/v1/shipments/{{ $shipment->id }}/eta</div>
                    <div>GET /api/v1/shipments/{{ $shipment->id }}/exceptions</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS FOR OPERATOR & ADMIN -->
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()) && !$shipment->isTerminal())
<!-- 1. MODAL: ADD TRACKING CHECKPOINT -->
<div id="eventModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div class="space-y-0.5">
                <h3 class="text-lg font-extrabold text-espresso-900">Catat Titik Checkpoint Baru</h3>
                <p class="text-xs text-espresso-500">Tambahkan riwayat permanen untuk <span class="font-mono font-bold text-sage-700">{{ $shipment->tracking_number }}</span></p>
            </div>
            <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shipments.events.store', $shipment->tracking_number) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="eventStatus" class="block text-xs font-bold text-espresso-800 mb-1.5">Status Checkpoint *</label>
                <select id="eventStatus" name="status" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                    <option value="{{ $shipment->status }}">{{ $shipment->status }} (Pertahankan status &bull; Pemindaian Transit)</option>
                    @foreach(\App\Models\Shipment::ALLOWED_TRANSITIONS[$shipment->status] ?? [] as $nextStatus)
                        <option value="{{ $nextStatus }}">&rarr; Ubah status ke {{ $nextStatus }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="eventLocationId" class="block text-xs font-bold text-espresso-800 mb-1.5">Hub / Lokasi Logistik</label>
                <select id="eventLocationId" name="location_id" onchange="onLocationChange(this)" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                    <option value="">-- Pilih Hub atau Masukkan Nama Lokasi Kustom --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" data-name="{{ $loc->name }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}">
                            {{ $loc->name }} ({{ $loc->code }}) - {{ $loc->country }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="eventLocationName" class="block text-xs font-bold text-espresso-800 mb-1.5">Nama Titik Lokasi (Opsional)</label>
                <input id="eventLocationName" type="text" name="location_name" placeholder="cth: Dermaga 04, Pelabuhan Singapura" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="eventLat" class="block text-xs font-bold text-espresso-800 mb-1.5">Lintang (Latitude)</label>
                    <input id="eventLat" type="number" step="0.000001" name="latitude" placeholder="cth: 1.290270" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label for="eventLng" class="block text-xs font-bold text-espresso-800 mb-1.5">Bujur (Longitude)</label>
                    <input id="eventLng" type="number" step="0.000001" name="longitude" placeholder="cth: 103.851959" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div>
                <label for="eventTime" class="block text-xs font-bold text-espresso-800 mb-1.5">Waktu Kejadian</label>
                <input id="eventTime" type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div>
                <label for="eventDesc" class="block text-xs font-bold text-espresso-800 mb-1.5">Keterangan Checkpoint *</label>
                <textarea id="eventDesc" name="description" rows="3" required placeholder="Jelaskan status kejadian..." class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-sm text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('eventModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 text-xs font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 cursor-pointer">Simpan Checkpoint</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL: REPORT EXCEPTION -->
<div id="exceptionModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h3 class="text-lg font-extrabold text-espresso-900">Laporkan Kendala Pengiriman</h3>
                <p class="text-xs text-espresso-500">Catat gangguan operasional untuk {{ $shipment->tracking_number }}</p>
            </div>
            <button onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shipments.exceptions.store', $shipment->tracking_number) }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Jenis Kendala *</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach(\App\Models\ShipmentException::TYPES as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Tingkat Keparahan *</label>
                    <select name="severity" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach(\App\Models\ShipmentException::SEVERITIES as $sev)
                            <option value="{{ $sev }}" {{ $sev === 'MEDIUM' ? 'selected' : '' }}>{{ $sev }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Perbarui Estimasi Tiba (Opsional jika memicu delay)</label>
                <input type="datetime-local" name="new_estimated_delivery_at" class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Deskripsi Kendala *</label>
                <textarea name="description" rows="3" required placeholder="cth: Kontainer tertahan pemeriksaan fisik bea cukai di pelabuhan transit..." class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg cursor-pointer">Kirim Laporan Kendala</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL: RECALCULATE ETA -->
<div id="etaModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-md rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h3 class="text-lg font-extrabold text-espresso-900">Atur Ulang Estimasi (ETA)</h3>
                <p class="text-xs text-espresso-500">Bandingkan terhadap target tiba awal dan evaluasi aturan keterlambatan.</p>
            </div>
            <button onclick="document.getElementById('etaModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shipments.eta.update', $shipment->tracking_number) }}" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PATCH')

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Estimasi Tiba Baru *</label>
                <input type="datetime-local" name="estimated_delivery_at" required value="{{ $shipment->estimated_delivery_at?->format('Y-m-d\TH:i') ?? now()->addDays(5)->format('Y-m-d\TH:i') }}"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Alasan Perubahan Jadwal</label>
                <input type="text" name="reason" placeholder="cth: Antrean sandar pelabuhan (Port Congestion)"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('etaModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-sage-500 hover:bg-sage-600 text-white font-bold shadow-lg cursor-pointer">Kalkulasi Ulang ETA</button>
            </div>
        </form>
    </div>
</div>

<script>
function onLocationChange(select) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.value) {
        document.getElementById('eventLocationName').value = selectedOption.dataset.name || '';
        document.getElementById('eventLat').value = selectedOption.dataset.lat || '';
        document.getElementById('eventLng').value = selectedOption.dataset.lng || '';
    }
}
</script>
@endif
@endsection
