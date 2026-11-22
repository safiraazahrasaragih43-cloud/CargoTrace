@extends('layouts.app')

@section('title', __('419 - Sesi Halaman Berakhir'))

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center py-10 px-4 text-center">
    <div class="max-w-md w-full p-8 sm:p-10 rounded-3xl bg-white border border-cream-200/80 shadow-[0_20px_50px_-15px_rgba(44,39,36,0.1)] space-y-6">
        <div class="w-16 h-16 mx-auto rounded-3xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>

        <div class="space-y-2">
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider">
                HTTP 419 &bull; {{ __('Sesi Kedaluwarsa') }}
            </span>
            <h1 class="text-2xl font-black text-espresso-900 tracking-tight">
                {{ __('Sesi Halaman Telah Berakhir') }}
            </h1>
            <p class="text-xs text-espresso-600 leading-relaxed">
                {{ __('Halaman ini dibiarkan terbuka terlalu lama sehingga token keamanan kedaluwarsa demi melindungi keamanan data Anda.') }}
            </p>
        </div>

        <div class="p-3.5 rounded-2xl bg-cream-50 border border-cream-200 text-espresso-500 text-[11px] leading-relaxed">
            <span class="font-bold text-espresso-700">&#128161; {{ __('Solusi Cepat:') }}</span>
            {{ __('Cukup muat ulang halaman atau klik tombol di bawah untuk mendapatkan sesi baru dan melanjutkan.') }}
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button onclick="window.location.reload()" class="flex-1 py-3 px-4 rounded-full bg-apricot-500 hover:bg-apricot-600 text-white font-bold text-xs shadow-md shadow-apricot-500/20 transition-all cursor-pointer flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>{{ __('Muat Ulang Halaman') }}</span>
            </button>
            <a href="{{ route('login') }}" class="flex-1 py-3 px-4 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                <span>{{ __('Masuk Kembali') }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
