<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Login - Zannora</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="min-h-screen bg-gradient-to-br from-slate-200 to-slate-300 font-sans antialiased text-slate-700">
        <main class="flex min-h-screen w-full items-stretch justify-center p-0">
            <section class="auth-page-shell">
                <span class="auth-cloud left-[8%] top-[18%] h-16 w-36" style="animation-duration:11s;"></span>
                <span class="auth-cloud right-[18%] top-[62%] h-14 w-32" style="animation-duration:9s;"></span>
                <span class="auth-cloud left-[58%] top-[30%] h-10 w-24" style="animation-duration:8s;"></span>

                <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-[1320px] flex-col px-4 py-5 sm:px-7 lg:px-10">
                    <header class="flex items-center justify-between text-white/95">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <svg viewBox="0 0 72 72" class="h-11 w-11">
                                <path d="M6 42c11-8 22-12 37-13-5 4-8 8-13 14 13-4 23-11 33-24-6 2-11 3-19 5 4-6 7-10 13-16-12 3-21 8-30 16-8-1-13-1-21-2 5 7 8 12 10 20z" fill="#f4bf4a"/>
                                <path d="M26 46c16-8 27-19 38-36-3 12-6 21-12 31 6-1 10-2 16-4-7 9-14 14-24 18-6-3-11-5-18-9z" fill="#eef6ff"/>
                            </svg>
                            <span>
                                <strong class="block font-heading text-4xl font-extrabold leading-none tracking-tight">Zannora</strong>
                                <small class="block text-base text-white/85">Fly Beyond Horizons</small>
                            </span>
                        </a>

                        <a href="{{ route('register') }}" class="rounded-xl border border-cyan-100/45 bg-cyan-300/28 px-5 py-2 font-semibold text-white shadow-md shadow-cyan-900/20 backdrop-blur-sm transition hover:bg-cyan-300/48">Register</a>
                    </header>

                    <div class="pointer-events-none absolute right-[4%] top-[22%] hidden lg:block">
                        <img src="{{ asset('images/airplane-hero.svg') }}" alt="Airplane illustration" class="auth-plane-image" />
                    </div>

                    <div class="flex flex-1 items-center justify-center py-6">
                        <div class="auth-card">
                            <h1 class="text-center font-heading text-[2.65rem] font-extrabold tracking-tight text-[#0f3f78] sm:text-[2.85rem]">
                                Welcome Back
                            </h1>
                            <p class="mt-1 text-center text-[1.35rem] text-slate-600 sm:mt-2">Login to your account</p>

                            @if (session('status'))
                                <div class="mt-5 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4" x-data="{ showPassword: false }">
                                @csrf

                                <label for="email" class="block text-base font-medium text-slate-600">Email</label>
                                <div class="auth-field">
                                    <span class="auth-field-icon">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M3 6.75A2.75 2.75 0 0 1 5.75 4h12.5A2.75 2.75 0 0 1 21 6.75v10.5A2.75 2.75 0 0 1 18.25 20H5.75A2.75 2.75 0 0 1 3 17.25V6.75zm2.54-.5 6.25 5 6.26-5H5.54z"/></svg>
                                    </span>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Email" class="auth-input">
                                    <span class="auth-arrow">&#8250;</span>
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="text-sm" />

                                <label for="password" class="block text-base font-medium text-slate-600">Password</label>
                                <div class="auth-field">
                                    <span class="auth-field-icon">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M17 9h-1V7a4 4 0 1 0-8 0v2H7a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2zm-6 7.73V18h2v-1.27a2 2 0 1 0-2 0zM10 9V7a2 2 0 1 1 4 0v2h-4z"/></svg>
                                    </span>
                                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="Password" class="auth-input">
                                    <button type="button" @click="showPassword = !showPassword" class="auth-arrow rounded-md px-1.5 transition hover:text-[#123e72]" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                                        <svg x-show="!showPassword" viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path d="M12 5c4.8 0 8.9 2.7 11 7-2.1 4.3-6.2 7-11 7s-8.9-2.7-11-7c2.1-4.3 6.2-7 11-7zm0 2C8.5 7 5.4 8.8 3.7 12 5.4 15.2 8.5 17 12 17s6.6-1.8 8.3-5C18.6 8.8 15.5 7 12 7zm0 2.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5z"/>
                                        </svg>
                                        <svg x-show="showPassword" x-cloak viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path d="M2.3 1.3 1 2.6l4.1 4.1C3.3 8 1.9 9.8 1 12c2.1 4.3 6.2 7 11 7 2 0 3.9-.5 5.5-1.3l3.2 3.2 1.3-1.3L2.3 1.3zm9.7 15.7c-3.5 0-6.6-1.8-8.3-5 .7-1.4 1.7-2.6 2.9-3.4l1.8 1.8a3.5 3.5 0 0 0 4.2 4.2l1.5 1.5c-.7.2-1.4.4-2.1.4zm9.3-5c-.9-1.9-2.3-3.6-4.1-4.8l-1.5 1.5a8.8 8.8 0 0 1 2.6 3.3 9.2 9.2 0 0 1-1.7 2.4l1.4 1.4c1.3-1 2.4-2.4 3.3-3.8z"/>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="text-sm" />

                                <div class="flex items-center justify-between gap-3 pt-1 text-base text-[#315b8c]">
                                    <label for="remember_me" class="inline-flex items-center gap-2">
                                        <input id="remember_me" name="remember" type="checkbox" @checked(old('remember')) class="h-4.5 w-4.5 rounded border-slate-300 text-[#c09133] focus:ring-[#c09133]">
                                        <span>Remember Me</span>
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="auth-link-fade font-semibold text-[#0f3f78]/90">Forgot Password?</a>
                                    @endif
                                </div>

                                <button type="submit" class="auth-gold-btn">
                                    Login
                                </button>
                            </form>

                            <p class="mt-6 text-center text-[1.2rem] text-slate-600">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="auth-link-fade font-bold text-[#0f3f78]">Register</a>
                            </p>

                            <div class="mt-4 flex items-center justify-center gap-3 text-sm text-[#0f3f78]/85 sm:text-base">
                                <a href="#" class="auth-link-fade">Privacy Policy</a>
                                <span>|</span>
                                <a href="#" class="auth-link-fade">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
