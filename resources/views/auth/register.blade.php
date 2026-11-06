@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[70vh] flex flex-col justify-center items-center py-6 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold tracking-tight text-white">
            Create an Account
        </h2>
        <p class="mt-2 text-center text-sm text-slate-400">
            Sign up to track and manage shipments on CargoTrace
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-950/80 border border-slate-800 py-8 px-6 shadow-2xl rounded-2xl sm:px-10">
            <form class="space-y-4" method="POST" action="{{ route('register') }}">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300">Full Name</label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required
                               value="{{ old('name') }}"
                               placeholder="e.g. John Doe"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-300">Email Address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               placeholder="user@example.com"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-300">Phone</label>
                        <div class="mt-1">
                            <input id="phone" name="phone" type="text"
                                   value="{{ old('phone') }}"
                                   placeholder="+62 812..."
                                   class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                        </div>
                    </div>
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-slate-300">Company</label>
                        <div class="mt-1">
                            <input id="company_name" name="company_name" type="text"
                                   value="{{ old('company_name') }}"
                                   placeholder="Acme Logistics"
                                   class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               placeholder="Minimum 8 characters"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm Password</label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               placeholder="Repeat password"
                               class="appearance-none block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl shadow-inner placeholder-slate-500 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-600/30 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Complete Registration
                    </button>
                </div>
            </form>

            <div class="mt-5 text-center text-xs text-slate-400">
                Already registered?
                <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300">
                    Sign in here
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
