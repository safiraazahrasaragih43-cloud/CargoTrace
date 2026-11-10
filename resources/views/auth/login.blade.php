@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-[75vh] flex flex-col justify-center items-center py-8 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center space-y-2">
        <div class="inline-flex items-center px-3 py-1 rounded-full bg-sage-100 text-sage-700 text-xs font-bold">
            CargoTrace Portal
        </div>
        <h2 class="text-3xl font-extrabold tracking-tight text-espresso-900">
            Log in to CargoTrace
        </h2>
        <p class="text-sm text-espresso-500">
            Real-time shipment visibility &amp; logistics operations
        </p>
    </div>

    <div class="mt-7 sm:mx-auto sm:w-full sm:max-w-md w-full">
        <div class="bg-white border border-cream-200/80 py-8 px-6 shadow-[0_16px_40px_-14px_rgba(44,39,36,0.08)] rounded-3xl sm:px-10">
            <form class="space-y-5" method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           value="{{ old('email') }}"
                           placeholder="user@example.com"
                           class="block w-full px-4 py-3 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 focus:border-transparent text-sm transition-all">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-espresso-800 uppercase tracking-wider mb-1.5">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           placeholder="••••••••"
                           class="block w-full px-4 py-3 bg-cream-100/70 border border-cream-200 rounded-2xl placeholder-espresso-400 text-espresso-900 focus:outline-none focus:bg-white focus:ring-2 focus:ring-sage-500 focus:border-transparent text-sm transition-all">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-sage-600 focus:ring-sage-500 border-cream-300 rounded bg-cream-50">
                        <label for="remember" class="ml-2 block text-xs font-medium text-espresso-600">
                            Remember me
                        </label>
                    </div>

                    <div class="text-xs">
                        <a href="{{ route('register') }}" class="font-bold text-apricot-600 hover:text-apricot-500">
                            Need an account?
                        </a>
                    </div>
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 rounded-full shadow-lg shadow-apricot-500/25 text-sm font-bold text-white bg-apricot-500 hover:bg-apricot-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-apricot-500 transition-all cursor-pointer">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-7 pt-6 border-t border-cream-200">
                <p class="text-[11px] font-bold text-espresso-500 uppercase tracking-wider mb-3 text-center">Quick Demo Accounts</p>
                <div class="grid grid-cols-1 gap-2 text-xs">
                    <button type="button" onclick="fillCreds('admin@cargotrace.com', 'password')" class="text-left px-3.5 py-2.5 rounded-2xl bg-cream-50 hover:bg-apricot-50 border border-cream-200 hover:border-apricot-200 transition-all flex items-center justify-between cursor-pointer">
                        <span class="font-bold text-apricot-600">Admin: admin@cargotrace.com</span>
                        <span class="text-espresso-500 font-mono text-[10px]">password</span>
                    </button>
                    <button type="button" onclick="fillCreds('operator@cargotrace.com', 'password')" class="text-left px-3.5 py-2.5 rounded-2xl bg-cream-50 hover:bg-sage-50 border border-cream-200 hover:border-sage-200 transition-all flex items-center justify-between cursor-pointer">
                        <span class="font-bold text-sage-700">Operator: operator@cargotrace.com</span>
                        <span class="text-espresso-500 font-mono text-[10px]">password</span>
                    </button>
                    <button type="button" onclick="fillCreds('customer@cargotrace.com', 'password')" class="text-left px-3.5 py-2.5 rounded-2xl bg-cream-50 hover:bg-cream-100 border border-cream-200 transition-all flex items-center justify-between cursor-pointer">
                        <span class="font-bold text-espresso-800">Customer: customer@cargotrace.com</span>
                        <span class="text-espresso-500 font-mono text-[10px]">password</span>
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
