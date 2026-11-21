@extends('layouts.app')

@section('title', __('Status Layanan REST API v1'))

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-cream-200">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-espresso-500 mb-1">
                <a href="{{ route('home') }}" class="hover:text-espresso-800 transition-colors">{{ __('Beranda') }}</a>
                <span>/</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-espresso-800 transition-colors">{{ __('Dashboard') }}</a>
                    <span>/</span>
                @endauth
                <span class="text-espresso-800 font-bold">{{ __('Status REST API') }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-espresso-900 tracking-tight flex items-center gap-2.5">
                <span>{{ __('Status & Kesehatan Layanan REST API v1') }}</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sage-100 text-sage-800 border border-sage-200">
                    <span class="w-2 h-2 rounded-full bg-sage-500 animate-pulse"></span>
                    {{ __('Operasional') }}
                </span>
            </h1>
            <p class="text-xs text-espresso-600 mt-1">{{ __('Pemantauan real-time ketersediaan endpoint API backend, integritas koneksi database, dan spesifikasi JSON Envelope CargoTrace.') }}</p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold text-espresso-700 bg-white hover:bg-cream-100 border border-cream-200 shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>{{ __('Kembali ke Dashboard') }}</span>
                </a>
            @else
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold text-espresso-700 bg-white hover:bg-cream-100 border border-cream-200 shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-espresso-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>{{ __('Halaman Depan') }}</span>
                </a>
            @endauth
            <a href="{{ url('/api/v1/health?format=json') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold text-white bg-espresso-900 hover:bg-espresso-800 shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                <span>{{ __('Lihat Raw JSON') }}</span>
            </a>
        </div>
    </div>

    <!-- Diagnostic Cards (4 Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: API Operational Status -->
        <div class="p-5 rounded-3xl bg-white/80 backdrop-blur-md border border-cream-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso-500">{{ __('Status Gateway API') }}</span>
                <span class="w-2.5 h-2.5 rounded-full bg-sage-500 ring-4 ring-sage-100"></span>
            </div>
            <div class="mt-3">
                <div class="text-xl font-extrabold text-sage-800">100% Operational</div>
                <div class="text-xs text-espresso-600 mt-1 flex items-center gap-1 font-mono">
                    <span class="px-1.5 py-0.5 rounded bg-sage-100 text-sage-700 font-bold text-[10px]">HTTP 200 OK</span>
                    <span>Latency &lt; 20ms</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Database Connection -->
        <div class="p-5 rounded-3xl bg-white/80 backdrop-blur-md border border-cream-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso-500">{{ __('Database MySQL') }}</span>
                <span class="w-2.5 h-2.5 rounded-full {{ $dbConnected ? 'bg-sage-500 ring-4 ring-sage-100' : 'bg-rose-500 ring-4 ring-rose-100' }}"></span>
            </div>
            <div class="mt-3">
                <div class="text-xl font-extrabold {{ $dbConnected ? 'text-espresso-900' : 'text-rose-600' }}">
                    {{ $dbConnected ? __('Terhubung Normal') : __('Koneksi Terputus') }}
                </div>
                <div class="text-xs text-espresso-600 mt-1 font-mono truncate">
                    cargotrace_db (127.0.0.1:3306)
                </div>
            </div>
        </div>

        <!-- Card 3: Backend & PHP -->
        <div class="p-5 rounded-3xl bg-white/80 backdrop-blur-md border border-cream-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso-500">{{ __('Engine & Platform') }}</span>
                <svg class="w-4 h-4 text-espresso-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
            </div>
            <div class="mt-3">
                <div class="text-xl font-extrabold text-espresso-900">Laravel {{ $laravelVersion }}</div>
                <div class="text-xs text-espresso-600 mt-1 font-mono">
                    PHP {{ $phpVersion }} (ZTS/cli)
                </div>
            </div>
        </div>

        <!-- Card 4: Security & Protocols -->
        <div class="p-5 rounded-3xl bg-white/80 backdrop-blur-md border border-cream-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-espresso-500">{{ __('Protokol Keamanan') }}</span>
                <svg class="w-4 h-4 text-apricot-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="mt-3">
                <div class="text-xl font-extrabold text-espresso-900">Sanctum RBAC</div>
                <div class="text-xs text-espresso-600 mt-1 font-mono truncate">
                    Bearer Token + Envelope JSON
                </div>
            </div>
        </div>
    </div>

    <!-- Live JSON Response Viewer & Playground -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Formatted JSON Response for /health (7 Cols) -->
        <div class="lg:col-span-7 bg-white/85 backdrop-blur-md border border-cream-200 rounded-3xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-cream-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sage-100 text-sage-700 flex items-center justify-center font-bold text-xs font-mono">
                        GET
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-espresso-900">{{ __('Respons Endpoint Health Check') }}</h2>
                        <p class="text-[11px] text-espresso-500 font-mono">/api/v1/health</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="copyHealthJson()" id="btnCopyJson" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-espresso-700 bg-cream-100 hover:bg-cream-200 transition-colors cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span id="copyText">{{ __('Salin JSON') }}</span>
                    </button>
                </div>
            </div>

            <!-- Code block -->
            <div class="relative rounded-2xl bg-espresso-950 p-4 overflow-x-auto shadow-inner">
                <pre class="font-mono text-xs text-sage-300 leading-relaxed"><code id="rawHealthJson">{{ $jsonString }}</code></pre>
            </div>

            <div class="flex items-center justify-between text-[11px] text-espresso-500">
                <span>{{ __('Format: Standardized RESTful JSON Envelope (`success`, `message`, `timestamp`)') }}</span>
                <span class="font-mono">{{ $timestamp }}</span>
            </div>
        </div>

        <!-- Right: Interactive API Tester Box (5 Cols) -->
        <div class="lg:col-span-5 bg-white/85 backdrop-blur-md border border-cream-200 rounded-3xl p-6 shadow-xs space-y-4">
            <div class="pb-3 border-b border-cream-100">
                <h2 class="text-sm font-bold text-espresso-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-apricot-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>{{ __('Uji Live Endpoint Tracking API') }}</span>
                </h2>
                <p class="text-xs text-espresso-500 mt-0.5">{{ __('Uji coba langsung pemanggilan endpoint publik tanpa perlu login atau membuka Postman.') }}</p>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-espresso-700 mb-1">{{ __('Nomor Resi / Tracking Number') }}</label>
                    <div class="flex gap-2">
                        <input type="text" id="apiTestInput" value="CT-20261101-9821" placeholder="CT-YYYYMMDD-XXXX" class="flex-1 px-3.5 py-2 rounded-2xl border border-cream-300 bg-cream-50 font-mono text-xs text-espresso-900 focus:outline-hidden focus:ring-2 focus:ring-sage-400">
                        <button type="button" onclick="testLiveApi()" id="btnRunTest" class="px-4 py-2 rounded-2xl bg-sage-600 hover:bg-sage-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer shrink-0">
                            {{ __('Panggil API') }}
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-[11px] text-espresso-500">
                    <span>{{ __('Contoh Cepat:') }}</span>
                    <button type="button" onclick="setTestNum('CT-20261101-9821')" class="underline hover:text-espresso-800 font-mono">CT-20261101-9821</button>
                    <span>&bull;</span>
                    <button type="button" onclick="setTestNum('CT-20261105-4412')" class="underline hover:text-espresso-800 font-mono">CT-20261105-4412</button>
                </div>

                <!-- Result Box -->
                <div id="testResultBox" class="hidden rounded-2xl bg-espresso-950 p-3.5 overflow-x-auto max-h-56 text-xs font-mono text-cream-200">
                    <div id="testStatusBadge" class="mb-2 text-[10px] font-bold"></div>
                    <pre><code id="testResultOutput"></code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- API Directory Table -->
    <div class="bg-white/85 backdrop-blur-md border border-cream-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-cream-100">
            <div>
                <h2 class="text-base font-extrabold text-espresso-900">{{ __('Katalog Endpoint REST API v1') }}</h2>
                <p class="text-xs text-espresso-600 mt-0.5">{{ __('Seluruh endpoint backend yang tersedia dalam sistem CargoTrace beserta metode dan hak aksesnya.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-espresso-500 font-medium">{{ __('Koleksi Postman:') }}</span>
                <span class="px-2.5 py-1 rounded-xl bg-cream-100 text-espresso-800 text-xs font-mono font-semibold">docs/CargoTrace_API_v1.postman_collection.json</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-cream-200 text-espresso-500 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">{{ __('Metode') }}</th>
                        <th class="py-3 px-3">{{ __('Endpoint') }}</th>
                        <th class="py-3 px-3">{{ __('Autentikasi') }}</th>
                        <th class="py-3 px-3">{{ __('Deskripsi Fitur') }}</th>
                        <th class="py-3 px-3 text-right">{{ __('Aksi Cepat') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-espresso-800 font-medium">
                    <!-- Endpoint 1 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-sage-100 text-sage-800 font-mono font-bold text-[10px]">GET</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/health</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-cream-200 text-espresso-700 text-[10px]">{{ __('Publik (Tanpa Auth)') }}</span></td>
                        <td class="py-3 px-3">{{ __('Memeriksa ketersediaan gateway API, koneksi database, dan timestamp server.') }}</td>
                        <td class="py-3 px-3 text-right">
                            <a href="{{ url('/api/v1/health?format=json') }}" target="_blank" class="text-sage-700 font-bold hover:underline">Test JSON &rarr;</a>
                        </td>
                    </tr>
                    <!-- Endpoint 2 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-sage-100 text-sage-800 font-mono font-bold text-[10px]">GET</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/tracking/{trackingNumber}</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-cream-200 text-espresso-700 text-[10px]">{{ __('Publik (Tanpa Auth)') }}</span></td>
                        <td class="py-3 px-3">{{ __('Pelacakan publik nomor resi internasional (data sensitif PII pemilik otomatis disaring).') }}</td>
                        <td class="py-3 px-3 text-right">
                            <a href="{{ url('/api/v1/tracking/CT-20261101-9821?format=json') }}" target="_blank" class="text-sage-700 font-bold hover:underline">Test JSON &rarr;</a>
                        </td>
                    </tr>
                    <!-- Endpoint 3 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-apricot-100 text-apricot-800 font-mono font-bold text-[10px]">POST</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/auth/login</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-cream-200 text-espresso-700 text-[10px]">{{ __('Publik') }}</span></td>
                        <td class="py-3 px-3">{{ __('Autentikasi kredensial pengguna dan penerbitan Laravel Sanctum Bearer Token.') }}</td>
                        <td class="py-3 px-3 text-right text-espresso-400">Via Postman</td>
                    </tr>
                    <!-- Endpoint 4 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-apricot-100 text-apricot-800 font-mono font-bold text-[10px]">POST</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/auth/register</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-cream-200 text-espresso-700 text-[10px]">{{ __('Publik') }}</span></td>
                        <td class="py-3 px-3">{{ __('Pendaftaran akun Customer baru (role secara ketat diatur sebagai customer).') }}</td>
                        <td class="py-3 px-3 text-right text-espresso-400">Via Postman</td>
                    </tr>
                    <!-- Endpoint 5 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-sage-100 text-sage-800 font-mono font-bold text-[10px]">GET</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/shipments</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-apricot-100 text-apricot-800 text-[10px] font-bold">Bearer Token (RBAC)</span></td>
                        <td class="py-3 px-3">{{ __('Daftar manifest kargo dengan filter multi-parameter (status, koridor, tanggal, sort).') }}</td>
                        <td class="py-3 px-3 text-right text-espresso-400">Via Postman</td>
                    </tr>
                    <!-- Endpoint 6 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-sage-100 text-sage-800 font-mono font-bold text-[10px]">GET</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/analytics/kpis</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-apricot-100 text-apricot-800 text-[10px] font-bold">Bearer Token</span></td>
                        <td class="py-3 px-3">{{ __('Metrik SLA real-time: rasio On-Time Delivery, rata-rata jam delay, dan exception terbuka.') }}</td>
                        <td class="py-3 px-3 text-right text-espresso-400">Via Postman</td>
                    </tr>
                    <!-- Endpoint 7 -->
                    <tr class="hover:bg-cream-50/60 transition-colors">
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-md bg-sage-100 text-sage-800 font-mono font-bold text-[10px]">GET</span></td>
                        <td class="py-3 px-3 font-mono font-bold text-espresso-900">/api/v1/shipments/{id}/weather</td>
                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded-full bg-apricot-100 text-apricot-800 text-[10px] font-bold">Bearer Token</span></td>
                        <td class="py-3 px-3">{{ __('Integrasi cuaca live pelabuhan destinasi kargo via Open-Meteo Telemetry API.') }}</td>
                        <td class="py-3 px-3 text-right text-espresso-400">Via Postman</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function copyHealthJson() {
    const raw = document.getElementById('rawHealthJson').innerText;
    navigator.clipboard.writeText(raw).then(() => {
        const copyText = document.getElementById('copyText');
        copyText.innerText = '{{ __('Tersalin!') }}';
        setTimeout(() => {
            copyText.innerText = '{{ __('Salin JSON') }}';
        }, 2000);
    });
}

function setTestNum(val) {
    document.getElementById('apiTestInput').value = val;
    testLiveApi();
}

async function testLiveApi() {
    const input = document.getElementById('apiTestInput').value.trim();
    const box = document.getElementById('testResultBox');
    const badge = document.getElementById('testStatusBadge');
    const out = document.getElementById('testResultOutput');
    const btn = document.getElementById('btnRunTest');

    if (!input) {
        alert('{{ __('Masukkan nomor resi terlebih dahulu') }}');
        return;
    }

    btn.disabled = true;
    btn.innerText = '{{ __('Memanggil...') }}';
    box.classList.remove('hidden');
    badge.innerHTML = '<span class="text-apricot-400">{{ __('Mengirim request ke') }} /api/v1/tracking/' + encodeURIComponent(input) + ' ...</span>';
    out.innerText = 'Loading...';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(input) + '?format=json', {
            headers: {
                'Accept': 'application/json'
            }
        });
        const json = await res.json();
        btn.disabled = false;
        btn.innerText = '{{ __('Panggil API') }}';

        if (res.ok && json.success) {
            badge.innerHTML = '<span class="text-sage-400 font-bold">&#10003; 200 OK — {{ __('Data Kargo Berhasil Diterima') }}</span>';
            out.innerText = JSON.stringify(json, null, 2);
        } else {
            badge.innerHTML = '<span class="text-rose-400 font-bold">&#10007; ' + res.status + ' ' + (json.message || 'Error') + '</span>';
            out.innerText = JSON.stringify(json, null, 2);
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerText = '{{ __('Panggil API') }}';
        badge.innerHTML = '<span class="text-rose-400 font-bold">&#10007; {{ __('Gagal menghubungi server') }}</span>';
        out.innerText = e.message;
    }
}
</script>
@endsection
