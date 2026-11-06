@extends('layouts.app')

@section('title', 'Operations Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-900/60 via-slate-900 to-slate-950 border border-indigo-500/20 p-6 sm:p-8">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                        @if(Auth::user()->isAdmin()) bg-rose-500/20 text-rose-300 border border-rose-500/30
                        @elseif(Auth::user()->isOperator()) bg-amber-500/20 text-amber-300 border border-amber-500/30
                        @else bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                        @endif">
                        Role: {{ ucfirst(Auth::user()->role) }}
                    </span>
                    <span class="text-xs text-slate-400">Authenticated via Sanctum/Session</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-sm text-slate-300">
                    CargoTrace Visibility Portal &mdash; {{ Auth::user()->company_name ?: 'Global Supply Chain Network' }}
                </p>
            </div>

            <!-- Quick Action / API Badge -->
            <div class="flex items-center gap-3">
                <a href="#quick-track" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Track Shipment
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stat Cards Placeholder (Scaffold for P9-P13) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Active Shipments</p>
            <p class="mt-2 text-2xl font-black text-white">0</p>
            <p class="text-xs text-indigo-400 mt-1">Pending Milestone P9</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">In Transit Hubs</p>
            <p class="mt-2 text-2xl font-black text-white">0</p>
            <p class="text-xs text-emerald-400 mt-1">Real-time status tracking</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Delay Exceptions</p>
            <p class="mt-2 text-2xl font-black text-white">0</p>
            <p class="text-xs text-amber-400 mt-1">Deterministic ETA rules</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">Delivered (30d)</p>
            <p class="mt-2 text-2xl font-black text-white">0</p>
            <p class="text-xs text-sky-400 mt-1">100% On-time target</p>
        </div>
    </div>

    <!-- API & System Status Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile & Token Info -->
        <div class="lg:col-span-1 p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Security & Session Details
            </h2>
            <div class="space-y-2 text-xs text-slate-300">
                <div class="flex justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-500">Email:</span>
                    <span class="font-mono text-slate-200">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-500">Assigned Role:</span>
                    <span class="font-semibold text-indigo-400 uppercase">{{ Auth::user()->role }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-500">Phone:</span>
                    <span>{{ Auth::user()->phone ?: 'Not configured' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-800">
                    <span class="text-slate-500">Company:</span>
                    <span>{{ Auth::user()->company_name ?: 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-slate-500">API Endpoint:</span>
                    <span class="font-mono text-emerald-400">/api/v1/auth/*</span>
                </div>
            </div>
        </div>

        <!-- Milestone Status Card -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
            <h2 class="text-base font-bold text-white flex items-center justify-between">
                <span>Milestone Roadmap Progress</span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">P8 Completed</span>
            </h2>
            <div class="space-y-3">
                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Milestone P8: Setup, ERD, Foundation & Authentication
                        </p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Laravel 12 setup, RBAC User schema, Sanctum API auth, Session auth, Vite/Tailwind build.</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-400">Ready</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-900/30 border border-slate-800/60 flex items-center justify-between opacity-70">
                    <div>
                        <p class="text-xs font-bold text-slate-300">Milestone P9: Core Entities & Roles</p>
                        <p class="text-[11px] text-slate-500 mt-0.5">Shipments, Carriers, Locations migrations, models, seeders, and RBAC policies.</p>
                    </div>
                    <span class="text-xs font-mono text-slate-500">Next</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
