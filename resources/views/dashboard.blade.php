@extends('layouts.dashboard')

@section('title', 'Operations Dashboard')
@section('page_title', 'Cargo Operations Center')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900/60 via-slate-900 to-slate-950 border border-indigo-500/20 p-6 sm:p-8">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                        @if(Auth::user()->isAdmin()) bg-rose-500/20 text-rose-300 border border-rose-500/30
                        @elseif(Auth::user()->isOperator()) bg-amber-500/20 text-amber-300 border border-amber-500/30
                        @else bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                        @endif">
                        Role: {{ ucfirst(Auth::user()->role) }}
                    </span>
                    <span class="text-xs text-slate-400">Authenticated via Sanctum/Session</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-slate-300">
                    CargoTrace Visibility Portal &mdash; {{ Auth::user()->company_name ?: 'Global Supply Chain Network' }}
                </p>
            </div>

            <!-- Quick Track Input Widget -->
            <div id="quick-track" class="w-full md:w-auto">
                <form id="trackForm" onsubmit="event.preventDefault(); quickTrack();" class="flex items-center gap-2">
                    <input id="trackInput" type="text" placeholder="CT-YYYYMMDD-XXXX"
                           class="w-full sm:w-64 px-3.5 py-2.5 bg-slate-900/90 border border-slate-700 rounded-xl text-xs font-mono placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition-all whitespace-nowrap">
                        Track Cargo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Operational Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Shipments</p>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-black text-white">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs text-indigo-400 mt-1">Lifecycle in progress</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">In Transit Hubs</p>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-black text-sky-400">{{ $stats['in_transit'] ?? 0 }}</p>
            <p class="text-xs text-sky-400/80 mt-1">Between port/airport nodes</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Delays Flagged</p>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-black text-amber-400">{{ $stats['delayed'] ?? 0 }}</p>
            <p class="text-xs text-amber-400/80 mt-1">Deterministic ETA rules</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-lg">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Delivered (30d)</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-2 text-3xl font-black text-emerald-400">{{ $stats['delivered'] ?? 0 }}</p>
            <p class="text-xs text-emerald-400/80 mt-1">Terminal signed state</p>
        </div>
    </div>

    <!-- Quick Tracking Modal / Result Drawer -->
    <div id="trackResultModal" class="hidden rounded-2xl border border-indigo-500/30 bg-slate-900 p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Live Consignment Tracking Result</span>
            </h3>
            <button onclick="document.getElementById('trackResultModal').classList.add('hidden')" class="text-xs text-slate-400 hover:text-white">&times; Close</button>
        </div>
        <div id="trackResultBody" class="text-xs text-slate-300 font-mono"></div>
    </div>

    <!-- SHIPMENTS MANIFEST TABLE SECTION -->
    <div id="shipments-section" class="rounded-2xl bg-slate-900/60 border border-slate-800/80 overflow-hidden shadow-2xl">
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-white">Global Consignments Manifest</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if(Auth::user()->isCustomer())
                        Showing consignments linked directly to your customer account.
                    @else
                        Master operational console across all customer shipments.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400">Total Consignments: <strong class="text-white">{{ $shipments->total() }}</strong></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 font-semibold uppercase tracking-wider">
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
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-slate-900/80 transition-colors">
                            <td class="px-5 py-4 font-mono font-medium text-indigo-400 whitespace-nowrap">
                                {{ $shipment->tracking_number }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $shipment->customer?->name ?: 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $shipment->customer?->company_name }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-950 text-slate-300 font-medium border border-slate-800">
                                    {{ $shipment->carrier?->name ?: 'Unassigned' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-white font-medium">{{ $shipment->origin?->name }} &rarr; {{ $shipment->destination?->name }}</div>
                                <div class="text-[11px] text-slate-500">
                                    @if($shipment->currentLocation)
                                        Current: <span class="text-sky-400">{{ $shipment->currentLocation->name }}</span>
                                    @else
                                        Origin dispatch pending
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold tracking-wider uppercase
                                    @if($shipment->status === 'DELIVERED') bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                                    @elseif($shipment->status === 'IN_TRANSIT') bg-sky-500/10 text-sky-400 border border-sky-500/30
                                    @elseif($shipment->status === 'BOOKED') bg-indigo-500/10 text-indigo-400 border border-indigo-500/30
                                    @elseif($shipment->status === 'EXCEPTION') bg-rose-500/10 text-rose-400 border border-rose-500/30
                                    @else bg-slate-700/30 text-slate-300 border border-slate-700
                                    @endif">
                                    {{ $shipment->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-400">
                                {{ $shipment->estimated_delivery_at?->format('M d, Y H:i') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-950 text-slate-300 border border-slate-800 text-[11px]">
                                    {{ $shipment->items->count() }} item(s)
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('shipments.show', $shipment->tracking_number) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-600/20 text-indigo-400 hover:bg-indigo-600 hover:text-white border border-indigo-500/30 transition-all">
                                    <span>Inspect</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-500">
                                No shipments found in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $shipments->links() }}
            </div>
        @endif
    </div>

    <!-- CARRIERS & MASTER NODES PREVIEW (For P9 Completeness) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Carriers Card -->
        <div id="carriers-section" class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Active Ocean & Air Carriers</h3>
                </div>
                <span class="text-xs font-mono text-slate-400">{{ $carriersCount ?? 5 }} registered</span>
            </div>
            <p class="text-xs text-slate-400">Integrated global lines with standardized prefix codes & automated tracking URL templates.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">A.P. Moller - Maersk</span>
                    <span class="font-mono text-indigo-400 text-[10px]">MAEU</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">MSC Mediterranean</span>
                    <span class="font-mono text-indigo-400 text-[10px]">MSCU</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">CMA CGM Group</span>
                    <span class="font-mono text-indigo-400 text-[10px]">CMDU</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">DHL Global Forwarding</span>
                    <span class="font-mono text-indigo-400 text-[10px]">DHLG</span>
                </div>
            </div>
        </div>

        <!-- Locations Card -->
        <div id="locations-section" class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Global Logistics Ports & Hubs</h3>
                </div>
                <span class="text-xs font-mono text-slate-400">{{ $locationsCount ?? 8 }} hubs</span>
            </div>
            <p class="text-xs text-slate-400">UN/LOCODE standard hubs with latitude/longitude ready for Leaflet.js interactive maps.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">Port of Tanjung Priok</span>
                    <span class="font-mono text-emerald-400 text-[10px]">IDTPP</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">Port of Singapore</span>
                    <span class="font-mono text-emerald-400 text-[10px]">SGSIN</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">Port of Rotterdam</span>
                    <span class="font-mono text-emerald-400 text-[10px]">NLRTM</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 flex items-center justify-between">
                    <span class="font-semibold text-slate-200">Soekarno-Hatta Cargo</span>
                    <span class="font-mono text-emerald-400 text-[10px]">IDCGK</span>
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
    body.innerHTML = '<span class="text-slate-400">Querying real-time REST API endpoint (/api/v1/tracking/' + input + ')...</span>';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
        const data = await res.json();

        if (res.ok && data.success) {
            const s = data.data;
            body.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Tracking Number</span>
                        <strong class="text-indigo-400 text-sm">${s.tracking_number}</strong>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Status</span>
                        <span class="text-emerald-400 font-bold">${s.status}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Carrier</span>
                        <span class="text-white">${s.carrier ? s.carrier.name : 'N/A'}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Origin Hub</span>
                        <span class="text-white">${s.origin ? s.origin.name : 'N/A'}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Destination Port</span>
                        <span class="text-white">${s.destination ? s.destination.name : 'N/A'}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-950/80 border border-slate-800">
                        <span class="text-slate-500 block">Estimated Arrival</span>
                        <span class="text-slate-300">${s.estimated_delivery_at ? new Date(s.estimated_delivery_at).toLocaleString() : 'Pending'}</span>
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-end">
                    <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-md transition-all">
                        <span>Open Timeline Ledger</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            `;
        } else {
            body.innerHTML = `<span class="text-rose-400">${data.message || 'Shipment not found for tracking number.'}</span>`;
        }
    } catch (e) {
        body.innerHTML = `<span class="text-rose-400">Network error fetching tracking details.</span>`;
    }
}
</script>
@endsection
