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

    <!-- Global Interactive Hubs Map -->
    <div class="p-5 sm:p-6 rounded-3xl glass-card-strong space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-extrabold text-espresso-900">{{ __('Peta Jaringan Pelabuhan & Hub Dunia') }}</h2>
                <p class="text-[11px] text-espresso-500">{{ __('Klik marker untuk melihat koordinat GPS dan kode pelabuhan UN/LOCODE.') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-[11px] font-bold">OpenStreetMap</span>
        </div>
        <div id="locationsWorldMap" class="w-full h-64 sm:h-80 rounded-2xl border border-cream-200 bg-cream-100 z-10 overflow-hidden"></div>
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
    $mapHubs = $locations->getCollection()->map(fn ($l) => [
        'name' => $l->name,
        'code' => $l->code,
        'type' => $l->type,
        'city' => $l->city,
        'country' => $l->country,
        'lat' => $l->latitude,
        'lng' => $l->longitude,
    ])->values()->all();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.getElementById('locationsWorldMap');
    if (!mapEl || typeof window.L === 'undefined') return;

    const hubs = {{ Illuminate\Support\Js::from($mapHubs) }};

    const map = window.L.map('locationsWorldMap', { scrollWheelZoom: false }).setView([15.0, 105.0], 2);

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const bounds = [];
    const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));
    hubs.forEach(hub => {
        if (hub.lat && hub.lng) {
            const pt = [parseFloat(hub.lat), parseFloat(hub.lng)];
            bounds.push(pt);
            const icon = window.L.divIcon({
                className: 'hub-pin',
                html: `<div style="background:#879880;border:2px solid #FFFFFF;width:18px;height:18px;border-radius:9999px;box-shadow:0 3px 8px rgba(44,39,36,0.25);"></div>`,
                iconSize: [18, 18],
                iconAnchor: [9, 9],
            });
            window.L.marker(pt, { icon })
                .addTo(map)
                .bindPopup(`<div style="font-family:sans-serif;font-size:12px;"><strong>${esc(hub.name)} (${esc(hub.code)})</strong><br/>${@json(__('Tipe'))}: ${esc(hub.type)}<br/>${esc(hub.city)}, ${esc(hub.country)}<br/><code>${esc(hub.lat)}, ${esc(hub.lng)}</code></div>`);
        }
    });

    if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
});

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
