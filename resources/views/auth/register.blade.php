@extends('layouts.app')

@section('title', __('Daftar Akun'))

@section('content')
<div class="min-h-[75vh] flex flex-col justify-center items-center py-8 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center space-y-2">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-xs font-bold">
            {{ __('Registrasi Pelanggan') }}
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-espresso-900">
            {{ __('Buat Akun Baru') }}
        </h2>
        <p class="text-sm text-espresso-500">
            {{ __('Daftar untuk melacak dan memantau pengiriman di CargoTrace') }}
        </p>
    </div>

    <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-md w-full">
        <div class="bg-white border border-cream-200/80 py-8 px-6 shadow-[0_16px_40px_-14px_rgba(44,39,36,0.08)] rounded-3xl sm:px-10">
            <form class="space-y-4" method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('Nama Lengkap') }}</label>
                    <input id="name" name="name" type="text" autocomplete="name" required
                           value="{{ old('name') }}"
                           placeholder="cth: Budi Santoso"
                           class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('Alamat Email') }}</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email') }}"
                           placeholder="nama@perusahaan.com"
                           class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="phone" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('No. Telepon') }}</label>
                        <input id="phone" name="phone" type="text"
                               value="{{ old('phone') }}"
                               placeholder="+62 812..."
                               class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                    </div>
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('Perusahaan') }}</label>
                        <input id="company_name" name="company_name" type="text"
                               value="{{ old('company_name') }}"
                               placeholder="PT Logistik Nusantara"
                               class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('Kata Sandi') }}</label>
                    <input id="password" name="password" type="password" required
                           placeholder="{{ __('Minimal 8 karakter') }}"
                           class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">{{ __('Konfirmasi Kata Sandi') }}</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           placeholder="{{ __('Ulangi kata sandi') }}"
                           class="block w-full px-4 py-2.5 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 text-sm transition-all">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-full shadow-lg shadow-apricot-500/25 text-sm font-bold text-white bg-apricot-500 hover:bg-apricot-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-apricot-500 transition-all cursor-pointer">
                        {{ __('Daftar Akun') }}
                    </button>
                </div>
            </form>

            <div class="mt-5 text-center text-xs text-espresso-500">
                {{ __('Sudah punya akun?') }}
                <a href="{{ route('login') }}" class="font-bold text-apricot-600 hover:text-apricot-500">
                    {{ __('Masuk di sini') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
