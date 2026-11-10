@extends('layouts.dashboard')

@section('title', 'Pelabuhan & Lokasi')
@section('page_title', 'Pelabuhan & Hub Global')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-espresso-900">Daftar Pelabuhan &amp; Hub Logistik Global</h1>
            <p class="text-xs text-espresso-500 mt-0.5">Lokasi hub berstandar UN/LOCODE, pos bea cukai, dan pusat distribusi beserta koordinat GPS.</p>
        </div>

        @if(auth()->check() && auth()->user()->can('create', \App\Models\Location::class))
            <button onclick="document.getElementById('newLocationModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Lokasi</span>
            </button>
        @endif
    </div>

    <!-- Flash Notifications -->
    @if(session('status'))
        <div class="p-4 rounded-2xl bg-sage-100 border border-sage-200 text-sage-700 text-sm font-semibold flex items-center gap-2.5 shadow-xs">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <div class="font-bold text-sm">Kesalahan Validasi:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="p-5 rounded-3xl bg-white border border-cream-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('locations.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama hub, UN/LOCODE, kota, atau negara..."
                   class="flex-1 px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            <select name="type" class="px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs font-semibold text-espresso-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                <option value="">Semua Tipe Fasilitas</option>
                @foreach(\App\Models\Location::TYPES as $type)
                    <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', $type) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                Saring Data
            </button>
            @if(!empty($filters['search']) || !empty($filters['type']))
                <a href="{{ route('locations.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-espresso-700 text-xs font-bold flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 flex-wrap text-[11px] font-bold">
            <a href="{{ route('locations.index') }}" class="px-3.5 py-1.5 rounded-full {{ empty($filters['type']) ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                Semua Hub
            </a>
            @foreach(\App\Models\Location::TYPES as $type)
                <a href="{{ route('locations.index', ['type' => $type]) }}" class="px-3.5 py-1.5 rounded-full {{ ($filters['type'] ?? '') === $type ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                    {{ str_replace('_', ' ', $type) }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Locations Table -->
    <div class="rounded-3xl bg-white border border-cream-200/80 overflow-hidden shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nama Hub / Fasilitas</th>
                        <th class="px-5 py-3.5">UN/LOCODE</th>
                        <th class="px-5 py-3.5">Tipe</th>
                        <th class="px-5 py-3.5">Kota / Negara</th>
                        <th class="px-5 py-3.5">Koordinat GPS</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-espresso-800">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-cream-50/80 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-espresso-900">{{ $loc->name }}</div>
                                <div class="text-[11px] text-espresso-500 truncate max-w-xs">{{ $loc->address }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-mono font-bold text-[11px]">{{ $loc->code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-bold text-[10px] uppercase">{{ str_replace('_', ' ', $loc->type) }}</span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-espresso-900">
                                {{ $loc->city }}, {{ $loc->country }}
                            </td>
                            <td class="px-5 py-4 font-mono text-[11px] text-espresso-600">
                                {{ number_format($loc->latitude, 4) }}, {{ number_format($loc->longitude, 4) }}
                            </td>
                            @if(auth()->user()?->isAdmin())
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('locations.destroy', $loc) }}" onsubmit="return confirm('Hapus lokasi {{ $loc->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 text-[11px] font-bold transition-colors cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-espresso-500">Belum ada lokasi hub yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="p-4 border-t border-cream-200 bg-cream-50/50">
                {{ $locations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL: ADD LOCATION (Admin & Operator) -->
@if(auth()->check() && auth()->user()->can('create', \App\Models\Location::class))
<div id="newLocationModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-8 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h3 class="text-lg font-extrabold text-espresso-900">Tambah Hub Logistik</h3>
                <p class="text-xs text-espresso-500">Daftarkan pelabuhan, bandara, gudang, atau pusat distribusi baru.</p>
            </div>
            <button onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Nama Hub / Fasilitas *</label>
                <input type="text" name="name" required placeholder="cth: Pelabuhan Tanjung Perak Surabaya"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">UN/LOCODE *</label>
                    <input type="text" name="code" required placeholder="cth: IDSUB"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono uppercase text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Tipe Fasilitas *</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach(\App\Models\Location::TYPES as $type)
                            <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Kota *</label>
                    <input type="text" name="city" required placeholder="cth: Surabaya"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Negara *</label>
                    <input type="text" name="country" required placeholder="cth: Indonesia"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Lintang (Latitude) *</label>
                    <input type="number" step="0.000001" name="latitude" required placeholder="-7.2036"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Bujur (Longitude) *</label>
                    <input type="number" step="0.000001" name="longitude" required placeholder="112.7317"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Alamat Lengkap</label>
                <input type="text" name="address" placeholder="Jl. Perak Timur No. 610"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold shadow-lg shadow-apricot-500/25 cursor-pointer">
                    Simpan Lokasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
