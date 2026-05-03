@extends('layouts.portal')

@section('title', 'Zannora | Flight Detail')
@section('active', 'flights')

@section('content')
    @php
        $durationMinutes = $flight->departure_time->diffInMinutes($flight->arrival_time);
        $durationHours = intdiv($durationMinutes, 60);
        $durationRemain = $durationMinutes % 60;
        $allSeats = $flight->airplane->seats->sortBy('seat_number');
    @endphp

    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <article class="portal-card">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
                        <h1 class="mt-1 font-heading text-4xl font-bold text-slate-800">{{ $flight->flight_number }}</h1>
                    </div>
                    @if ($flight->status === 'scheduled')
                        <span class="portal-status-confirmed">On Time</span>
                    @elseif ($flight->status === 'delayed')
                        <span class="portal-status-pending">Delayed</span>
                    @elseif ($flight->status === 'cancelled')
                        <span class="portal-status-cancelled">Cancelled</span>
                    @else
                        <span class="portal-status-default">{{ ucfirst($flight->status) }}</span>
                    @endif
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Route</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">
                            {{ $flight->departureAirport->code }} → {{ $flight->arrivalAirport->code }}
                        </p>
                        <p class="text-sm text-slate-600">{{ $flight->departureAirport->city }} to {{ $flight->arrivalAirport->city }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Departure</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $flight->departure_time->format('d M Y H:i') }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Arrival</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $flight->arrival_time->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Duration</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $durationHours }}h {{ $durationRemain }}m</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Aircraft</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $flight->airplane->model }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Price</p>
                        <p class="mt-1 text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </article>

            <article class="portal-card">
                <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Seat Availability</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Green seats are available. Red seats are already booked.
                </p>
                <div class="mt-5 grid grid-cols-4 gap-2 sm:grid-cols-6 lg:grid-cols-8">
                    @forelse ($allSeats as $seat)
                        @php($isAvailable = $availableSeats->contains('id', $seat->id))
                        <span @class([
                            'rounded-lg border px-2 py-2 text-center text-xs font-semibold',
                            'border-emerald-300 bg-emerald-100 text-emerald-700' => $isAvailable,
                            'border-red-300 bg-red-100 text-red-700' => ! $isAvailable,
                        ])>
                            {{ $seat->seat_number }}
                        </span>
                    @empty
                        <p class="col-span-full text-sm text-slate-500">No seat data available.</p>
                    @endforelse
                </div>
            </article>
        </div>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-3xl font-bold text-[#0f3f78]">Ready to Book?</h2>
            <p class="mt-2 text-slate-600">
                Continue to booking flow: choose passenger, select seat, and confirm your flight.
            </p>
            <div class="mt-5 space-y-3">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Available seats</p>
                    <p class="text-xl font-semibold text-slate-800">{{ $availableSeats->count() }} seats</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Status</p>
                    <p class="text-xl font-semibold text-slate-800">{{ ucfirst($flight->status) }}</p>
                </div>
            </div>

            @auth
                @if (auth()->user()->isCustomer())
                    <a href="{{ route('booking.create', ['flight' => $flight->id]) }}" class="portal-btn-gold mt-6 w-full">
                        Book Now
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="portal-btn-gold mt-6 w-full">
                    Login to Book
                </a>
            @endauth
        </aside>
    </section>
@endsection
