@extends('layouts.app')

@section('title', __('500 - Kesalahan Server'))

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center py-10 px-4 text-center">
    <div class="max-w-md w-full p-8 sm:p-10 rounded-3xl bg-white border border-cream-200/80 shadow-[0_20px_50px_-15px_rgba(44,39,36,0.1)] space-y-6">
        <div class="w-16 h-16 mx-auto rounded-3xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-600 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>

        <div class="space-y-2">
            <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold uppercase tracking-wider">
                HTTP 500 &bull; {{ __('Server Error') }}
            </span>
            <h1 class="text-2xl font-black text-espresso-900 tracking-tight">
                {{ __('Terjadi Kesalahan Server') }}
            </h1>
            <p class="text-xs text-espresso-600 leading-relaxed">
                {{ __('Sistem mengalami kendala saat memproses permintaan. Silakan muat ulang halaman atau periksa konfigurasi server.') }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button onclick="window.location.reload()" class="flex-1 py-3 px-4 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold text-xs shadow-md shadow-apricot-500/20 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>{{ __('Muat Ulang Halaman') }}</span>
            </button>
            <a href="{{ url('/') }}" class="flex-1 py-3 px-4 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>{{ __('Ke Beranda') }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
