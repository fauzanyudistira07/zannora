@extends('layouts.portal')

@section('title', 'Zannora | About')
@section('active', 'about')

@section('content')
    <section class="space-y-6">
        <article class="portal-card">
            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">About Zannora</p>
            <h1 class="mt-2 font-heading text-3xl font-bold text-slate-800 sm:text-4xl">Professional Web Booking Experience</h1>
            <p class="mt-4 max-w-3xl text-slate-600">
                Zannora dirancang sebagai web pemesanan tiket yang terasa seperti portal maskapai modern:
                pencarian flight yang cepat, seat map kabin yang realistis, pembayaran yang ringkas, dan e-ticket yang siap diunduh.
            </p>
        </article>

        <section class="grid gap-6 lg:grid-cols-3">
            <article class="portal-card">
                <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Coverage</p>
                <p class="mt-3 text-3xl font-bold text-[#0f3f78] sm:text-4xl">{{ $airportCount }}</p>
                <p class="mt-2 text-slate-600">Airports tersedia di jaringan demo domestik.</p>
            </article>
            <article class="portal-card">
                <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Operations</p>
                <p class="mt-3 text-3xl font-bold text-[#0f3f78] sm:text-4xl">{{ $activeFlightCount }}</p>
                <p class="mt-2 text-slate-600">Active flights siap dipesan saat ini.</p>
            </article>
            <article class="portal-card">
                <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Partners</p>
                <p class="mt-3 text-3xl font-bold text-[#0f3f78] sm:text-4xl">{{ $airlines->count() }}</p>
                <p class="mt-2 text-slate-600">Airline partners tampil pada katalog dan rute populer.</p>
            </article>
        </section>

        <article class="portal-card">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-[#315b8c]">Airline Partners</p>
                    <h2 class="mt-2 font-heading text-3xl font-bold text-slate-800">Trusted Airlines</h2>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($airlines as $airline)
                    <div class="portal-card-soft flex items-center gap-3">
                        <span class="grid h-11 w-11 place-content-center rounded-xl bg-[#0f3f78] text-xs font-bold text-white">
                            {{ strtoupper(substr($airline->code, 0, 3)) }}
                        </span>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $airline->name }}</p>
                            <p class="text-sm text-slate-500">{{ strtoupper($airline->code) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endsection
