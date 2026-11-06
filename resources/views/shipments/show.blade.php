@extends('layouts.dashboard')

@section('title', 'Shipment ' . $shipment->tracking_number . ' - CargoTrace')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- TOP BAR / BREADCRUMB -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-800">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Consignment Tracking</span>
                    <span class="text-xs text-slate-600">&bull;</span>
                    <span class="text-xs font-mono text-indigo-400">{{ $shipment->carrier->name }} ({{ $shipment->carrier->code }})</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-mono tracking-tight flex items-center gap-3">
                    {{ $shipment->tracking_number }}
                    <button onclick="navigator.clipboard.writeText('{{ $shipment->tracking_number }}'); alert('Tracking number copied to clipboard');" title="Copy tracking number" class="text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Lifecycle Badge -->
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase
                @if($shipment->status === 'DELIVERED') bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                @elseif($shipment->status === 'IN_TRANSIT') bg-sky-500/10 text-sky-400 border border-sky-500/30
                @elseif($shipment->status === 'OUT_FOR_DELIVERY') bg-cyan-500/10 text-cyan-400 border border-cyan-500/30
                @elseif($shipment->status === 'BOOKED') bg-indigo-500/10 text-indigo-400 border border-indigo-500/30
                @elseif($shipment->status === 'PICKED_UP') bg-amber-500/10 text-amber-400 border border-amber-500/30
                @elseif($shipment->status === 'EXCEPTION') bg-rose-500/10 text-rose-400 border border-rose-500/30
                @elseif($shipment->status === 'CANCELLED') bg-red-500/10 text-red-400 border border-red-500/30
                @else bg-slate-800 text-slate-300 border border-slate-700
                @endif">
                <span class="w-1.5 h-1.5 rounded-full mr-2
                    @if($shipment->status === 'DELIVERED') bg-emerald-400
                    @elseif($shipment->status === 'IN_TRANSIT') bg-sky-400 animate-pulse
                    @elseif($shipment->status === 'EXCEPTION') bg-rose-400 animate-ping
                    @else bg-indigo-400
                    @endif"></span>
                {{ $shipment->status }}
            </span>

            @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()) && !$shipment->isTerminal())
                <button onclick="document.getElementById('eventModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Log Checkpoint</span>
                </button>
            @endif
        </div>
    </div>

    <!-- FLASH NOTIFICATIONS -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm space-y-1">
            <div class="font-bold">Please correct the following errors:</div>
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
        <div class="md:col-span-2 p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transit Corridor</span>
                <span class="text-xs font-mono text-slate-500">ID: #{{ $shipment->id }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 items-center gap-4 py-2">
                <!-- Origin -->
                <div class="space-y-1">
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Origin Port</div>
                    <div class="text-lg font-bold text-white">{{ $shipment->origin->name }}</div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <span class="font-mono text-indigo-400">{{ $shipment->origin->code }}</span>
                        <span>&middot;</span>
                        <span>{{ $shipment->origin->country }}</span>
                    </div>
                </div>

                <!-- Arrow & Distance / Progress -->
                <div class="flex flex-col items-center justify-center space-y-1 py-2 sm:py-0">
                    <div class="w-full flex items-center justify-center">
                        <div class="h-0.5 bg-slate-800 flex-1"></div>
                        <div class="p-2 rounded-full bg-slate-800 border border-slate-700 text-indigo-400 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                        <div class="h-0.5 bg-slate-800 flex-1"></div>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400">Direct Route</span>
                </div>

                <!-- Destination -->
                <div class="space-y-1 sm:text-right">
                    <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Final Destination</div>
                    <div class="text-lg font-bold text-white">{{ $shipment->destination->name }}</div>
                    <div class="flex items-center sm:justify-end gap-1.5 text-xs text-slate-400">
                        <span class="font-mono text-emerald-400">{{ $shipment->destination->code }}</span>
                        <span>&middot;</span>
                        <span>{{ $shipment->destination->country }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Location Badge -->
            <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></div>
                    <span class="text-slate-400">Current Position:</span>
                    <span class="font-bold text-sky-400">{{ $shipment->currentLocation ? $shipment->currentLocation->name : 'At Origin Depot' }}</span>
                </div>
                <div class="text-slate-400 font-mono text-[11px]">
                    @if($shipment->departure_at)
                        Departed: <span class="text-slate-200">{{ $shipment->departure_at->format('M d, Y H:i') }}</span>
                    @else
                        Awaiting departure
                    @endif
                </div>
            </div>
        </div>

        <!-- Schedule & Customer Meta -->
        <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Schedule & Ownership</h3>

            <div class="space-y-3 text-xs">
                <div>
                    <div class="text-slate-400">Estimated Delivery:</div>
                    <div class="text-sm font-bold text-white mt-0.5">
                        {{ $shipment->estimated_delivery_at ? $shipment->estimated_delivery_at->format('l, M d, Y &bull; H:i') : 'Pending Calculation' }}
                    </div>
                </div>

                <div>
                    <div class="text-slate-400">Actual Delivery:</div>
                    <div class="text-sm font-semibold text-slate-300 mt-0.5">
                        {{ $shipment->actual_delivery_at ? $shipment->actual_delivery_at->format('M d, Y H:i') : 'Not Delivered Yet' }}
                    </div>
                </div>

                <div>
                    <div class="text-slate-400">Delay Variance:</div>
                    <div class="text-sm font-mono mt-0.5">
                        @if($shipment->delay_hours > 0)
                            <span class="text-rose-400 font-bold">+{{ $shipment->delay_hours }} hrs (Delayed)</span>
                        @else
                            <span class="text-emerald-400 font-bold">On Schedule (0 hrs)</span>
                        @endif
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-800">
                    <div class="text-slate-400">Customer Consignee:</div>
                    <div class="text-sm font-semibold text-indigo-300">{{ $shipment->customer->name }}</div>
                    <div class="text-[11px] text-slate-400">{{ $shipment->customer->email }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN GRID: TIMELINE & CARGO ITEMS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- VERTICAL TIMELINE LEDGER (2 cols) -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h2 class="text-base font-bold text-white">Immutable Tracking Ledger</h2>
                </div>
                <span class="text-xs font-mono text-slate-400">{{ $timeline->count() }} checkpoints logged</span>
            </div>

            <!-- Vertical Timeline Track -->
            @if($timeline->isEmpty())
                <div class="py-12 text-center text-slate-500 space-y-2">
                    <svg class="w-8 h-8 mx-auto text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm">No checkpoints recorded yet for this consignment.</p>
                </div>
            @else
                <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-800">
                    @foreach($timeline as $index => $event)
                        <div class="relative group">
                            <!-- Timeline Dot / Icon -->
                            <div class="absolute -left-6 sm:-left-8 top-1 w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-slate-950 transition-all
                                @if($event->status === 'DELIVERED') bg-emerald-500 text-white shadow-lg shadow-emerald-500/30
                                @elseif($event->status === 'IN_TRANSIT') bg-sky-500 text-white shadow-lg shadow-sky-500/30
                                @elseif($event->status === 'PICKED_UP') bg-amber-500 text-slate-950
                                @elseif($event->status === 'EXCEPTION') bg-rose-500 text-white shadow-lg shadow-rose-500/30
                                @elseif($event->status === 'CANCELLED') bg-red-600 text-white
                                @else bg-indigo-600 text-white
                                @endif">
                                @if($event->status === 'DELIVERED')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                @elseif($event->status === 'EXCEPTION')
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                    <span class="text-[10px]">{{ $index + 1 }}</span>
                                @endif
                            </div>

                            <!-- Content Card -->
                            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition-colors space-y-2">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase
                                            @if($event->status === 'DELIVERED') bg-emerald-500/10 text-emerald-400 border border-emerald-500/30
                                            @elseif($event->status === 'IN_TRANSIT') bg-sky-500/10 text-sky-400 border border-sky-500/30
                                            @elseif($event->status === 'EXCEPTION') bg-rose-500/10 text-rose-400 border border-rose-500/30
                                            @else bg-indigo-500/10 text-indigo-400 border border-indigo-500/30
                                            @endif">
                                            {{ $event->status }}
                                        </span>
                                        <span class="text-sm font-bold text-white">{{ $event->location_name }}</span>
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400">
                                        {{ $event->event_time->format('M d, Y &bull; H:i:s') }} UTC
                                    </div>
                                </div>

                                <p class="text-xs text-slate-300 leading-relaxed">{{ $event->description }}</p>

                                <div class="pt-2 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500 border-t border-slate-900">
                                    <div class="flex items-center gap-3">
                                        @if($event->latitude && $event->longitude)
                                            <span class="font-mono text-slate-400 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                {{ number_format($event->latitude, 4) }}, {{ number_format($event->longitude, 4) }}
                                            </span>
                                        @endif
                                        @if($event->location)
                                            <span class="text-slate-400">Hub: {{ $event->location->code }}</span>
                                        @endif
                                    </div>

                                    @if($event->creator)
                                        <span class="text-slate-400">Logged by: <strong class="text-slate-300">{{ $event->creator->name }}</strong></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- CARGO ITEMS MANIFEST (1 col) -->
        <div class="space-y-6">
            <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Itemized Cargo Manifest</h3>
                    <span class="text-xs font-mono text-indigo-400">{{ $shipment->items->count() }} item(s)</span>
                </div>

                <div class="space-y-3">
                    @forelse($shipment->items as $item)
                        <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5 text-xs">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-bold text-white">{{ $item->description }}</span>
                                <span class="font-mono text-slate-400 text-[11px]">{{ $item->quantity }} pcs</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-400 text-[11px] pt-1 border-t border-slate-900">
                                <span>Weight: <strong class="text-slate-300">{{ number_format($item->weight, 1) }} kg</strong></span>
                                <span>Declared: <strong class="text-emerald-400">${{ number_format($item->declared_value, 2) }}</strong></span>
                            </div>
                            @if($item->hs_code)
                                <div class="text-[10px] font-mono text-slate-500">HS Code: {{ $item->hs_code }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-slate-500 py-3 text-center">No cargo items registered.</div>
                    @endforelse
                </div>

                @if($shipment->notes)
                    <div class="pt-3 border-t border-slate-800 space-y-1">
                        <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Operational Remarks</span>
                        <p class="text-xs text-slate-300 italic bg-slate-950/50 p-2.5 rounded-lg border border-slate-800/80">{{ $shipment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- REST API ENDPOINT REFERENCE -->
            <div class="p-6 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3 text-xs">
                <div class="flex items-center gap-2 text-indigo-400 font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    <span>Developer REST API Access</span>
                </div>
                <p class="text-slate-400 text-[11px]">Query this consignment programmatically using JSON endpoints:</p>
                <div class="p-2.5 rounded-lg bg-slate-950 font-mono text-[10px] text-sky-400 break-all select-all">
                    GET /api/v1/tracking/{{ $shipment->tracking_number }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADD TRACKING CHECKPOINT (For Admin & Operator) -->
@if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator()) && !$shipment->isTerminal())
<div id="eventModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="space-y-0.5">
                <h3 class="text-lg font-bold text-white">Log Tracking Checkpoint</h3>
                <p class="text-xs text-slate-400">Append immutable milestone to <span class="font-mono text-indigo-400">{{ $shipment->tracking_number }}</span></p>
            </div>
            <button onclick="document.getElementById('eventModal').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('shipments.events.store', $shipment->tracking_number) }}" method="POST" class="space-y-4">
            @csrf

            <!-- Status Transition -->
            <div>
                <label for="eventStatus" class="block text-xs font-semibold text-slate-300 mb-1">Checkpoint Status *</label>
                <select id="eventStatus" name="status" required class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="{{ $shipment->status }}">{{ $shipment->status }} (Keep status &bull; In-Transit Scan)</option>
                    @foreach(\App\Models\Shipment::ALLOWED_TRANSITIONS[$shipment->status] ?? [] as $nextStatus)
                        <option value="{{ $nextStatus }}">&rarr; Transition to {{ $nextStatus }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Predefined Logistics Location -->
            <div>
                <label for="eventLocationId" class="block text-xs font-semibold text-slate-300 mb-1">Logistics Hub / Location</label>
                <select id="eventLocationId" name="location_id" onchange="onLocationChange(this)" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Select Hub or Enter Custom Waypoint Below --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" data-name="{{ $loc->name }}" data-lat="{{ $loc->latitude }}" data-lng="{{ $loc->longitude }}">
                            {{ $loc->name }} ({{ $loc->code }}) - {{ $loc->country }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Custom Location Name -->
            <div>
                <label for="eventLocationName" class="block text-xs font-semibold text-slate-300 mb-1">Waypoint Name (Optional Override)</label>
                <input id="eventLocationName" type="text" name="location_name" placeholder="e.g. Berth 04, Singapore Anchorage" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Coordinates -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="eventLat" class="block text-xs font-semibold text-slate-300 mb-1">Latitude</label>
                    <input id="eventLat" type="number" step="0.000001" name="latitude" placeholder="e.g. 1.290270" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="eventLng" class="block text-xs font-semibold text-slate-300 mb-1">Longitude</label>
                    <input id="eventLng" type="number" step="0.000001" name="longitude" placeholder="e.g. 103.851959" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Timestamp -->
            <div>
                <label for="eventTime" class="block text-xs font-semibold text-slate-300 mb-1">Timestamp</label>
                <input id="eventTime" type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Description -->
            <div>
                <label for="eventDesc" class="block text-xs font-semibold text-slate-300 mb-1">Checkpoint Description *</label>
                <textarea id="eventDesc" name="description" rows="3" required placeholder="Describe milestone (e.g. Cargo cleared customs inspection and released to feeder carrier)" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
                <button type="button" onclick="document.getElementById('eventModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition-all">
                    Commit Checkpoint
                </button>
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
