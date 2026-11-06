@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
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
                <h1 class="text-2xl sm:text-3xl font-black text-white">Welcome, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-slate-300">
                    CargoTrace Visibility Portal &mdash; {{ Auth::user()->company_name ?: 'Global Supply Chain Network' }}
                </p>
            </div>

            <!-- Quick Track Input Widget -->
            <div class="w-full md:w-auto">
                <form id="trackForm" onsubmit="event.preventDefault(); quickTrack();" class="flex items-center gap-2">
                    <input id="trackInput" type="text" placeholder="CT-YYYYMMDD-XXXX"
                           class="w-full sm:w-64 px-3.5 py-2 bg-slate-900/90 border border-slate-700 rounded-xl text-xs font-mono placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition-all whitespace-nowrap">
                        Track
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Operational Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Active Shipments</p>
            <p class="mt-2 text-3xl font-black text-white">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs text-indigo-400 mt-1">Lifecycle in progress</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">In Transit Hubs</p>
            <p class="mt-2 text-3xl font-black text-sky-400">{{ $stats['in_transit'] ?? 0 }}</p>
            <p class="text-xs text-sky-400/80 mt-1">Between port/airport nodes</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Delays Flagged</p>
            <p class="mt-2 text-3xl font-black text-amber-400">{{ $stats['delayed'] ?? 0 }}</p>
            <p class="text-xs text-amber-400/80 mt-1">Deterministic ETA threshold</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Completed / Delivered</p>
            <p class="mt-2 text-3xl font-black text-emerald-400">{{ $stats['delivered'] ?? 0 }}</p>
            <p class="text-xs text-emerald-400/80 mt-1">Terminal signed state</p>
        </div>
    </div>

    <!-- Quick Tracking Modal / Result Alert -->
    <div id="trackResultModal" class="hidden rounded-2xl border border-indigo-500/30 bg-slate-950 p-5 shadow-2xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Shipment Tracking Result
            </h3>
            <button onclick="document.getElementById('trackResultModal').classList.add('hidden')" class="text-xs text-slate-400 hover:text-white">✕ Close</button>
        </div>
        <div id="trackResultBody" class="text-xs text-slate-300 font-mono"></div>
    </div>

    <!-- Core Shipments Table -->
    <div class="rounded-2xl bg-slate-950/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-slate-800/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-white">Global Visibility Manifest</h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    @if(Auth::user()->isCustomer())
                        Showing consignments linked to your customer profile.
                    @else
                        Master operational view across all active customer consignments.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Total: <strong class="text-white">{{ $shipments->total() }}</strong></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 text-left text-xs">
                <thead class="bg-slate-900/60 text-slate-400 font-semibold uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Tracking Number</th>
                        <th scope="col" class="px-5 py-3.5">Customer</th>
                        <th scope="col" class="px-5 py-3.5">Carrier</th>
                        <th scope="col" class="px-5 py-3.5">Origin &rarr; Destination</th>
                        <th scope="col" class="px-5 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5">Est. Delivery</th>
                        <th scope="col" class="px-5 py-3.5">Items</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($shipments as $shipment)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="px-5 py-4 font-mono font-medium text-indigo-400 whitespace-nowrap">
                                {{ $shipment->tracking_number }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-white">{{ $shipment->customer?->name ?: 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $shipment->customer?->company_name }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 font-medium border border-slate-800">
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
                                <span class="px-2 py-0.5 rounded bg-slate-900 text-slate-300 border border-slate-800 text-[11px]">
                                    {{ $shipment->items->count() }} item(s)
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-500">
                                No shipments found matching criteria.
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
</div>

<script>
async function quickTrack() {
    const input = document.getElementById('trackInput').value.trim();
    if (!input) return;

    const modal = document.getElementById('trackResultModal');
    const body = document.getElementById('trackResultBody');
    modal.classList.remove('hidden');
    body.innerHTML = '<span class="text-slate-400">Querying live API endpoint (/api/v1/tracking/' + input + ')...</span>';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
        const data = await res.json();

        if (res.ok && data.success) {
            const s = data.data;
            body.innerHTML = `
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div><span class="text-slate-500">Tracking Number:</span> <strong class="text-white">${s.tracking_number}</strong></div>
                    <div><span class="text-slate-500">Status:</span> <span class="text-emerald-400 font-bold">${s.status}</span></div>
                    <div><span class="text-slate-500">Carrier:</span> ${s.carrier ? s.carrier.name : 'N/A'}</div>
                    <div><span class="text-slate-500">Route:</span> ${s.origin ? s.origin.name : ''} &rarr; ${s.destination ? s.destination.name : ''}</div>
                    <div><span class="text-slate-500">Est. Delivery:</span> ${s.estimated_delivery_at || 'Pending'}</div>
                    <div><span class="text-slate-500">Cargo Items:</span> ${s.items ? s.items.length : 0} item(s)</div>
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
