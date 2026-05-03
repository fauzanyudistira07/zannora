<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Admin Panel | Zannora')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portal-shell font-sans antialiased text-slate-700">
        @php
            $user = auth()->user();
            $menuGroups = [
                'Monitoring' => [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                ],
                'Operational' => [
                    ['label' => 'Passengers', 'route' => 'admin.passengers.index'],
                    ['label' => 'Flights', 'route' => 'admin.flights.index'],
                    ['label' => 'Bookings', 'route' => 'admin.bookings.index'],
                    ['label' => 'Payments', 'route' => 'admin.payments.index'],
                    ['label' => 'Tickets', 'route' => 'admin.tickets.index'],
                ],
            ];

            if ($user?->canViewReports()) {
                $menuGroups['Monitoring'][] = ['label' => 'Reports', 'route' => 'admin.reports.index'];
            }

            if ($user?->canViewUsers()) {
                array_unshift($menuGroups['Operational'], ['label' => 'Users', 'route' => 'admin.users.index']);
            }

            if ($user?->canManageMasterData()) {
                $menuGroups['Master Data'] = [
                    ['label' => 'Airports', 'route' => 'admin.airports.index'],
                    ['label' => 'Airlines', 'route' => 'admin.airlines.index'],
                    ['label' => 'Airplanes', 'route' => 'admin.airplanes.index'],
                    ['label' => 'Seats', 'route' => 'admin.seats.index'],
                ];
            }

            $currentRoute = optional(request()->route())->getName();
        @endphp

        <div class="relative z-10 mx-auto w-full max-w-[1500px] px-6 lg:px-12 2xl:px-16 overflow-x-clip py-6 lg:py-8" x-data="{ sidebarOpen: false }">
            <div
                class="fixed inset-0 z-40 bg-slate-900/45 lg:hidden"
                x-show="sidebarOpen"
                x-cloak
                @click="sidebarOpen = false"
            ></div>

            <div class="admin-shell">
                <aside class="admin-sidebar" :class="{ 'admin-sidebar-open': sidebarOpen }">
                    <a href="{{ route('admin.dashboard') }}" class="portal-brand">
                        <svg viewBox="0 0 72 72" class="h-10 w-10 shrink-0" aria-hidden="true">
                            <path d="M6 42c11-8 22-12 37-13-5 4-8 8-13 14 13-4 23-11 33-24-6 2-11 3-19 5 4-6 7-10 13-16-12 3-21 8-30 16-8-1-13-1-21-2 5 7 8 12 10 20z" fill="#f4bf4a"/>
                            <path d="M26 46c16-8 27-19 38-36-3 12-6 21-12 31 6-1 10-2 16-4-7 9-14 14-24 18-6-3-11-5-18-9z" fill="#eef6ff"/>
                        </svg>
                        <span>
                            <strong class="portal-brand-title">ZANNORA</strong>
                            <small class="portal-brand-subtitle">Admin Panel</small>
                        </span>
                    </a>

                    <nav class="mt-6 space-y-5">
                        @foreach ($menuGroups as $group => $menus)
                            @continue(empty($menus))
                            <div>
                                <p class="admin-sidebar-group">{{ $group }}</p>
                                <div class="mt-2 grid gap-1">
                                    @foreach ($menus as $menu)
                                        <a
                                            href="{{ route($menu['route']) }}"
                                            @click="sidebarOpen = false"
                                            @class([
                                                'admin-sidebar-link',
                                                'admin-sidebar-link-active' => $currentRoute && str_starts_with($currentRoute, explode('.', $menu['route'])[0].'.'.explode('.', $menu['route'])[1]),
                                            ])
                                        >
                                            {{ $menu['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>

                    <div class="mt-4 space-y-2 border-t border-white/20 pt-4">
                        <a href="{{ route('admin.profile.index') }}" @click="sidebarOpen = false" @class(['admin-sidebar-link', 'admin-sidebar-link-active' => str_starts_with((string) $currentRoute, 'admin.profile')])>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-sidebar-link w-full text-left">Logout</button>
                        </form>
                    </div>
                </aside>

                <div class="admin-content">
                    <header class="admin-topbar">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-[#315b8c]">Admin Control</p>
                            <h1 class="font-heading text-2xl font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="rounded-full border border-blue-200 bg-white px-3 py-1 text-sm text-slate-600">
                                {{ auth()->user()->name }}
                            </span>
                            <button class="admin-mobile-toggle lg:hidden" @click="sidebarOpen = !sidebarOpen" type="button">
                                Menu
                            </button>
                        </div>
                    </header>

                    @if (session('status'))
                        <div class="mt-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <p class="font-semibold">Ada kesalahan validasi:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <main class="mt-6">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
