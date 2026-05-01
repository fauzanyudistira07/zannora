@extends('layouts.admin')

@section('title', 'Airline Detail | Zannora')
@section('page-title', 'Airline Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <p class="text-sm text-slate-500">Logo</p>
                    @if ($airline->logo)
                        <img src="{{ str_starts_with($airline->logo, 'http') ? $airline->logo : asset('storage/'.$airline->logo) }}" alt="{{ $airline->name }}" class="mt-1 h-12 w-12 rounded-full object-cover">
                    @else
                        <p class="font-semibold text-slate-800">-</p>
                    @endif
                </div>
                <div><p class="text-sm text-slate-500">Code</p><p class="font-semibold text-slate-800">{{ $airline->code }}</p></div>
                <div><p class="text-sm text-slate-500">Name</p><p class="font-semibold text-slate-800">{{ $airline->name }}</p></div>
                <div><p class="text-sm text-slate-500">Total Bookings</p><p class="font-semibold text-slate-800">{{ $bookingCount }}</p></div>
            </div>
            @if ($airline->description)
                <div class="mt-4 admin-card-soft">
                    <p class="text-sm text-slate-500">Description</p>
                    <p class="text-slate-700">{{ $airline->description }}</p>
                </div>
            @endif
        </article>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Airplanes ({{ $airline->airplanes_count }})</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($airline->airplanes as $airplane)
                        <a href="{{ route('admin.airplanes.show', $airplane) }}" class="admin-card-soft block">
                            <p class="font-semibold">{{ $airplane->model }}</p>
                            <p class="text-sm text-slate-600">Reg: {{ $airplane->registration_number }} | Capacity: {{ $airplane->capacity }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada airplane.</p>
                    @endforelse
                </div>
            </article>

            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Recent Flights ({{ $airline->flights_count }})</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($airline->flights as $flight)
                        <a href="{{ route('admin.flights.show', $flight) }}" class="admin-card-soft block">
                            <p class="font-semibold">{{ $flight->flight_number }}</p>
                            <p class="text-sm text-slate-600">{{ $flight->departureAirport?->code }} -> {{ $flight->arrivalAirport?->code }}</p>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada flight.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection
