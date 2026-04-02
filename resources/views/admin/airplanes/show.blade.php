@extends('layouts.admin')

@section('title', 'Airplane Detail | Zannora')
@section('page-title', 'Airplane Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Airline</p><p class="font-semibold text-slate-800">{{ $airplane->airline?->name }}</p></div>
                <div><p class="text-sm text-slate-500">Model</p><p class="font-semibold text-slate-800">{{ $airplane->model }}</p></div>
                <div><p class="text-sm text-slate-500">Registration</p><p class="font-semibold text-slate-800">{{ $airplane->registration_number }}</p></div>
                <div><p class="text-sm text-slate-500">Capacity</p><p class="font-semibold text-slate-800">{{ $airplane->capacity }}</p></div>
            </div>
            @if ($airplane->description)
                <div class="mt-4 admin-card-soft">
                    <p class="text-sm text-slate-500">Description</p>
                    <p class="text-slate-700">{{ $airplane->description }}</p>
                </div>
            @endif
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('admin.airplanes.generate-seats', $airplane) }}" class="flex flex-wrap items-center gap-2">
                    @csrf
                    <select name="class" class="admin-field">
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                        <option value="first">First</option>
                    </select>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="reset" value="1" class="rounded border-slate-300 text-[#0f3f78]"> Reset existing seats
                    </label>
                    <button class="admin-btn-primary" type="submit">Generate Seats</button>
                </form>
            </div>
        </article>

        <div class="grid gap-6 xl:grid-cols-2">
            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Seats ({{ $airplane->seats_count }})</h2>
                <div class="mt-4 grid grid-cols-4 gap-2 sm:grid-cols-6">
                    @forelse ($airplane->seats as $seat)
                        <span class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-center text-xs font-semibold text-slate-700">{{ $seat->seat_number }}</span>
                    @empty
                        <p class="col-span-full text-sm text-slate-500">Belum ada seat.</p>
                    @endforelse
                </div>
            </article>

            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Related Flights ({{ $airplane->flights_count }})</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($airplane->flights as $flight)
                        <a href="{{ route('admin.flights.show', $flight) }}" class="admin-card-soft block">
                            <p class="font-semibold">{{ $flight->flight_number }}</p>
                            <p class="text-sm text-slate-600">{{ $flight->departureAirport?->code }} -> {{ $flight->arrivalAirport?->code }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada flight terkait.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection
