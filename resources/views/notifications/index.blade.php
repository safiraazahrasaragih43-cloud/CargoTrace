@extends('layouts.dashboard')

@section('title', 'Notifikasi Sistem')
@section('page_title', 'Pusat Notifikasi Operasional')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Top Header & Mark All Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-espresso-900">Notifikasi Sistem</h1>
            <p class="text-xs text-espresso-500 mt-0.5">
                Pembaruan status pengiriman, peringatan keterlambatan ETA, dan laporan kendala secara langsung.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 rounded-full glass-sage text-xs font-bold text-sage-800">
                Belum Dibaca: <strong class="text-espresso-900">{{ $unreadCount ?? 0 }}</strong>
            </span>

            @if(($unreadCount ?? 0) > 0)
                <form method="POST" action="{{ route('notifications.read_all') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-md transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Tandai Semua Dibaca</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Flash Status -->
    @if(session('status'))
        <div class="p-4 rounded-2xl glass-sage text-sage-800 text-xs sm:text-sm font-semibold flex items-center gap-2.5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Notifications Feed -->
    <div class="rounded-3xl glass-card-strong overflow-hidden divide-y divide-cream-200/70">
        @forelse($notifications as $notif)
            <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4 {{ !$notif->isRead() ? 'bg-apricot-50/45' : '' }} transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-2xl shrink-0 flex items-center justify-center font-bold
                        @if(in_array($notif->type, [\App\Models\AppNotification::TYPE_ETA_DELAY, \App\Models\AppNotification::TYPE_EXCEPTION_ALERT])) bg-rose-100 text-rose-600
                        @elseif($notif->type === \App\Models\AppNotification::TYPE_EXCEPTION_RESOLVED) bg-sage-100 text-sage-700
                        @else bg-apricot-100 text-apricot-600
                        @endif">
                        @if(in_array($notif->type, [\App\Models\AppNotification::TYPE_ETA_DELAY, \App\Models\AppNotification::TYPE_EXCEPTION_ALERT]))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-extrabold text-espresso-900">{{ $notif->title }}</h3>
                            @if(!$notif->isRead())
                                <span class="px-2 py-0.5 rounded-full bg-apricot-500 text-white text-[10px] font-bold uppercase tracking-wider">Baru</span>
                            @endif
                            <span class="px-2 py-0.5 rounded-full bg-cream-100 text-espresso-600 text-[10px] font-mono font-bold">{{ $notif->type }}</span>
                        </div>

                        <p class="text-xs text-espresso-700 leading-relaxed">{{ $notif->message }}</p>

                        <div class="flex flex-wrap items-center gap-3 pt-1 text-[11px] text-espresso-500">
                            <span>{{ $notif->created_at?->diffForHumans() }} ({{ $notif->created_at?->format('d M Y H:i') }})</span>
                            @if($notif->shipment)
                                <a href="{{ route('shipments.show', $notif->shipment->tracking_number) }}" class="font-mono font-bold text-sage-700 hover:underline">
                                    Resi: {{ $notif->shipment->tracking_number }} &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 sm:pl-4">
                    @if(!$notif->isRead())
                        <form method="POST" action="{{ route('notifications.read', $notif) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-1.5 rounded-full bg-white hover:bg-sage-50 border border-cream-200 text-xs font-bold text-espresso-800 shadow-xs transition-colors cursor-pointer">
                                Tandai Dibaca
                            </button>
                        </form>
                    @else
                        <span class="px-3 py-1 rounded-full bg-cream-100 text-[11px] font-semibold text-espresso-500">
                            Dibaca
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center space-y-2">
                <p class="text-sm font-bold text-espresso-800">Belum ada notifikasi saat ini</p>
                <p class="text-xs text-espresso-500">Notifikasi otomatis muncul saat pengiriman dibuat, status berubah, atau terjadi kendala rute.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="p-4 rounded-2xl glass-card">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
