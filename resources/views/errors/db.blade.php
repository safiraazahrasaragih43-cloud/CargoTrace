@extends('layouts.app')

@section('title', __('Koneksi Database Cloud Belum Siap'))

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center py-10 px-4 text-center">
    <div class="max-w-xl w-full p-8 sm:p-10 rounded-3xl bg-white border border-cream-200/80 shadow-[0_20px_50px_-15px_rgba(44,39,36,0.1)] space-y-6">
        <div class="w-16 h-16 mx-auto rounded-3xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7zm0 5c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3m-16-5c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3"/></svg>
        </div>

        <div class="space-y-2">
            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider">
                Database Cloud &bull; {{ __('Konfigurasi Diperlukan') }}
            </span>
            <h1 class="text-2xl font-black text-espresso-900 tracking-tight">
                {{ __('Database Cloud Belum Terhubung') }}
            </h1>
            <p class="text-xs text-espresso-600 leading-relaxed">
                {{ __('Aplikasi CargoTrace berjalan normal di Vercel, namun belum terhubung ke server database MySQL cloud untuk membaca data tabel.') }}
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-cream-50 border border-cream-200 text-left space-y-2.5 text-xs text-espresso-700">
            <p class="font-bold text-espresso-900 text-[13px] flex items-center gap-1.5">
                <span>&#128161;</span> {{ __('Cara Menghubungkan ke Vercel:') }}
            </p>
            <ol class="list-decimal list-inside space-y-1.5 text-espresso-600 pl-1">
                <li>{{ __('Buka Dashboard Vercel proyek Anda') }} &rarr; <b>Settings</b> &rarr; <b>Environment Variables</b>.</li>
                <li>{{ __('Tambahkan kredensial database cloud Anda:') }}
                    <div class="mt-1.5 p-2.5 rounded-xl bg-white border border-cream-200 font-mono text-[11px] text-espresso-800 space-y-0.5">
                        <div>DB_CONNECTION=mysql</div>
                        <div>DB_HOST=<i>(host cloud MySQL / TiDB Anda)</i></div>
                        <div>DB_PORT=<i>(port cloud, misal: 4000 atau 3306)</i></div>
                        <div>DB_DATABASE=<i>(nama database cloud)</i></div>
                        <div>DB_USERNAME=<i>(user database cloud)</i></div>
                        <div>DB_PASSWORD=<i>(password database cloud)</i></div>
                    </div>
                </li>
                <li>{{ __('Lakukan Redeploy di Vercel agar variabel baru aktif.') }}</li>
            </ol>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <a href="{{ url('/') }}" class="flex-1 py-3 px-4 rounded-full bg-sage-500 hover:bg-sage-600 text-white font-bold text-xs shadow-md shadow-sage-500/20 transition-all flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>{{ __('Ke Halaman Beranda') }}</span>
            </a>
            <button onclick="window.location.reload()" class="flex-1 py-3 px-4 rounded-full bg-cream-100 hover:bg-cream-200 text-espresso-800 font-bold text-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>{{ __('Coba Lagi (Reload)') }}</span>
            </button>
        </div>
    </div>
</div>
@endsection
