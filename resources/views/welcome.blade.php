@extends('layouts.portal')

@section('title', 'Zannora | Home')
@section('active', 'home')

@section('content')
    @php
        $pendingBooking = $recentBookings->firstWhere('status', 'pending');
        $firstBooking = $recentBookings->first();
        $firstDetail = $firstBooking?->details?->first();
        $ticketLink = $firstDetail?->ticket;
    @endphp

    <section class="grid items-start gap-10 pb-10 pt-3 lg:grid-cols-[1.12fr_0.88fr]">
        <div class="pt-2">
            <p class="text-sm uppercase tracking-[0.35em] text-white/95">Trusted Airline Experience</p>
            <h1 class="mt-5 max-w-3xl font-heading text-5xl font-bold leading-[1.06] text-white sm:text-6xl">
                Find Your Next Flight with Zannora
            </h1>
            <p class="mt-7 max-w-2xl text-2xl text-white/90 sm:text-[1.8rem]">
                Book flights easily, compare routes in seconds, and travel confidently with professional airline service.
            </p>

            <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('flights.index') }}" class="quick-link-card">
                    <span class="quick-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" />
                            <path d="M20 20l-3.5-3.5" />
                        </svg>
                    </span>
                    <span class="quick-link-title">Search Flights</span>
                </a>
                <a href="{{ auth()->check() ? route('my-bookings.index') : route('login') }}" class="quick-link-card">
                    <span class="quick-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 3h8l4 4v14H4V3h4z" />
                            <path d="M8 11h8M8 15h8" />
                        </svg>
                    </span>
                    <span class="quick-link-title">My Bookings</span>
                </a>
                <a href="{{ auth()->check() ? route('passengers.index') : route('login') }}" class="quick-link-card">
                    <span class="quick-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="8" r="3" />
                            <path d="M4 19a5 5 0 0 1 10 0" />
                            <circle cx="17" cy="9" r="2" />
                            <path d="M14 19a4 4 0 0 1 6 0" />
                        </svg>
                    </span>
                    <span class="quick-link-title">Passengers</span>
                </a>
                <a href="{{ auth()->check() && $pendingBooking ? route('payments.create', ['booking' => $pendingBooking->id]) : route('login') }}" class="quick-link-card">
                    <span class="quick-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="6" width="18" height="12" rx="2" />
                            <path d="M3 10h18M7 14h3" />
                        </svg>
                    </span>
                    <span class="quick-link-title">Payments</span>
                </a>
                <a href="{{ auth()->check() && $ticketLink ? route('tickets.show', $ticketLink) : route('login') }}" class="quick-link-card">
                    <span class="quick-link-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4V9z" />
                            <path d="M9 7v10" />
                        </svg>
                    </span>
                    <span class="quick-link-title">My Tickets</span>
                </a>
            </div>
        </div>

        <div class="relative">
            <img src="{{ asset('images/airplane-hero.svg') }}" alt="Airplane" class="pointer-events-none absolute top-10 right-0 z-0 hidden w-[300px] animate-plane-float opacity-75 xl:block">
            <div class="relative z-10 portal-card animate-fade-up">
                <h2 class="font-heading text-[2rem] font-bold text-[#0f3f78]">Search Flights</h2>
                <form action="{{ route('flights.index') }}" method="GET" class="mt-6 space-y-4">
                    <div>
                        <label for="from" class="portal-label">From</label>
                        <select id="from" name="from" class="portal-select">
                            <option value="">Select departure</option>
                            @foreach ($airports as $airport)
                                <option value="{{ $airport->id }}" @selected(($filters['departure_airport_id'] ?? null) == $airport->id)>
                                    {{ $airport->city }} ({{ $airport->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="to" class="portal-label">To</label>
                        <select id="to" name="to" class="portal-select">
                            <option value="">Select destination</option>
                            @foreach ($airports as $airport)
                                <option value="{{ $airport->id }}" @selected(($filters['arrival_airport_id'] ?? null) == $airport->id)>
                                    {{ $airport->city }} ({{ $airport->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="date" class="portal-label">Departure Date</label>
                            <input id="date" name="date" type="date" value="{{ $filters['departure_date'] ?? '' }}" class="portal-input">
                        </div>
                        <div>
                            <label for="class" class="portal-label">Class</label>
                            <select id="class" name="class" class="portal-select">
                                <option value="">Any class</option>
                                <option value="economy" @selected(($filters['class'] ?? '') === 'economy')>Economy</option>
                                <option value="business" @selected(($filters['class'] ?? '') === 'business')>Business</option>
                                <option value="first" @selected(($filters['class'] ?? '') === 'first')>First</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="portal-btn-gold w-full">Search Flights</button>
                </form>
            </div>
        </div>
    </section>

    <section class="pt-6">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-white/85">Popular Routes</p>
                <h2 class="mt-2 font-heading text-4xl font-bold text-white">Featured Flights</h2>
            </div>
            <a href="{{ route('flights.index') }}" class="portal-btn-blue">View All Flights</a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($featuredFlights as $flight)
                @php
                    $availableSeats = (int) ($seatAvailability[$flight->id] ?? 0);
                @endphp
                <article class="portal-card transition hover:brightness-[1.02]">
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
                    <h3 class="mt-1 text-2xl font-bold text-slate-800">
                        {{ $flight->departureAirport->code }} → {{ $flight->arrivalAirport->code }}
                    </h3>
                    <p class="mt-1 text-slate-600">{{ $flight->departureAirport->city }} to {{ $flight->arrivalAirport->city }}</p>
                    <div class="mt-3 text-sm text-slate-600">
                        <p>{{ $flight->departure_time->format('d M Y H:i') }} - {{ $flight->arrival_time->format('d M Y H:i') }}</p>
                        <p class="mt-1">{{ $availableSeats }} seats left</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</p>
                        <a href="{{ route('flights.show', $flight) }}" class="portal-btn-blue px-4 py-2">View / Book</a>
                    </div>
                </article>
            @empty
                <div class="portal-card col-span-full text-center text-slate-600">
                    No featured flights available at the moment.
                </div>
            @endforelse
        </div>
    </section>

    <section id="about" class="mt-14">
        <div class="flex items-end justify-between gap-4">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-white/85">Trusted Partners</p>
                <h2 class="mt-2 font-heading text-4xl font-bold text-white">Airlines</h2>
            </div>
        </div>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($airlines as $airline)
                <div class="portal-card flex items-center gap-3 !p-4">
                    <span class="grid h-11 w-11 place-content-center rounded-xl bg-[#0f3f78] text-xs font-bold text-white">
                        {{ strtoupper(substr($airline->code, 0, 3)) }}
                    </span>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $airline->name }}</p>
                        <p class="text-sm text-slate-500">{{ strtoupper($airline->code) }}</p>
                    </div>
                </div>
            @empty
                <div class="portal-card col-span-full text-center text-slate-600">Airline data is not available yet.</div>
            @endforelse
        </div>
    </section>

    @auth
        @if (auth()->user()->isCustomer())
            <section class="mt-14 grid gap-6 lg:grid-cols-2">
                <div class="portal-card">
                    <h2 class="font-heading text-3xl font-bold text-[#0f3f78]">Recent Booking</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentBookings as $booking)
                            <a href="{{ route('my-bookings.show', $booking) }}" class="portal-card-soft block transition hover:bg-white">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p>
                                    @if ($booking->status === 'pending')
                                        <span class="portal-status-pending">Pending</span>
                                    @elseif ($booking->status === 'confirmed')
                                        <span class="portal-status-confirmed">Confirmed</span>
                                    @elseif ($booking->status === 'cancelled')
                                        <span class="portal-status-cancelled">Cancelled</span>
                                    @else
                                        <span class="portal-status-default">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-sm text-slate-600">
                                    {{ $booking->flight->departureAirport->code }} → {{ $booking->flight->arrivalAirport->code }}
                                </p>
                            </a>
                        @empty
                            <p class="portal-card-soft text-center text-slate-500">No booking yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="portal-card">
                    <h2 class="font-heading text-3xl font-bold text-[#0f3f78]">Why Choose Zannora</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div class="portal-card-soft">
                            <p class="font-semibold text-slate-800">Fast Booking</p>
                            <p class="text-sm text-slate-600">Complete booking in only a few steps.</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="font-semibold text-slate-800">Secure Payment</p>
                            <p class="text-sm text-slate-600">Protected payment process and clear status.</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="font-semibold text-slate-800">Best Price</p>
                            <p class="text-sm text-slate-600">Competitive fare for every destination.</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="font-semibold text-slate-800">Trusted Airlines</p>
                            <p class="text-sm text-slate-600">Partnered with professional airline brands.</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endauth
@endsection

