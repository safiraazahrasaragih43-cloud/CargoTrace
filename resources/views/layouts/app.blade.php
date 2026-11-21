<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream-50 text-espresso-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CargoTrace') }} - @yield('title', __('Visibilitas Pengiriman Global'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased selection:bg-apricot-500 selection:text-white bg-cream-50 text-espresso-900">
    <!-- Top Soft Navigation -->
    <header class="border-b border-cream-200/80 bg-cream-50/85 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-2xl bg-sage-500 flex items-center justify-center text-white font-extrabold text-sm shadow-md shadow-sage-500/25 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight text-espresso-900">CargoTrace</span>
                        <span class="hidden sm:inline-block text-[10px] uppercase px-2 py-0.5 rounded-full bg-sage-100 text-sage-700 font-semibold ml-1.5">{{ __('Logistik') }}</span>
                    </div>
                </a>
            </div>

            <!-- User Menu / Actions -->
            <div class="flex items-center gap-3">
                @include('components.language-switcher')

                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-espresso-900">{{ Auth::user()->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                @if(Auth::user()->isAdmin()) bg-apricot-100 text-apricot-600
                                @elseif(Auth::user()->isOperator()) bg-sage-100 text-sage-700
                                @else bg-cream-200 text-espresso-800
                                @endif">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-4 py-1.5 rounded-full text-xs font-semibold text-espresso-800 bg-white hover:bg-cream-100 border border-cream-200 shadow-xs transition-colors cursor-pointer">
                                {{ __('Keluar') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full text-xs font-semibold text-espresso-800 hover:bg-cream-100 transition-colors">
                            {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-1.5 rounded-full text-xs font-bold bg-apricot-500 hover:bg-apricot-600 text-white shadow-md shadow-apricot-500/25 transition-all">
                            {{ __('Daftar') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(request()->hasSession() && session('success'))
            <div class="mb-4 rounded-2xl border border-sage-300 bg-sage-100 p-4 text-sage-700 flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5 text-sage-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700 shadow-xs">
                <div class="flex items-center gap-2 font-bold text-sm mb-1">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ __('Harap periksa kembali kesalahan berikut:') }}</span>
                </div>
                <ul class="list-disc list-inside text-xs text-rose-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-cream-200/80 bg-cream-100/50 py-6 text-center text-xs text-espresso-500">
        <p>&copy; {{ date('Y') }} {{ __('Platform CargoTrace. Sistem Pelacakan Pengiriman Global Terpadu.') }}</p>
    </footer>
</body>
</html>
