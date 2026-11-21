@extends('layouts.dashboard')

@section('title', __('Pelabuhan & Lokasi'))
@section('page_title', __('Pelabuhan & Hub Global'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-espresso-900">{{ __('Daftar Pelabuhan & Hub Logistik Global') }}</h1>
            <p class="text-xs text-espresso-500 mt-0.5">{{ __('Lokasi hub berstandar UN/LOCODE, pos bea cukai, dan pusat distribusi beserta koordinat GPS.') }}</p>
        </div>

        @if(auth()->check() && auth()->user()->can('create', \App\Models\Location::class))
            <button onclick="document.getElementById('newLocationModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>{{ __('Tambah Lokasi') }}</span>
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
            <div class="font-bold text-sm">{{ __('Kesalahan Validasi:') }}</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Global Interactive Hubs Map with Route Lanes & Distances -->
    <div class="p-5 sm:p-6 rounded-3xl glass-card-strong space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-0.5 rounded-full bg-sage-100 text-sage-800 text-[10px] font-bold uppercase tracking-wider">{{ __('Jalur Rute Maritim & Jarak Global') }}</span>
                    <span class="text-xs text-espresso-500">Haversine GPS Telemetry</span>
                </div>
                <h2 class="text-lg font-extrabold text-espresso-900">{{ __('Peta Jaringan Pelabuhan & Hub Dunia') }}</h2>
                <p class="text-[11px] text-espresso-500">{{ __('Klik garis rute atau marker untuk melihat jarak maritim (km & NM) dan waktu tempuh estimasi.') }}</p>
            </div>

            <!-- Controls & Toggles -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Map Layer Switcher: Google Satellite vs Vector -->
                <div class="inline-flex p-1 rounded-full bg-cream-100 border border-cream-200 text-xs font-bold shadow-2xs">
                    <button type="button" id="btnSatelliteMode" onclick="setMapBasemap('satellite')" class="px-3.5 py-1 rounded-full bg-emerald-600 text-white shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                        <span>🛰️ {{ __('Citra Satelit Google') }}</span>
                    </button>
                    <button type="button" id="btnStreetMode" onclick="setMapBasemap('street')" class="px-3.5 py-1 rounded-full text-espresso-700 hover:text-espresso-900 transition-all flex items-center gap-1.5 cursor-pointer">
                        <span>🗺️ {{ __('Peta Vektor') }}</span>
                    </button>
                </div>

                <button type="button" id="toggleDistancesBtn" onclick="toggleRouteDistances()" class="px-3.5 py-1.5 rounded-full bg-white border border-cream-200 text-espresso-700 hover:bg-cream-100 text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-apricot-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span id="toggleDistancesText">{{ __('Label Jarak: AKTIF') }}</span>
                </button>
                <button type="button" id="toggleSeaBtn" onclick="toggleSeaLanes()" class="px-3.5 py-1.5 rounded-full bg-sage-500 text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
                    <span>🚢 {{ __('Jalur Laut') }}</span>
                </button>
                <button type="button" id="toggleAirBtn" onclick="toggleAirLanes()" class="px-3.5 py-1.5 rounded-full bg-purple-500 text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
                    <span>✈️ {{ __('Jalur Udara') }}</span>
                </button>
            </div>
        </div>

        <!-- Corridor Route Legend Badges with Distances -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 text-xs">
            <div onclick="focusCorridor('sea_europe')" class="p-2.5 rounded-2xl bg-orange-50/70 border border-orange-200/80 hover:bg-orange-100/70 hover:border-orange-300 transition-all cursor-pointer flex items-center justify-between shadow-2xs group" title="Klik untuk zoom ke koridor ini">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#EA580C] shadow-xs group-hover:scale-110 transition-transform"></span>
                    <span class="font-bold text-orange-950">{{ __('Koridor SEA → Eropa') }}</span>
                </div>
                <span class="font-mono text-[11px] font-extrabold text-[#EA580C]">~16,577 km</span>
            </div>

            <div onclick="focusCorridor('asia_america')" class="p-2.5 rounded-2xl bg-sky-50/70 border border-sky-200/80 hover:bg-sky-100/70 hover:border-sky-300 transition-all cursor-pointer flex items-center justify-between shadow-2xs group" title="Klik untuk zoom ke koridor ini">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#0284C7] shadow-xs group-hover:scale-110 transition-transform"></span>
                    <span class="font-bold text-sky-950">{{ __('Koridor Asia → Amerika') }}</span>
                </div>
                <span class="font-mono text-[11px] font-extrabold text-[#0284C7]">~10,574 km</span>
            </div>

            <div onclick="focusCorridor('sea_australia')" class="p-2.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 hover:bg-emerald-100/70 hover:border-emerald-300 transition-all cursor-pointer flex items-center justify-between shadow-2xs group" title="Klik untuk zoom ke koridor ini">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#10B981] shadow-xs group-hover:scale-110 transition-transform"></span>
                    <span class="font-bold text-emerald-950">{{ __('Koridor SEA → Australia') }}</span>
                </div>
                <span class="font-mono text-[11px] font-extrabold text-[#10B981]">~7,219 km</span>
            </div>

            <div onclick="focusCorridor('air_feeder')" class="p-2.5 rounded-2xl bg-purple-50/70 border border-purple-200/80 hover:bg-purple-100/70 hover:border-purple-300 transition-all cursor-pointer flex items-center justify-between shadow-2xs group" title="Klik untuk zoom ke koridor ini">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#8B5CF6] shadow-xs group-hover:scale-110 transition-transform"></span>
                    <span class="font-bold text-purple-950">{{ __('Jalur Udara / Feeder') }}</span>
                </div>
                <span class="font-mono text-[11px] font-extrabold text-[#8B5CF6]">~884 km</span>
            </div>
        </div>

        <!-- The Map Container -->
        <div id="locationsWorldMap" class="w-full h-80 sm:h-96 lg:h-[430px] rounded-2xl border border-cream-200 bg-cream-100 z-10 overflow-hidden shadow-inner"></div>

        <div class="flex items-center justify-between text-[11px] text-espresso-500 px-1">
            <span>&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" class="underline hover:text-espresso-800">OpenStreetMap</a> contributors &bull; CargoTrace Global AIS Maritime Corridors</span>
            <span class="font-mono font-bold text-sage-700">1 NM = 1.852 km</span>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="p-5 rounded-3xl glass-card-strong space-y-4">
        <form method="GET" action="{{ route('locations.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Cari nama hub, UN/LOCODE, kota, atau negara...') }}"
                   class="flex-1 px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            <select name="type" class="px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs font-semibold text-espresso-800 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                <option value="">{{ __('Semua Tipe Fasilitas') }}</option>
                @foreach(\App\Models\Location::TYPES as $type)
                    <option value="{{ $type }}" {{ ($filters['type'] ?? '') === $type ? 'selected' : '' }}>{{ str_replace('_', ' ', $type) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                {{ __('Saring Data') }}
            </button>
            @if(!empty($filters['search']) || !empty($filters['type']))
                <a href="{{ route('locations.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-espresso-700 text-xs font-bold flex items-center justify-center">
                    {{ __('Reset') }}
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 flex-wrap text-[11px] font-bold">
            <a href="{{ route('locations.index') }}" class="px-3.5 py-1.5 rounded-full {{ empty($filters['type']) ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                {{ __('Semua Hub') }}
            </a>
            @foreach(\App\Models\Location::TYPES as $type)
                <a href="{{ route('locations.index', ['type' => $type]) }}" class="px-3.5 py-1.5 rounded-full {{ ($filters['type'] ?? '') === $type ? 'bg-sage-500 text-white' : 'bg-cream-100 text-espresso-600 hover:bg-cream-200' }} transition-colors">
                    {{ str_replace('_', ' ', $type) }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Locations Table -->
    <div class="rounded-3xl glass-card-strong overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('Nama Hub / Fasilitas') }}</th>
                        <th class="px-5 py-3.5">UN/LOCODE</th>
                        <th class="px-5 py-3.5">{{ __('Tipe') }}</th>
                        <th class="px-5 py-3.5">{{ __('Kota / Negara') }}</th>
                        <th class="px-5 py-3.5">{{ __('Koordinat GPS') }}</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="px-5 py-3.5 text-right">{{ __('Aksi') }}</th>
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
                                            {{ __('Hapus') }}
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-espresso-500">{{ __('Belum ada lokasi hub yang terdaftar.') }}</td>
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
                <h3 class="text-lg font-extrabold text-espresso-900">{{ __('Tambah Hub Logistik') }}</h3>
                <p class="text-xs text-espresso-500">{{ __('Daftarkan pelabuhan, bandara, gudang, atau pusat distribusi baru.') }}</p>
            </div>
            <button onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <!-- Geocoding External API Lookup Box -->
            <div class="p-3.5 rounded-2xl bg-sage-50 border border-sage-200 space-y-2">
                <label class="block font-bold text-sage-700 text-[11px] uppercase tracking-wider">{{ __('Cari Koordinat Otomatis (External Geocoding API)') }}</label>
                <div class="flex gap-2">
                    <input id="geocodeQuery" type="text" placeholder="{{ __('Ketik pelabuhan/kota (cth: Port of Tokyo)') }}"
                           class="flex-1 px-3 py-2 bg-white border border-sage-200 rounded-xl text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sage-500">
                    <button type="button" onclick="runGeocodeSearch()" class="px-4 py-2 rounded-xl bg-sage-500 hover:bg-sage-600 text-white font-bold transition-colors cursor-pointer shrink-0">
                        {{ __('Cari Koordinat') }}
                    </button>
                </div>
                <div id="geocodeStatus" class="text-[11px] text-espresso-600"></div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">{{ __('Nama Hub / Fasilitas *') }}</label>
                <input id="locNameInput" type="text" name="name" required placeholder="cth: Pelabuhan Tanjung Perak Surabaya"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">UN/LOCODE *</label>
                    <input type="text" name="code" required placeholder="cth: IDSUB"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono uppercase text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">{{ __('Tipe Fasilitas *') }}</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                        @foreach(\App\Models\Location::TYPES as $type)
                            <option value="{{ $type }}">{{ str_replace('_', ' ', $type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">{{ __('Kota *') }}</label>
                    <input id="locCityInput" type="text" name="city" required placeholder="cth: Surabaya"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">{{ __('Negara *') }}</label>
                    <input id="locCountryInput" type="text" name="country" required placeholder="cth: Indonesia"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">{{ __('Lintang (Latitude) *') }}</label>
                    <input id="locLatInput" type="number" step="0.000001" name="latitude" required placeholder="-7.2036"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">{{ __('Bujur (Longitude) *') }}</label>
                    <input id="locLngInput" type="number" step="0.000001" name="longitude" required placeholder="112.7317"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">{{ __('Alamat Lengkap') }}</label>
                <input type="text" name="address" placeholder="Jl. Perak Timur No. 610"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">
                    {{ __('Batal') }}
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold shadow-lg shadow-apricot-500/25 cursor-pointer">
                    {{ __('Simpan Lokasi') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@php
    $allHubs = \App\Models\Location::select('id', 'name', 'code', 'type', 'city', 'country', 'latitude', 'longitude')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get()
        ->map(fn ($l) => [
            'id' => $l->id,
            'name' => $l->name,
            'code' => $l->code,
            'type' => $l->type,
            'city' => $l->city,
            'country' => $l->country,
            'lat' => (float) $l->latitude,
            'lng' => (float) $l->longitude,
        ])->values()->all();
@endphp

<style>
.custom-hub-pin {
    display: flex;
    align-items: center;
    justify-content: center;
}
.route-distance-badge {
    background: transparent !important;
    border: none !important;
}
.route-distance-pill {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(4px);
    border: 1.5px solid;
    padding: 2.5px 8px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 800;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    white-space: nowrap;
    box-shadow: 0 4px 12px rgba(44,39,36,0.18);
    display: inline-flex;
    align-items: center;
    gap: 4.5px;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.route-distance-pill:hover {
    transform: scale(1.08);
    box-shadow: 0 6px 16px rgba(44,39,36,0.25);
}
</style>

<script>
let worldMap = null;
let googleHybridLayer = null;
let osmVectorLayer = null;
let distanceMarkersLayer = null;
let seaLanesLayer = null;
let airLanesLayer = null;
let isDistancesVisible = true;
let isSeaVisible = true;
let isAirVisible = true;
let corridorBounds = {};

document.addEventListener('DOMContentLoaded', function () {
    initWorldLocationsMap();
});

function initWorldLocationsMap() {
    const mapEl = document.getElementById('locationsWorldMap');
    if (!mapEl || typeof window.L === 'undefined') return;

    const hubs = {{ Illuminate\Support\Js::from($allHubs) }};
    const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));

    // Initialize Map centered on global sea trade crossroads (Singapore/Equator)
    worldMap = window.L.map('locationsWorldMap', {
        scrollWheelZoom: false,
        worldCopyJump: false,
        minZoom: 2,
        maxZoom: 20
    }).setView([18.0, 85.0], 2);

    // 1. Google Maps / Earth Hybrid Satellite Layer (Lush green forests, realistic blue ocean, terrain, roads & labels)
    googleHybridLayer = window.L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        subdomains: ['0', '1', '2', '3'],
        maxZoom: 20,
        attribution: '&copy; Google Earth / Maps Satellite &bull; &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    // 2. OpenStreetMap Vector Basemap
    osmVectorLayer = window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    // Default to Google Hybrid Satellite!
    googleHybridLayer.addTo(worldMap);

    distanceMarkersLayer = window.L.layerGroup().addTo(worldMap);
    seaLanesLayer = window.L.layerGroup().addTo(worldMap);
    airLanesLayer = window.L.layerGroup().addTo(worldMap);

    // Connected routes dictionary for hubs
    const hubConnectedRoutes = {
        'IDTPP': ['SEA &rarr; Eropa (~16,577 km)', 'SEA &rarr; Australia (~7,219 km)'],
        'SGSIN': ['SEA &rarr; Eropa (~16,577 km)', 'SEA &rarr; Australia (~7,219 km)'],
        'NLRTM': ['SEA &rarr; Eropa (via Terusan Suez)'],
        'DEHAM': ['SEA &rarr; Eropa (Feeder Rotterdam)'],
        'CNSHA': ['Asia &rarr; Amerika Utara (~10,574 km)'],
        'JPYOK': ['Asia &rarr; Amerika Utara (Trans-Pasifik)'],
        'USLAX': ['Asia &rarr; Amerika Utara (~10,574 km)'],
        'AUSYD': ['SEA &rarr; Australia (Port Botany)'],
        'AUMEL': ['SEA &rarr; Australia (Melbourne Hub)'],
        'IDCGK': ['Jalur Udara / Feeder CGK &harr; SINAC (884 km)'],
        'SINAC': ['Jalur Udara / Feeder CGK &harr; SINAC (884 km)']
    };

    // 1. Plot Port & Location Markers
    const allPinsBounds = [];
    hubs.forEach(hub => {
        if (hub.lat && hub.lng) {
            const pt = [parseFloat(hub.lat), parseFloat(hub.lng)];
            allPinsBounds.push(pt);

            const isAirport = hub.type === 'AIRPORT';
            const pinColor = isAirport ? '#8B5CF6' : (hub.type === 'CUSTOMS' ? '#E11D48' : '#879880');
            const pinBorder = '#FFFFFF';
            const iconSymbol = isAirport ? '✈' : '⚓';

            const icon = window.L.divIcon({
                className: 'custom-hub-pin',
                html: `<div style="background:${pinColor};border:2.5px solid ${pinBorder};width:24px;height:24px;border-radius:9999px;box-shadow:0 3px 10px rgba(44,39,36,0.3);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;">${iconSymbol}</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12],
                popupAnchor: [0, -14],
            });

            const connected = hubConnectedRoutes[hub.code] || [];
            let connectedHtml = '';
            if (connected.length > 0) {
                connectedHtml = `<div style="margin-top:6px;padding-top:6px;border-top:1px solid #EAE6DF;font-size:11px;">
                    <strong style="color:#6B7D64;">{{ __('Rute Maritim Terhubung') }}:</strong>
                    <ul style="margin:2px 0 0 14px;padding:0;color:#2C2724;">
                        ${connected.map(r => `<li>${r}</li>`).join('')}
                    </ul>
                </div>`;
            }

            window.L.marker(pt, { icon })
                .addTo(worldMap)
                .bindPopup(`
                    <div style="font-family:sans-serif;font-size:12px;line-height:1.45;min-width:200px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:3px;">
                            <strong style="font-size:13px;color:#2C2724;">${esc(hub.name)}</strong>
                            <span style="background:#E2E8DF;color:#4F5E4A;padding:1px 6px;border-radius:9999px;font-family:monospace;font-weight:bold;font-size:10px;">${esc(hub.code)}</span>
                        </div>
                        <div style="color:#665E57;font-size:11px;">
                            <span>${esc(hub.city)}, ${esc(hub.country)}</span> &bull; 
                            <span style="font-weight:600;color:#E07A2B;">${esc(hub.type)}</span>
                        </div>
                        <div style="font-family:monospace;font-size:10px;color:#879880;margin-top:2px;">
                            GPS: ${esc(hub.lat.toFixed(4))}, ${esc(hub.lng.toFixed(4))}
                        </div>
                        ${connectedHtml}
                    </div>
                `);
        }
    });

    // 2. Define Comprehensive Global Maritime Corridors & Distance Lines
    const globalCorridors = [
        // ================= KORIDOR 1: SEA -> EROPA =================
        {
            id: 'sea_europe',
            type: 'sea',
            name: '{{ __('Koridor SEA → Eropa') }}',
            color: '#EA580C',
            sla: '{{ __('Batas Keterlambatan SLA:') }} 12 {{ __('jam') }}',
            exception: '{{ __('Fokus Kendala Utama:') }} CUSTOMS_HOLD',
            segments: [
                {
                    from: 'Tanjung Priok (IDTPP)',
                    to: 'Port of Singapore (SGSIN)',
                    km: 887,
                    nm: 479,
                    transit: '1.5 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [-2.4, 105.5],
                    coords: [
                        [-6.1018, 106.8833],
                        [-2.0000, 105.4000],
                        [1.2644, 103.8220]
                    ]
                },
                {
                    from: 'Port of Singapore (SGSIN)',
                    to: 'Port of Rotterdam (NLRTM)',
                    km: 15200,
                    nm: 8200,
                    transit: '20-24 {{ __('Hari Transit Pelayaran') }} (Via Terusan Suez)',
                    mid: [21.5, 38.0],
                    coords: [
                        [1.2644, 103.8220],
                        [5.5000, 96.5000],
                        [6.0000, 80.5000],
                        [11.5000, 52.0000],
                        [12.6000, 43.5000],
                        [27.8000, 34.2000],
                        [31.3000, 32.3000],
                        [36.5000, 14.5000],
                        [36.0000, -5.5000],
                        [43.5000, -9.5000],
                        [49.5000, -4.0000],
                        [51.9244, 4.4777]
                    ]
                },
                {
                    from: 'Port of Rotterdam (NLRTM)',
                    to: 'Port of Hamburg (DEHAM)',
                    km: 490,
                    nm: 265,
                    transit: '1 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [53.6, 6.8],
                    coords: [
                        [51.9244, 4.4777],
                        [53.2000, 5.2000],
                        [54.0000, 7.8000],
                        [53.5353, 9.9868]
                    ]
                }
            ]
        },

        // ================= KORIDOR 2: ASIA -> AMERIKA UTARA =================
        {
            id: 'asia_america',
            type: 'sea',
            name: '{{ __('Koridor Asia → Amerika') }}',
            color: '#0284C7',
            sla: '{{ __('Batas Keterlambatan SLA:') }} 18 {{ __('jam') }}',
            exception: '{{ __('Fokus Kendala Utama:') }} WEATHER_DELAY',
            segments: [
                {
                    from: 'Shanghai Yangshan (CNSHA)',
                    to: 'Port of Yokohama (JPYOK)',
                    km: 1721,
                    nm: 929,
                    transit: '3 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [33.2, 131.5],
                    coords: [
                        [30.6167, 122.0667],
                        [32.0000, 128.5000],
                        [33.5000, 135.5000],
                        [35.4437, 139.6380]
                    ]
                },
                {
                    from: 'Port of Yokohama (JPYOK)',
                    to: 'Port of Los Angeles (USLAX) [Pasifik Barat]',
                    km: 8853,
                    nm: 4780,
                    transit: '12-14 {{ __('Hari Transit Pelayaran') }} (Trans-Pasifik)',
                    mid: [41.0, 165.0],
                    coords: [
                        [35.4437, 139.6380],
                        [39.5000, 155.0000],
                        [42.5000, 170.0000],
                        [43.5000, 179.9900]
                    ]
                },
                {
                    from: 'Port of Yokohama (JPYOK)',
                    to: 'Port of Los Angeles (USLAX) [Pasifik Timur]',
                    km: 8853,
                    nm: 4780,
                    transit: '12-14 {{ __('Hari Transit Pelayaran') }} (Trans-Pasifik)',
                    mid: [39.0, -145.0],
                    coords: [
                        [43.5000, -179.9900],
                        [42.0000, -165.0000],
                        [38.5000, -145.0000],
                        [36.0000, -130.0000],
                        [33.7432, -118.2673]
                    ]
                }
            ]
        },

        // ================= KORIDOR 3: SEA -> AUSTRALIA =================
        {
            id: 'sea_australia',
            type: 'sea',
            name: '{{ __('Koridor SEA → Australia') }}',
            color: '#10B981',
            sla: '{{ __('Batas Keterlambatan SLA:') }} 24 {{ __('jam') }}',
            exception: '{{ __('Fokus Kendala Utama:') }} DOCUMENTATION_ISSUE',
            segments: [
                {
                    from: 'Tanjung Priok (IDTPP)',
                    to: 'Port of Melbourne (AUMEL)',
                    km: 5620,
                    nm: 3034,
                    transit: '8-10 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [-24.0, 117.0],
                    coords: [
                        [-6.1018, 106.8833],
                        [-8.8000, 115.5000],
                        [-18.0000, 118.0000],
                        [-32.0000, 115.0000],
                        [-36.5000, 137.0000],
                        [-37.8409, 144.9125]
                    ]
                },
                {
                    from: 'Port of Melbourne (AUMEL)',
                    to: 'Port of Sydney (AUSYD)',
                    km: 712,
                    nm: 384,
                    transit: '1.5 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [-38.2, 148.5],
                    coords: [
                        [-37.8409, 144.9125],
                        [-39.0000, 147.0000],
                        [-37.5000, 150.2000],
                        [-33.9722, 151.2167]
                    ]
                },
                {
                    from: 'Port of Singapore (SGSIN)',
                    to: 'Port of Sydney (AUSYD) [Rute Timur]',
                    km: 6305,
                    nm: 3404,
                    transit: '10 {{ __('Hari Berlayar (Kecepatan 18-20 knot)') }}',
                    mid: [-16.0, 133.0],
                    coords: [
                        [1.2644, 103.8220],
                        [-5.0000, 119.0000],
                        [-10.5000, 128.0000],
                        [-22.0000, 153.0000],
                        [-33.9722, 151.2167]
                    ]
                }
            ]
        },

        // ================= KORIDOR 4: JALUR UDARA / FEEDER =================
        {
            id: 'air_feeder',
            type: 'air',
            name: '{{ __('Jalur Udara / Feeder') }}',
            color: '#8B5CF6',
            sla: '{{ __('Batas Keterlambatan SLA:') }} 6 {{ __('jam') }}',
            exception: '{{ __('Fokus Kendala Utama:') }} AIR_CUSTOMS_SCAN',
            segments: [
                {
                    from: 'Soekarno-Hatta Cargo (IDCGK)',
                    to: 'Changi Airfreight (SINAC)',
                    km: 884,
                    nm: 477,
                    transit: '1j 45m {{ __('Jam Terbang (Airfreight Express)') }}',
                    mid: [-2.5, 105.2],
                    coords: [
                        [-6.1275, 106.6537],
                        [-2.5000, 105.2000],
                        [1.3644, 103.9915]
                    ]
                }
            ]
        }
    ];

    // 3. Render Route Polylines and Distance Badges
    globalCorridors.forEach(corridor => {
        const corridorPoints = [];
        const targetLayer = corridor.type === 'air' ? airLanesLayer : seaLanesLayer;

        corridor.segments.forEach(seg => {
            seg.coords.forEach(pt => corridorPoints.push(pt));

            // Create Polyline for the segment
            const polyline = window.L.polyline(seg.coords, {
                color: corridor.color,
                weight: corridor.type === 'air' ? 3.0 : 3.5,
                opacity: 0.85,
                dashArray: corridor.type === 'air' ? '5, 6' : '10, 6',
                lineCap: 'round',
                lineJoin: 'round'
            });

            // Hover effects on lines
            polyline.on('mouseover', function () {
                this.setStyle({ weight: 5.5, opacity: 1.0 });
            });
            polyline.on('mouseout', function () {
                this.setStyle({ weight: corridor.type === 'air' ? 3.0 : 3.5, opacity: 0.85 });
            });

            // Popup on line click
            const popupHtml = `
                <div style="font-family:sans-serif;font-size:12px;min-width:220px;line-height:1.45;">
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${corridor.color};"></span>
                        <strong style="color:#2C2724;font-size:13px;">${esc(corridor.name)}</strong>
                    </div>
                    <div style="color:#665E57;font-size:11px;font-weight:600;margin-bottom:6px;">
                        ${esc(seg.from)} &rarr; ${esc(seg.to)}
                    </div>
                    <div style="background:#F6F4EF;padding:6px 8px;border-radius:8px;font-size:11px;space-y:2px;">
                        <div><strong style="color:#2C2724;">{{ __('Total Jarak:') }}</strong> <span style="font-family:monospace;font-weight:bold;color:${corridor.color};">${seg.km.toLocaleString()} km</span> <span style="color:#879880;">(${seg.nm.toLocaleString()} NM)</span></div>
                        <div><strong style="color:#2C2724;">{{ __('Estimasi Waktu Tempuh:') }}</strong> ${esc(seg.transit)}</div>
                        <div><strong style="color:#2C2724;">${esc(corridor.sla)}</strong></div>
                        <div style="font-size:10px;color:#879880;margin-top:2px;">${esc(corridor.exception)}</div>
                    </div>
                </div>
            `;
            polyline.bindPopup(popupHtml);
            targetLayer.addLayer(polyline);

            // Floating Distance Badge along the midpoint of the route
            if (seg.mid && seg.mid.length === 2) {
                const badgeIcon = window.L.divIcon({
                    className: 'route-distance-badge',
                    html: `
                        <div class="route-distance-pill" style="border-color:${corridor.color};color:#2C2724;" title="${esc(seg.from)} &rarr; ${esc(seg.to)}">
                            <span style="display:inline-block;width:6px;height:6px;border-radius:9999px;background:${corridor.color};"></span>
                            <span>${seg.km.toLocaleString()} km</span>
                            <span style="color:#879880;font-size:9px;">(${seg.nm.toLocaleString()} NM)</span>
                        </div>
                    `,
                    iconSize: [110, 24],
                    iconAnchor: [55, 12],
                });

                const badgeMarker = window.L.marker(seg.mid, { icon: badgeIcon });
                badgeMarker.bindPopup(popupHtml);
                distanceMarkersLayer.addLayer(badgeMarker);
            }
        });

        // Store bounds for corridor focus
        if (corridorPoints.length > 0) {
            corridorBounds[corridor.id] = window.L.latLngBounds(corridorPoints);
        }
    });

    // Fit map bounds to encompass all hubs comfortably
    if (allPinsBounds.length > 1) {
        worldMap.fitBounds(allPinsBounds, { padding: [40, 40] });
    }
}

// Interactive Toggle Functions
function setMapBasemap(mode) {
    if (!worldMap || !googleHybridLayer || !osmVectorLayer) return;
    const btnSat = document.getElementById('btnSatelliteMode');
    const btnStr = document.getElementById('btnStreetMode');
    if (mode === 'satellite') {
        worldMap.removeLayer(osmVectorLayer);
        googleHybridLayer.addTo(worldMap);
        googleHybridLayer.bringToBack();
        if (btnSat) btnSat.className = 'px-3.5 py-1 rounded-full bg-emerald-600 text-white shadow-xs transition-all flex items-center gap-1.5 cursor-pointer';
        if (btnStr) btnStr.className = 'px-3.5 py-1 rounded-full text-espresso-700 hover:text-espresso-900 transition-all flex items-center gap-1.5 cursor-pointer';
    } else {
        worldMap.removeLayer(googleHybridLayer);
        osmVectorLayer.addTo(worldMap);
        osmVectorLayer.bringToBack();
        if (btnSat) btnSat.className = 'px-3.5 py-1 rounded-full text-espresso-700 hover:text-espresso-900 transition-all flex items-center gap-1.5 cursor-pointer';
        if (btnStr) btnStr.className = 'px-3.5 py-1 rounded-full bg-sage-600 text-white shadow-xs transition-all flex items-center gap-1.5 cursor-pointer';
    }
}

function toggleRouteDistances() {
    if (!worldMap || !distanceMarkersLayer) return;
    isDistancesVisible = !isDistancesVisible;
    const btnText = document.getElementById('toggleDistancesText');

    if (isDistancesVisible) {
        worldMap.addLayer(distanceMarkersLayer);
        if (btnText) btnText.textContent = '{{ __('Label Jarak: AKTIF') }}';
    } else {
        worldMap.removeLayer(distanceMarkersLayer);
        if (btnText) btnText.textContent = '{{ __('Label Jarak: MATI') }}';
    }
}

function toggleSeaLanes() {
    if (!worldMap || !seaLanesLayer) return;
    isSeaVisible = !isSeaVisible;
    const btn = document.getElementById('toggleSeaBtn');

    if (isSeaVisible) {
        worldMap.addLayer(seaLanesLayer);
        if (btn) btn.className = 'px-3.5 py-1.5 rounded-full bg-sage-500 text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5';
    } else {
        worldMap.removeLayer(seaLanesLayer);
        if (btn) btn.className = 'px-3.5 py-1.5 rounded-full bg-cream-200 text-espresso-600 text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5';
    }
}

function toggleAirLanes() {
    if (!worldMap || !airLanesLayer) return;
    isAirVisible = !isAirVisible;
    const btn = document.getElementById('toggleAirBtn');

    if (isAirVisible) {
        worldMap.addLayer(airLanesLayer);
        if (btn) btn.className = 'px-3.5 py-1.5 rounded-full bg-purple-500 text-white text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5';
    } else {
        worldMap.removeLayer(airLanesLayer);
        if (btn) btn.className = 'px-3.5 py-1.5 rounded-full bg-cream-200 text-espresso-600 text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5';
    }
}

function focusCorridor(corridorId) {
    if (!worldMap || !corridorBounds[corridorId]) return;
    worldMap.flyToBounds(corridorBounds[corridorId], {
        padding: [50, 50],
        duration: 1.2
    });
}

// Nominatim Geocoding
async function runGeocodeSearch() {
    const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));
    const q = document.getElementById('geocodeQuery').value.trim();
    const statusEl = document.getElementById('geocodeStatus');
    if (!q || q.length < 2) {
        statusEl.textContent = @json(__('Ketik minimal 2 karakter nama kota atau pelabuhan.'));
        return;
    }

    statusEl.textContent = @json(__('Mencari koordinat melalui Nominatim OpenStreetMap...'));

    try {
        const res = await fetch('/integrations/geocode?q=' + encodeURIComponent(q));
        const json = await res.json();
        const payload = json.data || {};

        if (res.ok && payload.available && payload.results && payload.results.length > 0) {
            const first = payload.results[0];
            document.getElementById('locLatInput').value = first.latitude;
            document.getElementById('locLngInput').value = first.longitude;
            if (first.city && !document.getElementById('locCityInput').value) {
                document.getElementById('locCityInput').value = first.city;
            }
            if (first.country && !document.getElementById('locCountryInput').value) {
                document.getElementById('locCountryInput').value = first.country;
            }
            if (!document.getElementById('locNameInput').value) {
                document.getElementById('locNameInput').value = first.display_name.split(',')[0];
            }
            statusEl.innerHTML = `<span class="text-sage-700 font-bold">&#10003; ${@json(__('Ditemukan'))}: ${esc(first.display_name)} (${esc(first.latitude)}, ${esc(first.longitude)})</span>`;
        } else {
            statusEl.innerHTML = `<span class="text-rose-600 font-semibold">${esc(payload.message) || @json(__('Lokasi tidak ditemukan atau layanan eksternal sedang sibuk.'))}</span>`;
        }
    } catch (e) {
        statusEl.innerHTML = `<span class="text-rose-600 font-semibold">${@json(__('Gagal menghubungi layanan geocoding eksternal.'))}</span>`;
    }
}
</script>
@endsection
