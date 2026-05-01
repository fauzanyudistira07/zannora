<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Zannora Airline')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portal-shell font-sans antialiased text-slate-700">
        @php($active = trim($__env->yieldContent('active')))
        @php($currentUser = auth()->user())
        @php($unreadPortalNotifications = $currentUser && $currentUser->isCustomer() ? $currentUser->unreadNotifications()->count() : 0)

        <header class="portal-container relative z-40 py-6 portal-print-hide" x-data="{ mobileOpen: false }">
            <div class="portal-topbar">
                <a href="{{ route('home') }}" class="portal-brand">
                    <svg viewBox="0 0 72 72" class="h-10 w-10 shrink-0" aria-hidden="true">
                        <path d="M6 42c11-8 22-12 37-13-5 4-8 8-13 14 13-4 23-11 33-24-6 2-11 3-19 5 4-6 7-10 13-16-12 3-21 8-30 16-8-1-13-1-21-2 5 7 8 12 10 20z" fill="#f4bf4a"/>
                        <path d="M26 46c16-8 27-19 38-36-3 12-6 21-12 31 6-1 10-2 16-4-7 9-14 14-24 18-6-3-11-5-18-9z" fill="#eef6ff"/>
                    </svg>
                    <span>
                        <strong class="portal-brand-title">ZANNORA</strong>
                        <small class="portal-brand-subtitle">Fly Beyond Horizons</small>
                    </span>
                </a>

                <nav class="portal-nav hidden lg:flex">
                    <a href="{{ route('home') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'home'])>Home</a>
                    <a href="{{ route('flights.index') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'flights'])>Flights</a>
                    @auth
                        @if ($currentUser->isCustomer())
                            <a href="{{ route('my-bookings.index') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'bookings'])>My Bookings</a>
                            <a href="{{ route('my-bookings.change-requests.index') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'change-requests'])>Service Requests</a>
                            <a href="{{ route('passengers.index') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'passengers'])>Passengers</a>
                            <a href="{{ route('profile.edit') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'profile'])>Profile</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="portal-nav-link">{{ $currentUser->roleLabel() }}</a>
                        @endif
                    @endauth
                    <a href="{{ route('about') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'about'])>About</a>
                    <a href="{{ route('contact') }}" @class(['portal-nav-link', 'portal-nav-link-active' => $active === 'contact'])>Contact</a>
                </nav>

                <div class="hidden items-center gap-2 sm:flex">
                    @guest
                        <a href="{{ route('login') }}" class="landing-nav-outline">Login</a>
                        <a href="{{ route('register') }}" class="landing-nav-solid">Register</a>
                    @else
                        @if ($currentUser->isCustomer())
                            <a href="{{ route('notifications.index') }}" class="portal-action-btn">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V10a6 6 0 1 0-12 0v4.2a2 2 0 0 1-.6 1.4L4 17h5" />
                                    <path d="M9 17a3 3 0 0 0 6 0" />
                                </svg>
                                <span>Notifications</span>
                                @if ($unreadPortalNotifications > 0)
                                    <span class="rounded-full bg-amber-300 px-2 py-0.5 text-[11px] font-bold text-slate-900">{{ $unreadPortalNotifications }}</span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="portal-action-btn">
                                <span>{{ $currentUser->roleLabel() }}</span>
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="portal-action-btn portal-action-btn-ghost">
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <path d="M16 17l5-5-5-5" />
                                    <path d="M21 12H9" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    @endguest
                </div>

                <button
                    type="button"
                    class="portal-mobile-toggle lg:hidden"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen.toString()"
                    aria-label="Toggle navigation menu"
                >
                    <svg x-show="!mobileOpen" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    <svg x-show="mobileOpen" x-cloak viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M6 6l12 12M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div class="portal-mobile-panel lg:hidden" x-show="mobileOpen" x-cloak x-transition.opacity.scale.origin.top>
                <nav class="grid gap-1">
                    <a href="{{ route('home') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'home'])>Home</a>
                    <a href="{{ route('flights.index') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'flights'])>Flights</a>
                    @auth
                        @if ($currentUser->isCustomer())
                            <a href="{{ route('my-bookings.index') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'bookings'])>My Bookings</a>
                            <a href="{{ route('my-bookings.change-requests.index') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'change-requests'])>Service Requests</a>
                            <a href="{{ route('passengers.index') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'passengers'])>Passengers</a>
                            <a href="{{ route('profile.edit') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'profile'])>Profile</a>
                            <a href="{{ route('notifications.index') }}" class="portal-mobile-link">Notifications @if ($unreadPortalNotifications > 0)<span class="ml-1 rounded-full bg-amber-300 px-2 py-0.5 text-[11px] font-bold text-slate-900">{{ $unreadPortalNotifications }}</span>@endif</a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="portal-mobile-link">{{ $currentUser->roleLabel() }}</a>
                        @endif
                    @endauth
                    <a href="{{ route('about') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'about'])>About</a>
                    <a href="{{ route('contact') }}" @class(['portal-mobile-link', 'portal-mobile-link-active' => $active === 'contact'])>Contact</a>
                </nav>

                <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-white/20 pt-3">
                    @guest
                        <a href="{{ route('login') }}" class="landing-nav-outline">Login</a>
                        <a href="{{ route('register') }}" class="landing-nav-solid">Register</a>
                    @else
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="portal-action-btn portal-action-btn-ghost">Logout</button>
                        </form>
                    @endguest
                </div>
            </div>
        </header>

        <main class="portal-container pb-16">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="relative z-10 mt-10 border-t border-white/25 bg-black/15 backdrop-blur-sm portal-print-hide">
            <div class="portal-container flex flex-col gap-3 py-6 text-sm text-white/90 md:flex-row md:items-center md:justify-between">
                <p>&copy; {{ now()->year }} Zannora Airline. All rights reserved.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('about') }}" class="auth-link-light">About</a>
                    <a href="{{ route('contact') }}" class="auth-link-light">Contact</a>
                    <span class="auth-link-light opacity-70">Privacy Policy</span>
                    <span class="auth-link-light opacity-70">Terms</span>
                </div>
            </div>
        </footer>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.admin-table-wrap, .portal-table-wrap').forEach(function (wrap) {
                    wrap.style.display = 'block';
                    wrap.style.width = '100%';
                    wrap.style.maxWidth = '100%';
                    wrap.style.overflowX = 'auto';
                    wrap.style.overflowY = 'hidden';
                    wrap.style.webkitOverflowScrolling = 'touch';
                    wrap.style.touchAction = 'pan-x';
                });

                document.querySelectorAll('.admin-table, .portal-table').forEach(function (table) {
                    table.style.width = 'max-content';
                    table.style.minWidth = '860px';
                    table.style.tableLayout = 'auto';
                });
            });
        </script>
    </body>
</html>
