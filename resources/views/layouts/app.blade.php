<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false, districtOpen: {{ request()->routeIs('kecamatan.activities.*') || request()->routeIs('kecamatan.inventaris.*') || request()->routeIs('kecamatan.desa-activities.*') ? 'true' : 'false' }} }" x-init="sidebarCollapsed = localStorage.getItem('sidebar-collapsed') === '1'">
        @php
            $activeRoles = auth()->user()?->getRoleNames()->implode(', ') ?: '-';
            $primaryHref = auth()->user()?->hasRole('super-admin') ? route('super-admin.users.index') : route('dashboard');
            $activeLinkClass = 'bg-emerald-600 text-white shadow-sm';
            $idleLinkClass = 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700';
        @endphp

        <div class="min-h-screen bg-gray-50 text-slate-800 dark:bg-slate-900 dark:text-slate-100">
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden"
            ></div>

            <header class="fixed inset-x-0 top-0 z-40 h-14 border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-700 dark:bg-slate-800/95">
                <div class="h-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="inline-flex items-center justify-center rounded-md p-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden">
                            <span class="sr-only">Toggle sidebar</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <button @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebar-collapsed', sidebarCollapsed ? '1' : '0')" class="hidden lg:inline-flex items-center gap-2 rounded-md px-2.5 py-2 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">
                            <span class="sr-only">Collapse sidebar</span>
                            <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="text-xs font-medium" x-text="sidebarCollapsed ? 'Expand' : 'Minimize'"></span>
                        </button>
                        <a href="{{ $primaryHref }}" class="text-sm font-semibold tracking-wide uppercase text-slate-700 dark:text-slate-100 truncate">
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-xs text-slate-500 dark:text-slate-300">{{ $activeRoles }}</span>
                        <span class="text-sm text-slate-700 dark:text-slate-200">{{ auth()->user()->name }}</span>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-rose-600 hover:text-rose-700 dark:text-rose-400">Log Out</button>
                        </form>
                    </div>
                </div>
            </header>

            <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-20' : 'lg:w-64']" class="fixed inset-y-0 left-0 z-40 w-72 transform border-r border-slate-200 bg-white transition-all duration-200 ease-in-out dark:border-slate-700 dark:bg-slate-800 lg:translate-x-0">
                <div class="h-full flex flex-col">
                    <div class="h-14 px-4 flex items-center justify-between border-b border-slate-200 dark:border-slate-700">
                        <a href="{{ $primaryHref }}" :class="sidebarCollapsed ? 'justify-center w-full' : ''" class="flex items-center gap-2 min-w-0">
                            <x-application-logo class="h-7 w-7 fill-current text-emerald-600" />
                            <span x-show="!sidebarCollapsed" class="text-sm font-semibold text-slate-700 dark:text-slate-100 truncate">{{ config('app.name', 'Laravel') }}</span>
                        </a>
                        <button @click="sidebarOpen = false" class="rounded-md p-1 text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 lg:hidden">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="!sidebarCollapsed" class="mx-4 mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500 dark:text-slate-400">User</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-slate-100">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        <p class="mt-2 text-xs text-emerald-700 dark:text-emerald-400">{{ $activeRoles }}</p>
                    </div>

                    <nav class="flex-1 overflow-y-auto p-4 space-y-5">
                        <div class="space-y-1">
                            <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Main</p>
                            @unlessrole('super-admin')
                            <a href="{{ route('dashboard') }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm {{ request()->routeIs('dashboard') ? $activeLinkClass : $idleLinkClass }}" title="Dashboard">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l9-8 9 8M5 10v10h14V10" />
                                </svg>
                                <span x-show="!sidebarCollapsed">Dashboard</span>
                            </a>
                            @endunlessrole

                            @role('super-admin')
                            <a href="{{ route('super-admin.users.index') }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm {{ request()->routeIs('super-admin.users.*') ? $activeLinkClass : $idleLinkClass }}" title="Management User">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-3-3H10a3 3 0 00-3 3v5m10 0H7m8-12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed">Management User</span>
                            </a>
                            @endrole
                        </div>

                        <div class="space-y-1">
                            <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Activity</p>
                            @role('admin-desa')
                            <a href="{{ route('desa.activities.index') }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm {{ request()->routeIs('desa.activities.*') ? $activeLinkClass : $idleLinkClass }}" title="Activities Desa">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-4m5 8H4a1 1 0 01-1-1V4a1 1 0 011-1h16a1 1 0 011 1v16a1 1 0 01-1 1z" />
                                </svg>
                                <span x-show="!sidebarCollapsed">Activities Desa</span>
                            </a>
                            <a href="{{ route('desa.inventaris.index') }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm {{ request()->routeIs('desa.inventaris.*') ? $activeLinkClass : $idleLinkClass }}" title="Inventaris Desa">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-4M4 7h10M4 7v10a2 2 0 002 2h8m-2-4h4m-2-2v4" />
                                </svg>
                                <span x-show="!sidebarCollapsed">Inventaris Desa</span>
                            </a>
                            @endrole

                            @role('admin-kecamatan')
                            <button @click="sidebarCollapsed ? window.location.href='{{ route('kecamatan.activities.index') }}' : districtOpen = !districtOpen" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'" class="w-full flex items-center rounded-md px-3 py-2 text-sm {{ request()->routeIs('kecamatan.activities.*') || request()->routeIs('kecamatan.inventaris.*') || request()->routeIs('kecamatan.desa-activities.*') ? $activeLinkClass : $idleLinkClass }}" title="Kecamatan">
                                <span class="flex items-center gap-3">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-18 0H1m6-8h10M9 9h6" />
                                    </svg>
                                    <span x-show="!sidebarCollapsed">Kecamatan</span>
                                </span>
                                <svg x-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="{ 'rotate-180': districtOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="districtOpen && !sidebarCollapsed" x-transition class="space-y-1 pl-4">
                                <a href="{{ route('kecamatan.activities.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->routeIs('kecamatan.activities.*') ? $activeLinkClass : $idleLinkClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Activities Kecamatan
                                </a>
                                <a href="{{ route('kecamatan.inventaris.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->routeIs('kecamatan.inventaris.*') ? $activeLinkClass : $idleLinkClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Inventaris Kecamatan
                                </a>
                                <a href="{{ route('kecamatan.desa-activities.index') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm {{ request()->routeIs('kecamatan.desa-activities.*') ? $activeLinkClass : $idleLinkClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    Activities Desa
                                </a>
                            </div>
                            @endrole
                        </div>

                        <div class="space-y-1">
                            <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Account</p>
                            <a href="{{ route('profile.edit') }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 rounded-md px-3 py-2 text-sm {{ request()->routeIs('profile.*') ? $activeLinkClass : $idleLinkClass }}" title="Profile">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 17.8M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span x-show="!sidebarCollapsed">Profile</span>
                            </a>
                        </div>
                    </nav>

                    <div class="p-4 border-t border-slate-200 dark:border-slate-700">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" :class="sidebarCollapsed ? 'px-0' : 'px-3'" class="w-full rounded-md border border-rose-200 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50 dark:border-rose-900/60 dark:text-rose-400 dark:hover:bg-rose-900/20">
                                <span x-show="!sidebarCollapsed">Log Out</span>
                                <svg x-show="sidebarCollapsed" class="mx-auto h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'" class="pt-14 transition-all duration-200">
                @isset($header)
                    <div class="border-b border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
                        <div class="px-4 sm:px-6 lg:px-8 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <main class="px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
