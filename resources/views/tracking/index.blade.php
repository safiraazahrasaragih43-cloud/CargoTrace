@extends('layouts.dashboard')

@section('title', __('Lacak Kargo Cepat'))
@section('page_title', __('Lacak Kargo Cepat'))

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Hero Search Card -->
    <div class="relative overflow-hidden rounded-3xl bg-sage-500 text-white p-7 sm:p-10 shadow-[0_18px_45px_-15px_rgba(135,152,128,0.45)]">
        <div class="absolute -right-12 -bottom-12 w-56 h-56 rounded-full bg-white/10 pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl space-y-5">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                {{ __('Telemetri REST Real-Time') }}
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold leading-tight">
                {{ __('Lacak pengiriman apa pun di seluruh pelabuhan & checkpoint global') }}
            </h1>
            <p class="text-sm text-sage-100">
                {{ __('Masukkan nomor resi CargoTrace Anda (cth: CT-20261101-9821) untuk memantau posisi langsung dan catatan perjalanan.') }}
            </p>

            <form onsubmit="event.preventDefault(); liveTrackLookup();" class="flex flex-col sm:flex-row gap-2.5 pt-1">
                <input id="liveTrackInput" type="text" value="{{ $initialQuery ?? '' }}" placeholder="CT-YYYYMMDD-XXXX"
                       class="flex-1 px-4 py-3.5 bg-white border border-white rounded-2xl text-sm font-mono text-espresso-900 placeholder-espresso-400 shadow-lg focus:outline-none focus:ring-2 focus:ring-apricot-400">
                <button type="submit" class="px-6 py-3.5 rounded-2xl bg-apricot-500 hover:bg-apricot-600 text-white font-bold text-sm shadow-lg shadow-apricot-500/30 transition-all cursor-pointer shrink-0">
                    {{ __('Lacak Pengiriman') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Sample Quick Picks -->
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-espresso-500 uppercase tracking-wider">{{ __('Pengiriman Terkini') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($recentShipments as $sample)
                <button type="button" onclick="selectTrack('{{ $sample->tracking_number }}')" class="text-left p-4 rounded-3xl bg-white hover:bg-cream-100/70 border border-cream-200 shadow-xs transition-all space-y-2 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span class="font-mono font-bold text-xs text-sage-700">{{ $sample->tracking_number }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-apricot-100 text-apricot-600">{{ $sample->status }}</span>
                    </div>
                    <div class="text-xs font-bold text-espresso-900 truncate">
                        {{ $sample->origin?->name ?? __('Asal') }} &rarr; {{ $sample->destination?->name ?? __('Tujuan') }}
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Result Card -->
    <div id="liveTrackResult" class="hidden rounded-3xl bg-white border border-cream-200 p-6 sm:p-8 shadow-[0_18px_40px_-15px_rgba(44,39,36,0.1)] space-y-5">
        <div id="liveTrackBody"></div>
    </div>
</div>

<script>
function selectTrack(no) {
    document.getElementById('liveTrackInput').value = no;
    liveTrackLookup();
}

async function liveTrackLookup() {
    const esc = window.escapeHtml || ((v) => String(v ?? '').replace(/[&<>"'`]/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c])));
    const val = document.getElementById('liveTrackInput').value.trim();
    if (!val) return;

    const card = document.getElementById('liveTrackResult');
    const body = document.getElementById('liveTrackBody');
    card.classList.remove('hidden');
    body.innerHTML = '<p class="text-sm text-espresso-500">' + @json(__('Mengambil detail pengiriman secara langsung...')) + '</p>';

    try {
        const res = await fetch('/api/v1/tracking/' + encodeURIComponent(val));
        const json = await res.json();

        if (res.ok && json.success) {
            const s = json.data;
            const events = s.tracking_events || [];
            const txtRef = @json(__('Referensi Nomor Resi'));
            const txtTimeline = @json(__('Timeline Lengkap'));
            const txtCarrier = @json(__('Ekspedisi'));
            const txtOrigin = @json(__('Asal'));
            const txtDest = @json(__('Tujuan'));
            const txtCurrLoc = @json(__('Posisi Saat Ini'));
            const txtNone = @json(__('Tidak Ada'));
            const txtAtOrigin = @json(__('Di Lokasi Asal'));
            const txtHistory = @json(__('Riwayat Checkpoint'));
            const txtNoEvents = @json(__('Belum ada riwayat checkpoint tercatat.'));

            body.innerHTML = `
                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-cream-200">
                        <div>
                            <span class="text-xs font-bold text-espresso-500 uppercase">${txtRef}</span>
                            <h3 class="text-2xl font-extrabold font-mono text-sage-700">${esc(s.tracking_number)}</h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-sage-100 text-sage-700">${esc(s.status)}</span>
                            <a href="/shipments/${encodeURIComponent(s.tracking_number)}" class="px-4 py-2 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-md transition-all">
                                ${txtTimeline} &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                            <span class="text-espresso-500 block font-semibold">${txtCarrier}</span>
                            <strong class="text-espresso-900">${s.carrier ? esc(s.carrier.name) : txtNone}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                            <span class="text-espresso-500 block font-semibold">${txtOrigin}</span>
                            <strong class="text-espresso-900">${s.origin ? esc(s.origin.name) : txtNone}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                            <span class="text-espresso-500 block font-semibold">${txtDest}</span>
                            <strong class="text-espresso-900">${s.destination ? esc(s.destination.name) : txtNone}</strong>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200">
                            <span class="text-espresso-500 block font-semibold">${txtCurrLoc}</span>
                            <strong class="text-sage-700">${s.current_location ? esc(s.current_location.name) : txtAtOrigin}</strong>
                        </div>
                    </div>

                    ${events.length ? `
                        <div class="space-y-2.5">
                            <h4 class="text-xs font-bold text-espresso-500 uppercase tracking-wider">${txtHistory} (${events.length})</h4>
                            <div class="space-y-2">
                                ${events.slice().reverse().map(ev => `
                                    <div class="p-3.5 rounded-2xl bg-cream-50/70 border border-cream-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-apricot-100 text-apricot-600 mr-2">${esc(ev.status)}</span>
                                            <strong class="text-espresso-900">${esc(ev.location_name)}</strong>
                                            <p class="text-espresso-600 mt-1">${esc(ev.description)}</p>
                                        </div>
                                        <span class="font-mono text-[11px] text-espresso-500 shrink-0">${esc(new Date(ev.event_time).toLocaleString())}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : `<p class="text-xs text-espresso-500">${txtNoEvents}</p>`}
                </div>
            `;
        } else {
            body.innerHTML = `<p class="text-sm font-bold text-rose-600">${esc(json.message) || @json(__('Nomor resi pengiriman tidak ditemukan.'))}</p>`;
        }
    } catch (e) {
        body.innerHTML = '<p class="text-sm font-bold text-rose-600">' + @json(__('Terjadi kesalahan saat menghubungi API pelacakan.')) + '</p>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('liveTrackInput').value.trim()) {
        liveTrackLookup();
    }
});
</script>
@endsection
