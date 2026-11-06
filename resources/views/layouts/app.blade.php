<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CargoTrace') }} - @yield('title', 'Global Shipment Visibility')</title>

    <!-- Tailwind & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased selection:bg-indigo-500 selection:text-white">
    <!-- Top Navigation -->
    <header class="border-b border-slate-800 bg-slate-950/70 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-indigo-600 to-sky-400 flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        CT
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-wider bg-clip-text text-transparent bg-gradient-to-r from-white via-slate-200 to-slate-400">CargoTrace</span>
                        <span class="hidden sm:inline-block text-xs uppercase px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-400 font-mono font-medium ml-1.5">v1.0</span>
                    </div>
                </a>
            </div>

            <!-- User Menu / Actions -->
            <div class="flex items-center gap-4">
                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-slate-200">{{ Auth::user()->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                @if(Auth::user()->isAdmin()) bg-rose-500/10 text-rose-400 border border-rose-500/20
                                @elseif(Auth::user()->isOperator()) bg-amber-500/10 text-amber-400 border border-amber-500/20
                                @else bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                @endif">
                                {{ Auth::user()->role }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-colors">
                                Sign Out
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-lg text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/30 transition-all">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-500/30 bg-emerald-950/40 p-4 text-emerald-300 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl border border-rose-500/30 bg-rose-950/40 p-4 text-rose-300">
                <div class="flex items-center gap-2 font-semibold mb-1">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please review the errors below:</span>
                </div>
                <ul class="list-disc list-inside text-sm text-rose-400 space-y-1">
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
    <footer class="border-t border-slate-800/80 bg-slate-950/40 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} CargoTrace Platform. Enterprise Global Shipment Tracking.</p>
    </footer>
</body>
</html>
