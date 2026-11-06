@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center py-6 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold tracking-tight text-white">
            Log in to CargoTrace
        </h2>
        <p class="mt-2 text-center text-sm text-slate-400">
            Real-time shipment visibility & logistics operations
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-950/80 border border-slate-800 py-8 px-6 shadow-2xl rounded-2xl sm:px-10">
            <form class="space-y-5" method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">Email Address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               placeholder="user@example.com"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               placeholder="••••••••"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-700 rounded bg-slate-900">
                        <label for="remember" class="ml-2 block text-xs text-slate-400">
                            Remember me
                        </label>
                    </div>

                    <div class="text-xs">
                        <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300">
                            Need an account?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-600/30 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-all">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-6 border-t border-slate-800/80">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 text-center">Demo Accounts (After Seeding)</p>
                <div class="grid grid-cols-1 gap-1.5 text-xs text-slate-400">
                    <button type="button" onclick="fillCreds('admin@cargotrace.com', 'password')" class="text-left p-2 rounded-lg bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition-colors flex items-center justify-between">
                        <span class="font-medium text-rose-300">Admin: admin@cargotrace.com</span>
                        <span class="text-slate-500 font-mono text-[10px]">password</span>
                    </button>
                    <button type="button" onclick="fillCreds('operator@cargotrace.com', 'password')" class="text-left p-2 rounded-lg bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition-colors flex items-center justify-between">
                        <span class="font-medium text-amber-300">Operator: operator@cargotrace.com</span>
                        <span class="text-slate-500 font-mono text-[10px]">password</span>
                    </button>
                    <button type="button" onclick="fillCreds('customer@cargotrace.com', 'password')" class="text-left p-2 rounded-lg bg-slate-900/60 hover:bg-slate-900 border border-slate-800 transition-colors flex items-center justify-between">
                        <span class="font-medium text-emerald-300">Customer: customer@cargotrace.com</span>
                        <span class="text-slate-500 font-mono text-[10px]">password</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillCreds(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
@endsection
