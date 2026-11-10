@extends('layouts.dashboard')

@section('title', 'Operations Dashboard')
@section('page_title', 'Cargo Operations Center')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header Banner (Sage Organic Style) -->
    <div class="relative overflow-hidden rounded-3xl bg-sage-500 text-white p-6 sm:p-8 shadow-[0_18px_45px_-15px_rgba(135,152,128,0.45)]">
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-16 w-40 h-40 rounded-full bg-white/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/20 backdrop-blur-xs text-white">
                        Role: {{ ucfirst(Auth::user()->role) }}
                    </span>
                    <span class="text-xs text-sage-100 font-medium">Authenticated via Sanctum/Session</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-sage-50">
                    CargoTrace Visibility Portal &mdash; {{ Auth::user()->company_name ?: 'Global Supply Chain Network' }}
                </p>
            </div>

            <!-- Quick Track Input Widget -->
            <div id="quick-track" class="w-full md:w-auto">
                <form id="trackForm" onsubmit="event.preventDefault(); quickTrack();" class="flex items-center gap-2 bg-white/15 backdrop-blur-sm p-1.5 rounded-2xl border border-white/20">
                    <input id="trackInput" type="text" placeholder="CT-YYYYMMDD-XXXX"
                           class="w-full sm:w-60 px-3.5 py-2.5 bg-white rounded-xl text-xs font-mono placeholder-espresso-400 text-espresso-900 focus:outline-none focus:ring-2 focus:ring-apricot-400 transition-all">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/30 transition-all whitespace-nowrap cursor-pointer">
                        Track Cargo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Operational Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-[0_10px_28px_-14px_rgba(44,39,36,0.08)]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Active Shipments</p>
                <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-espresso-900">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-sage-600 mt-1">Lifecycle in progress</p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-[0_10px_28px_-14px_rgba(44,39,36,0.08)]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">In Transit Hubs</p>
                <div class="w-9 h-9 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-apricot-600">{{ $stats['in_transit'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-espresso-500 mt-1">Between port/airport nodes</p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-[0_10px_28px_-14px_rgba(44,39,36,0.08)]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Delays Flagged</p>
                <div class="w-9 h-9 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-rose-600">{{ $stats['delayed'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-espresso-500 mt-1">Deterministic ETA rules</p>
        </div>

        <div class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-[0_10px_28px_-14px_rgba(44,39,36,0.08)]">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-espresso-500 uppercase tracking-wider">Delivered (30d)</p>
                <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-sage-700">{{ $stats['delivered'] ?? 0 }}</p>
            <p class="text-xs font-semibold text-sage-600 mt-1">Terminal signed state</p>
        </div>
    </div>

    <!-- Quick Tracking Modal / Result Drawer -->
    <div id="trackResultModal" class="hidden rounded-3xl border border-cream-200 bg-white p-6 shadow-[0_20px_45px_-15px_rgba(44,39,36,0.12)] space-y-4">
        <div class="flex items-center justify-between border-b border-cream-200 pb-3">
            <h3 class="text-sm font-extrabold text-espresso-900 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sage-500 animate-ping"></span>
                <span>Live Consignment Tracking Result</span>
            </h3>
            <button onclick="document.getElementById('trackResultModal').classList.add('hidden')" class="px-3 py-1 rounded-full bg-cream-100 hover:bg-cream-200 text-xs font-bold text-espresso-600 cursor-pointer">&times; Close</button>
        </div>
        <div id="trackResultBody" class="text-xs text-espresso-800 font-mono"></div>
    </div>

    <!-- SHIPMENTS MANIFEST TABLE SECTION -->
    <div id="shipments-section" class="rounded-3xl bg-white border border-cream-200/80 overflow-hidden shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)]">
        <div class="p-6 border-b border-cream-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-extrabold text-espresso-900">Global Consignments Manifest</h2>
                <p class="text-xs text-espresso-500 mt-0.5">
                    @if(Auth::user()->isCustomer())
                        Showing consignments linked directly to your customer account.
                    @else
                        Master operational console across all customer shipments.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="px-3.5 py-1.5 rounded-full bg-cream-100 text-xs font-bold text-espresso-700">Total: <strong class="text-espresso-900">{{ $shipments->total() }}</strong></span>
                <a href="{{ route('shipments.index') }}" class="px-4 py-1.5 rounded-full bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold transition-colors">
                    Open Full Manifest &rarr;
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Tracking Number</th>
                        <th scope="col" class="px-5 py-3.5">Customer</th>
                        <th scope="col" class="px-5 py-3.5">Carrier</th>
                        <th scope="col" class="px-5 py-3.5">Origin &rarr; Destination</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Est. Delivery</th>
                        <th scope="col" class="px-5 py-3.5">Items</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Action</th>
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
                                    {{ $shipment->carrier?->name ?: 'Unassigned' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-espresso-900 font-semibold">{{ $shipment->origin?->name }} &rarr; {{ $shipment->destination?->name }}</div>
                                <div class="text-[11px] text-espresso-500">
                                    @if($shipment->currentLocation)
                                        Current: <span class="text-sage-700 font-semibold">{{ $shipment->currentLocation->name }}</span>
                                    @else
                                        Origin dispatch pending
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
                                {{ $shipment->estimated_delivery_at?->format('M d, Y H:i') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full bg-cream-100 text-espresso-700 font-semibold text-[11px]">
                                    {{ $shipment->items->count() }} item(s)
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('shipments.show', $shipment->tracking_number) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-sm shadow-apricot-500/20 transition-all">
                                    <span>Inspect</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-espresso-500">
                                No shipments found in the system.
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
        <div id="carriers-section" class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-apricot-100 text-apricot-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Active Ocean &amp; Air Carriers</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-cream-100 text-[11px] font-mono font-bold text-espresso-600">{{ $carriersCount ?? 5 }} registered</span>
                    @if(Auth::user()->can('viewAny', \App\Models\Carrier::class))
                        <a href="{{ route('carriers.index') }}" class="px-3 py-1 rounded-full bg-apricot-100 hover:bg-apricot-500 hover:text-white text-apricot-600 text-[11px] font-bold transition-colors">Manage &rarr;</a>
                    @endif
                </div>
            </div>
            <p class="text-xs text-espresso-500">Integrated global lines with standardized prefix codes &amp; automated tracking URL templates.</p>
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
        <div id="locations-section" class="p-6 rounded-3xl bg-white border border-cream-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-2xl bg-sage-100 text-sage-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-extrabold text-espresso-900">Global Logistics Ports &amp; Hubs</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-sage-100 text-[11px] font-mono font-bold text-sage-700">{{ $locationsCount ?? 8 }} hubs</span>
                    <a href="{{ route('locations.index') }}" class="px-3 py-1 rounded-full bg-sage-100 hover:bg-sage-500 hover:text-white text-sage-700 text-[11px] font-bold transition-colors">Explore &rarr;</a>
                </div>
            </div>
            <p class="text-xs text-espresso-500">UN/LOCODE standard hubs with latitude/longitude ready for Leaflet.js interactive maps.</p>
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

<script>
function quickInspect(trackingNo) {
    document.getElementById('trackInput').value = trackingNo;
    quickTrack();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function quickTrack() {
    const input = document.getElementById('trackInput').value.trim();
    if (!input) return;

    const modal = document.getElementById('trackResultModal');
    const body = document.getElementById('trackResultBody');
    modal.classList.remove('hidden');
    body.innerHTML = '<span class="text-espresso-500">Querying real-time REST API endpoint (/api/v1/tracking/' + input + ')...</span>';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
        const data = await res.json();

        if (res.ok && data.success) {
            const s = data.data;
            body.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs font-sans">
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Tracking Number</span>
                        <strong class="text-sage-700 font-mono text-sm">${s.tracking_number}</strong>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Status</span>
                        <span class="text-apricot-600 font-extrabold">${s.status}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Carrier</span>
                        <span class="text-espresso-900 font-bold">${s.carrier ? s.carrier.name : 'N/A'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Origin Hub</span>
                        <span class="text-espresso-900 font-bold">${s.origin ? s.origin.name : 'N/A'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Destination Port</span>
                        <span class="text-espresso-900 font-bold">${s.destination ? s.destination.name : 'N/A'}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                        <span class="text-espresso-500 block font-semibold">Estimated Arrival</span>
                        <span class="text-espresso-800 font-semibold">${s.estimated_delivery_at ? new Date(s.estimated_delivery_at).toLocaleString() : 'Pending'}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-cream-200 flex justify-end">
                    <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md transition-all">
                        <span>Open Timeline Ledger</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            `;
        } else {
            body.innerHTML = `<span class="text-rose-600 font-sans font-semibold">${data.message || 'Shipment not found for tracking number.'}</span>`;
        }
    } catch (e) {
        body.innerHTML = `<span class="text-rose-600 font-sans font-semibold">Network error fetching tracking details.</span>`;
    }
}
</script>
@endsection
