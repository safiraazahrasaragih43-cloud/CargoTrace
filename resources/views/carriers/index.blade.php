@extends('layouts.dashboard')

@section('title', 'Ekspedisi & Maskapai')
@section('page_title', 'Ekspedisi & Maskapai Kargo')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-espresso-900">Daftar Ekspedisi &amp; Maskapai Global</h1>
            <p class="text-xs text-espresso-500 mt-0.5">Kelola mitra perusahaan pelayaran laut, kargo udara, dan logistik darat.</p>
        </div>

        @if(auth()->check() && auth()->user()->can('create', \App\Models\Carrier::class))
            <button onclick="document.getElementById('newCarrierModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white text-xs font-bold shadow-lg shadow-apricot-500/25 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Ekspedisi</span>
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

    <!-- Search Bar -->
    <div class="p-5 rounded-3xl bg-white border border-cream-200/80 shadow-xs">
        <form method="GET" action="{{ route('carriers.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama ekspedisi atau kode SCAC (cth: MAEU, DHLG)..."
                   class="flex-1 px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                Cari Ekspedisi
            </button>
            @if(!empty($filters['search']))
                <a href="{{ route('carriers.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-espresso-700 text-xs font-bold flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Carriers Table -->
    <div class="rounded-3xl bg-white border border-cream-200/80 overflow-hidden shadow-[0_14px_35px_-15px_rgba(44,39,36,0.08)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nama Ekspedisi</th>
                        <th class="px-5 py-3.5">Kode</th>
                        <th class="px-5 py-3.5">Kontak</th>
                        <th class="px-5 py-3.5">Template URL Pelacakan</th>
                        <th class="px-5 py-3.5">Status</th>
                        @if(auth()->user()?->isAdmin())
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-espresso-800">
                    @forelse($carriers as $carrier)
                        <tr class="hover:bg-cream-50/80 transition-colors">
                            <td class="px-5 py-4 font-extrabold text-espresso-900">{{ $carrier->name }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-mono font-bold text-[11px]">{{ $carrier->code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-espresso-900 font-medium">{{ $carrier->contact_email ?: '—' }}</div>
                                <div class="text-[11px] text-espresso-500">{{ $carrier->phone ?: '' }}</div>
                            </td>
                            <td class="px-5 py-4 font-mono text-[11px] text-espresso-500 max-w-xs truncate">
                                {{ $carrier->tracking_url_template ?: 'API internal standar' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($carrier->is_active)
                                    <span class="px-2.5 py-1 rounded-full bg-sage-100 text-sage-700 font-bold text-[10px] uppercase">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-cream-200 text-espresso-500 font-bold text-[10px] uppercase">Nonaktif</span>
                                @endif
                            </td>
                            @if(auth()->user()?->isAdmin())
                                <td class="px-5 py-4 text-right">
                                    <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" onsubmit="return confirm('Hapus ekspedisi {{ $carrier->name }}?')">
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
                            <td colspan="6" class="px-5 py-10 text-center text-espresso-500">Belum ada ekspedisi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($carriers->hasPages())
            <div class="p-4 border-t border-cream-200 bg-cream-50/50">
                {{ $carriers->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL: ADD CARRIER (Admin Only) -->
@if(auth()->check() && auth()->user()->can('create', \App\Models\Carrier::class))
<div id="newCarrierModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-espresso-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg rounded-3xl bg-white border border-cream-200 shadow-2xl p-6 sm:p-8 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-cream-200">
            <div>
                <h3 class="text-lg font-extrabold text-espresso-900">Daftarkan Ekspedisi Baru</h3>
                <p class="text-xs text-espresso-500">Tambahkan mitra pelayaran laut, udara, atau darat.</p>
            </div>
            <button onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="p-1.5 rounded-full bg-cream-100 text-espresso-600 hover:text-espresso-900 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Nama Ekspedisi *</label>
                <input type="text" name="name" required placeholder="cth: Hapag-Lloyd AG"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Kode SCAC *</label>
                    <input type="text" name="code" required placeholder="cth: HLCU"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono uppercase text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
                <div>
                    <label class="block font-bold text-espresso-800 mb-1">Telepon Layanan</label>
                    <input type="text" name="phone" placeholder="+49 40 3001-0"
                           class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Email Kontak</label>
                <input type="email" name="contact_email" placeholder="support@ekspedisi.com"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <div>
                <label class="block font-bold text-espresso-800 mb-1">Template URL Pelacakan</label>
                <input type="text" name="tracking_url_template" placeholder="https://track.ekspedisi.com/{TRACKING_NUMBER}"
                       class="w-full px-3.5 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl font-mono text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">
            </div>

            <input type="hidden" name="is_active" value="1">

            <div class="pt-3 flex items-center justify-end gap-3 border-t border-cream-200">
                <button type="button" onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="px-4 py-2 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold shadow-lg shadow-apricot-500/25 cursor-pointer">
                    Simpan Ekspedisi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
