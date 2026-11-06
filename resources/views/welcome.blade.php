<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950 text-slate-100 scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CargoTrace - Global Shipment Visibility & Logistics Tracking Platform</title>

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased selection:bg-indigo-500 selection:text-white bg-slate-950">

    <!-- Top Sticky Navigation Bar -->
    <header class="border-b border-slate-800/80 bg-slate-950/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-sky-400 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-100 to-slate-400">CargoTrace</span>
                        <span class="hidden sm:inline-block text-[10px] uppercase px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-400 font-mono font-semibold ml-1.5 border border-indigo-500/30">v1.0</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-300">
                <a href="#features" class="hover:text-white transition-colors">Features</a>
                <a href="#tracking" class="hover:text-white transition-colors">Live Tracking</a>
                <a href="#architecture" class="hover:text-white transition-colors">Architecture</a>
                <a href="#roles" class="hover:text-white transition-colors">Role Matrix</a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard ({{ Auth::user()->name }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all">
                        Register Account
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
        <!-- Ambient Glowing Background Grid -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(99,102,241,0.25),rgba(255,255,255,0))] pointer-events-none"></div>
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-sky-500/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Enterprise Logistics Visibility &middot; Laravel 12 + Service Layer
            </div>

            <!-- Main Headline -->
            <div class="max-w-4xl mx-auto space-y-4">
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white leading-tight">
                    Global Shipment Visibility & <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 via-sky-300 to-teal-300">Intelligent Tracking</span>
                </h1>
                <p class="text-base sm:text-xl text-slate-300 max-w-2xl mx-auto font-normal leading-relaxed">
                    End-to-end supply chain visibility with strict lifecycle state machines, deterministic ETA delay predictions, Sanctum REST APIs, and role-based operational controls.
                </p>
            </div>

            <!-- Interactive Quick Tracking Bar in Hero -->
            <div id="tracking" class="max-w-2xl mx-auto pt-2">
                <form onsubmit="event.preventDefault(); heroTrack();" class="p-2 sm:p-2.5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-2xl backdrop-blur flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input id="heroTrackInput" type="text"
                               placeholder="Enter Tracking Number (e.g. CT-20261101-9821)"
                               class="w-full pl-11 pr-4 py-3 bg-slate-950/70 border border-slate-800 rounded-xl text-sm font-mono text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2">
                        <span>Track Cargo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Quick Samples -->
                <div class="mt-3 flex items-center justify-center gap-2 flex-wrap text-xs text-slate-400">
                    <span>Try sample consignments:</span>
                    <button type="button" onclick="setHeroTrack('CT-20261101-9821')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 hover:border-indigo-500 font-mono text-sky-400 transition-colors">CT-20261101-9821 (In Transit)</button>
                    <button type="button" onclick="setHeroTrack('CT-20261105-4412')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 hover:border-indigo-500 font-mono text-indigo-400 transition-colors">CT-20261105-4412 (Booked)</button>
                    <button type="button" onclick="setHeroTrack('CT-20261020-1109')" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 hover:border-indigo-500 font-mono text-emerald-400 transition-colors">CT-20261020-1109 (Delivered)</button>
                </div>

                <!-- Hero Tracking Result Container -->
                <div id="heroResultModal" class="hidden mt-6 text-left p-6 rounded-2xl bg-slate-900/90 border border-indigo-500/30 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span class="text-sm font-bold text-white">Live Consignment Tracking Detail</span>
                        </div>
                        <button onclick="document.getElementById('heroResultModal').classList.add('hidden')" class="text-xs text-slate-400 hover:text-white">&times; Close</button>
                    </div>
                    <div id="heroResultContent"></div>
                </div>
            </div>

            <!-- Hero Highlights / Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto pt-6">
                <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/80">
                    <p class="text-2xl font-black text-white">100%</p>
                    <p class="text-xs text-slate-400 mt-1">Lifecycle Determinism</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/80">
                    <p class="text-2xl font-black text-indigo-400">&lt; 150ms</p>
                    <p class="text-xs text-slate-400 mt-1">REST API Latency</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/80">
                    <p class="text-2xl font-black text-sky-400">3 Roles</p>
                    <p class="text-xs text-slate-400 mt-1">Admin, Operator, Customer</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-800/80">
                    <p class="text-2xl font-black text-emerald-400">14 Tables</p>
                    <p class="text-xs text-slate-400 mt-1">MySQL Enterprise Schema</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CORE FEATURES SECTION -->
    <section id="features" class="py-20 border-t border-slate-800/80 bg-slate-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h2 class="text-xs font-bold tracking-widest text-indigo-400 uppercase">Engineered for Excellence</h2>
                <p class="text-3xl font-black text-white">Mission-Critical Supply Chain Features</p>
                <p class="text-sm text-slate-400">Focusing on robust software engineering, security, and strict business logic over superficial UI.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-indigo-500/40 transition-all space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Strict State Transitions</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Deterministic lifecycle states (CREATED &rarr; BOOKED &rarr; PICKED_UP &rarr; IN_TRANSIT &rarr; DELIVERED). Terminal states cannot be reversed, enforced directly in the Service layer.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-sky-500/40 transition-all space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-400 border border-sky-500/20 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Append-Only Timeline Logging</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Every container milestone is logged with exact event timestamps and GPS coordinates. Events are immutable, preventing history tampering and maintaining chain-of-custody.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 hover:border-emerald-500/40 transition-all space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Role-Based Access Control</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Admin, Logistics Operator, and Customer roles with dedicated Laravel Policies. Customers are strictly scoped to inspect only their own authorized consignments.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ARCHITECTURE & ROLE MATRIX SECTION -->
    <section id="roles" class="py-20 border-t border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <h2 class="text-xs font-bold tracking-widest text-indigo-400 uppercase">Security & Permissions</h2>
                <p class="text-3xl font-black text-white">Enterprise Role Matrix</p>
                <p class="text-sm text-slate-400">Strict backend authorization enforced at both HTTP Middleware and Policy layers.</p>
            </div>

            <div class="rounded-2xl bg-slate-900/60 border border-slate-800 overflow-hidden shadow-2xl">
                <table class="min-w-full divide-y divide-slate-800 text-left text-xs">
                    <thead class="bg-slate-950 text-slate-400 uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Capability / Resource</th>
                            <th class="px-6 py-4 text-center">Administrator</th>
                            <th class="px-6 py-4 text-center">Logistics Operator</th>
                            <th class="px-6 py-4 text-center">Customer</th>
                            <th class="px-6 py-4 text-center">Public (Guest)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <tr>
                            <td class="px-6 py-4 font-medium text-white">User Management & System RBAC</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Full Access</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-white">Carrier & Port Locations Master Data</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Full CRUD</td>
                            <td class="px-6 py-4 text-center text-sky-400 font-bold">&#10003; View / Update</td>
                            <td class="px-6 py-4 text-center text-slate-400">&#10003; View Only</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-white">Shipment Booking & Creation</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Create Any</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Create Any</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-white">Shipment Timeline & Visibility</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; All Shipments</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; All Shipments</td>
                            <td class="px-6 py-4 text-center text-amber-400 font-bold">&#10003; Own Consignments Only</td>
                            <td class="px-6 py-4 text-center text-sky-400 font-bold">&#10003; Public Track #</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 font-medium text-white">Event & Checkpoint Logging</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Permitted</td>
                            <td class="px-6 py-4 text-center text-emerald-400 font-bold">&#10003; Permitted</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                            <td class="px-6 py-4 text-center text-rose-500 font-bold">&#10005; Denied</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-12 text-slate-500 text-xs mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">CT</div>
                <span class="text-slate-300 font-bold">CargoTrace Visibility Platform</span>
                <span>&copy; {{ date('Y') }} UAS Pemrograman Web</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a>
                <a href="{{ url('/api/v1/health') }}" target="_blank" class="hover:text-white transition-colors font-mono">API Health</a>
            </div>
        </div>
    </footer>

    <!-- Script for Hero Live Tracking -->
    <script>
    function setHeroTrack(trackingNo) {
        document.getElementById('heroTrackInput').value = trackingNo;
        heroTrack();
    }

    async function heroTrack() {
        const input = document.getElementById('heroTrackInput').value.trim();
        if (!input) return;

        const modal = document.getElementById('heroResultModal');
        const content = document.getElementById('heroResultContent');
        modal.classList.remove('hidden');
        content.innerHTML = '<p class="text-slate-400">Querying real-time shipment manifest...</p>';

        try {
            const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input));
            const data = await res.json();

            if (res.ok && data.success) {
                const s = data.data;
                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 bg-slate-950/70 p-4 rounded-xl border border-slate-800">
                            <div>
                                <span class="text-xs text-slate-500">Tracking Reference:</span>
                                <p class="text-base font-black font-mono text-indigo-400">${s.tracking_number}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    ${s.status === 'DELIVERED' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                      (s.status === 'IN_TRANSIT' ? 'bg-sky-500/20 text-sky-300 border border-sky-500/30' :
                                      'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30')}">
                                    ${s.status}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-slate-500 block">Assigned Carrier</span>
                                <strong class="text-white">${s.carrier ? s.carrier.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-slate-500 block">Port of Origin</span>
                                <strong class="text-white">${s.origin ? s.origin.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-slate-500 block">Destination Port</span>
                                <strong class="text-white">${s.destination ? s.destination.name : 'N/A'}</strong>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-950/40 border border-slate-800">
                                <span class="text-slate-500 block">Est. Delivery</span>
                                <strong class="text-white">${s.estimated_delivery_at ? new Date(s.estimated_delivery_at).toLocaleDateString() : 'Pending'}</strong>
                            </div>
                        </div>

                        ${s.items && s.items.length ? `
                            <div class="p-3 rounded-lg bg-slate-950/50 border border-slate-800 space-y-1.5">
                                <span class="text-xs font-semibold text-slate-400 uppercase">Itemized Manifest (${s.items.length} items)</span>
                                <ul class="divide-y divide-slate-800/80 text-xs">
                                    ${s.items.map(it => `
                                        <li class="py-1 flex justify-between">
                                            <span class="text-slate-300">${it.description} &times; ${it.quantity}</span>
                                            <span class="font-mono text-slate-400">${it.weight_kg} kg | $${Number(it.value_amount).toLocaleString()}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        ` : ''}

                        <div class="pt-2 flex justify-end">
                            <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition-all">
                                <span>View Full Shipment Timeline</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `<p class="text-rose-400">${data.message || 'Consignment not found for tracking number.'}</p>`;
            }
        } catch (e) {
            content.innerHTML = `<p class="text-rose-400">Failed to query tracking data. Check connection.</p>`;
        }
    }
    </script>
</body>
</html>
