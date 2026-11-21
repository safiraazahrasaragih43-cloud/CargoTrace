@extends('layouts.dashboard')

@section('title', __('Log Audit Sistem'))
@section('page_title', __('Log Audit Sistem'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-espresso-900 text-cream-50 text-[10px] font-bold uppercase tracking-wider">{{ __('Khusus Admin') }}</span>
                <span class="text-xs text-espresso-500">{{ __('Keamanan & Kepatuhan Operasional') }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-espresso-900">{{ __('Jejak Audit Aktivitas Sistem') }}</h1>
            <p class="text-xs text-espresso-500 mt-0.5">{{ __('Rekam jejak seluruh mutasi pengiriman, pelacakan, kendala, dan alamat IP aktor secara kronologis.') }}</p>
        </div>
    </div>

    <!-- Filter Bar (Glassmorphism) -->
    <div class="p-5 rounded-3xl glass-card-strong">
        <form method="GET" action="{{ route('audit_logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Cari nama aktor, aksi, atau alamat IP...') }}"
                   class="sm:col-span-2 px-4 py-2.5 bg-white/80 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">

            <input type="text" name="action" value="{{ $filters['action'] ?? '' }}" placeholder="{{ __('Filter Aksi (cth: SHIPMENT_CREATED)') }}"
                   class="px-4 py-2.5 bg-white/80 border border-cream-200 rounded-2xl text-xs text-espresso-900 placeholder-espresso-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500">

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-2xl bg-sage-500 hover:bg-sage-600 text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                    {{ __('Saring Log') }}
                </button>
                @if(!empty($filters['search']) || !empty($filters['action']) || !empty($filters['entity_type']))
                    <a href="{{ route('audit_logs.index') }}" class="px-4 py-2.5 rounded-2xl bg-cream-100 hover:bg-cream-200 text-espresso-700 text-xs font-bold flex items-center justify-center">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="rounded-3xl glass-card-strong overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-cream-200 text-left text-xs">
                <thead class="bg-sage-50/90 text-espresso-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">{{ __('Waktu') }}</th>
                        <th class="px-5 py-3.5">{{ __('Aktor / Pengguna') }}</th>
                        <th class="px-5 py-3.5">{{ __('Aksi') }}</th>
                        <th class="px-5 py-3.5">{{ __('Entitas') }}</th>
                        <th class="px-5 py-3.5">{{ __('Alamat IP') }}</th>
                        <th class="px-5 py-3.5">{{ __('Perubahan Data') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-100 text-espresso-800">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/60 transition-colors align-top">
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-[11px] text-espresso-600">
                                {{ $log->created_at?->format('d M Y H:i:s') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-espresso-900">{{ $log->actor?->name ?? __('Sistem Otomatis') }}</div>
                                <div class="text-[11px] text-espresso-500">{{ $log->actor?->email ?? 'system@cargotrace.local' }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full bg-apricot-100 text-apricot-600 font-mono font-bold text-[10px] uppercase">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-semibold text-sage-800">
                                {{ $log->entity_type }} <span class="font-mono text-espresso-500">#{{ $log->entity_id ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-[11px] text-espresso-500">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="px-5 py-4 max-w-sm space-y-1.5">
                                @if(!empty($log->description))
                                    <p class="text-xs font-medium text-espresso-800">{{ $log->description }}</p>
                                @endif
                                @if(!empty($log->old_values) || !empty($log->new_values))
                                    <details class="text-[11px]">
                                        <summary class="cursor-pointer font-bold text-sage-700 hover:underline">{{ __('Lihat Muatan Perubahan') }}</summary>
                                        <div class="mt-2 space-y-1.5 font-mono text-[10px]">
                                            @if(!empty($log->old_values))
                                                <div class="p-2 rounded-xl bg-rose-50 text-rose-800 break-all">
                                                    <span class="font-bold">{{ __('Lama:') }}</span> {{ json_encode($log->old_values, JSON_UNESCAPED_SLASHES) }}
                                                </div>
                                            @endif
                                            @if(!empty($log->new_values))
                                                <div class="p-2 rounded-xl bg-sage-50 text-sage-800 break-all">
                                                    <span class="font-bold">{{ __('Baru:') }}</span> {{ json_encode($log->new_values, JSON_UNESCAPED_SLASHES) }}
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @elseif(empty($log->description))
                                    <span class="text-espresso-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-espresso-500">
                                {{ __('Belum ada catatan jejak audit yang cocok dengan filter.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-cream-200 bg-cream-50/50">
                {{ $logs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
