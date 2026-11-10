@extends('layouts.dashboard')

@section('title', 'Daftar Pengiriman')
@section('page_title', 'Daftar Pengiriman')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-espresso-900">Daftar Pengiriman Global</h1>
            <p class="text-xs text-espresso-500 mt-0.5">Cari, saring, buat pengiriman baru, dan pantau siklus perjalanan secara menyeluruh.</p>
        </div>

        @if(auth()->check() && auth()->user()->can('create', \App\Models\Shipment::class))
            <button onclick="document.getElementById('newShipmentModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Pengiriman</span>
            </button>
        @endif
    </div>

    <!-- Flash Status / Errors -->
    @if(session('status'))
        <div class="p-4 rounded-2xl bg-sage-100 border border-sage-200 text-sage-700 text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <div class="font-bold text-sm">Gagal membuat pengiriman:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Status Filter Bar -->
    <div class="p-5 rounded-3xl bg-white border border-cream-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('shipments.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-espresso-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor resi, nama pelanggan, atau ekspedisi..."
                       class="w-full pl-10 pr-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>
            <select name="status" class="px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs font-semibold text-espresso-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                <option value="">Semua Status</option>
                @foreach(\App\Models\Shipment::STATUSES as $st)
                    <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                Terapkan Filter
            </button>
            @if(!empty($filters['search']) || !empty($filters['status']))
                <a href="{{ route('shipments.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-espresso-700 text-xs font-bold flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>

        <!-- Quick Status Filter Pills -->
        <div class="flex items-center gap-2 flex-wrap text-[11px] font-bold">
            <a href="{{ route('shipments.index') }}" class="px-3.5 py-1.5 rounded-full {{ empty($filters['status']) ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                Semua
            </a>
            @foreach(['CREATED', 'BOOKED', 'PICKED_UP', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'EXCEPTION'] as $st)
                <a href="{{ route('shipments.index', ['status' => $st]) }}" class="px-3.5 py-1.5 rounded-full {{ ($filters['status'] ?? '') === $st ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                    {{ $st }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Shipments Table -->
    <div class="rounded-3xl bg-white border border-cream-200/80 overflow-hidden shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Resi</th>
                        <th class="px-5 py-3.5">Pelanggan</th>
                        <th class="px-5 py-3.5">Ekspedisi</th>
                        <th class="px-5 py-3.5">Jalur Rute</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Estimasi Tiba</th>
                        <th class="px-5 py-3.5">Muatan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
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
                                    {{ $shipment->currentLocation ? 'Posisi saat ini: ' . $shipment->currentLocation->name : 'Menunggu keberangkatan awal' }}
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
                            <td colspan="8" class="px-5 py-12 text-center text-espresso-500">
                                Tidak ada data pengiriman yang sesuai dengan filter Anda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="p-4 border-t border-cream-200 bg-cream-50/50">
                {{ $shipments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL: CREATE NEW SHIPMENT -->
@if(auth()->check() && auth()->user()->can('create', \App\Models\Shipment::class))
<div id="newShipmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-8 space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h3 class="text-lg font-extrabold text-espresso-900">Buat Pengiriman Baru</h3>
                <p class="text-xs text-espresso-500">Nomor resi (CT-YYYYMMDD-XXXX) dibuat otomatis dengan status awal CREATED.</p>
            </div>
            <button onclick="document.getElementById('newShipmentModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shipments.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Pelanggan Penerima *</label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Ekspedisi / Maskapai *</label>
                    <select name="carrier_id" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}">{{ $car->name }} ({{ $car->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Lokasi Asal *</label>
                    <select name="origin_id" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }} ({{ $loc->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Lokasi Tujuan *</label>
                    <select name="destination_id" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $loop->index === 1 ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Estimasi Tanggal Tiba *</label>
                <input type="datetime-local" name="estimated_delivery_at" required value="{{ now()->addDays(10)->format('Y-m-d\TH:i') }}"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="p-4 rounded-2xl bg-cream-50 border border-cream-200 space-y-3">
                <div class="font-bold text-espresso-900 uppercase tracking-wider text-[11px]">Rincian Barang Muatan Utama</div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-3">
                        <input type="text" name="items[0][description]" required placeholder="Nama barang (cth: Komponen Elektronik)"
                               class="w-full px-3.5 py-2 bg-white border border-cream-200 rounded-xl text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sage-500">
                    </div>
                    <div>
                        <input type="number" name="items[0][quantity]" required min="1" value="10" placeholder="Jumlah (pcs)"
                               class="w-full px-3.5 py-2 bg-white border border-cream-200 rounded-xl text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sage-500">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="items[0][weight_kg]" required min="0" value="250" placeholder="Berat (kg)"
                               class="w-full px-3.5 py-2 bg-white border border-cream-200 rounded-xl text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sage-500">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="items[0][value_amount]" min="0" value="5000" placeholder="Nilai Barang ($)"
                               class="w-full px-3.5 py-2 bg-white border border-cream-200 rounded-xl text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sage-500">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Catatan Operasional</label>
                <textarea name="notes" rows="2" placeholder="Instruksi penanganan khusus..."
                          class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('newShipmentModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold shadow-lg shadow-apricot-500/25 cursor-pointer">
                    Simpan Pengiriman
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
