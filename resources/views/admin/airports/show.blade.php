@extends('layouts.admin')

@section('title', 'Airport Detail | Zannora')
@section('page-title', 'Airport Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Code</p><p class="font-semibold text-slate-800">{{ $airport->code }}</p></div>
                <div><p class="text-sm text-slate-500">Name</p><p class="font-semibold text-slate-800">{{ $airport->name }}</p></div>
                <div><p class="text-sm text-slate-500">City</p><p class="font-semibold text-slate-800">{{ $airport->city }}</p></div>
                <div><p class="text-sm text-slate-500">Country</p><p class="font-semibold text-slate-800">{{ $airport->country }}</p></div>
            </div>
        </article>

        <div class="grid gap-4 md:grid-cols-2">
            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Departure Flights ({{ $airport->departure_flights_count }})</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($airport->departureFlights as $flight)
                        <a href="{{ route('admin.flights.show', $flight) }}" class="admin-card-soft block">
                            <p class="font-semibold">{{ $flight->flight_number }}</p>
                            <p class="text-sm text-slate-600">{{ $flight->arrivalAirport?->code }} - {{ $flight->airline?->name }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada departure flight.</p>
                    @endforelse
                </div>
            </article>

            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Arrival Flights ({{ $airport->arrival_flights_count }})</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($airport->arrivalFlights as $flight)
                        <a href="{{ route('admin.flights.show', $flight) }}" class="admin-card-soft block">
                            <p class="font-semibold">{{ $flight->flight_number }}</p>
                            <p class="text-sm text-slate-600">{{ $flight->departureAirport?->code }} - {{ $flight->airline?->name }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada arrival flight.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection
