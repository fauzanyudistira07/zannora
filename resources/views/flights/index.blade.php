@extends('layouts.portal')

@section('title', 'Zannora | Flights')
@section('active', 'flights')

@section('content')
    <section class="grid gap-4 sm:gap-6 lg:grid-cols-[320px_1fr]">
        <aside class="portal-card h-fit">
            <h1 class="font-heading text-2xl font-bold text-[#0f3f78] sm:text-3xl">Search Result</h1>
            <p class="mt-1 text-sm text-slate-600">Filter flights by route, time, and price.</p>

            <form action="{{ route('flights.index') }}" method="GET" class="mt-5 space-y-4">
                <div>
                    <label for="from" class="portal-label">From</label>
                    <select id="from" name="from" class="portal-select">
                        <option value="">All origin</option>
                        @foreach ($airports as $airport)
                            <option value="{{ $airport->id }}" @selected(($filters['from'] ?? null) == $airport->id)>
                                {{ $airport->city }} ({{ $airport->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="to" class="portal-label">To</label>
                    <select id="to" name="to" class="portal-select">
                        <option value="">All destination</option>
                        @foreach ($airports as $airport)
                            <option value="{{ $airport->id }}" @selected(($filters['to'] ?? null) == $airport->id)>
                                {{ $airport->city }} ({{ $airport->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="portal-label">Date</label>
                    <input id="date" type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="portal-input">
                </div>
                <div>
                    <label for="airline_id" class="portal-label">Airline</label>
                    <select id="airline_id" name="airline_id" class="portal-select">
                        <option value="">All airlines</option>
                        @foreach ($airlines as $airline)
                            <option value="{{ $airline->id }}" @selected(($filters['airline_id'] ?? null) == $airline->id)>{{ $airline->name }}</option>
                        @endforeach
                    </select>
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
                <div>
                    <label for="max_price" class="portal-label">Max Price</label>
                    <input id="max_price" type="number" min="0" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="portal-input" placeholder="Example: 1500000">
                </div>
                <div>
                    <label for="time" class="portal-label">Departure Time</label>
                    <select id="time" name="time" class="portal-select">
                        <option value="">Any time</option>
                        <option value="morning" @selected(($filters['time'] ?? '') === 'morning')>Morning (05:00 - 11:59)</option>
                        <option value="afternoon" @selected(($filters['time'] ?? '') === 'afternoon')>Afternoon (12:00 - 16:59)</option>
                        <option value="evening" @selected(($filters['time'] ?? '') === 'evening')>Evening (17:00 - 20:59)</option>
                        <option value="night" @selected(($filters['time'] ?? '') === 'night')>Night (21:00 - 04:59)</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="portal-label">Sort</label>
                    <select id="sort" name="sort" class="portal-select">
                        <option value="time_asc" @selected(($filters['sort'] ?? 'time_asc') === 'time_asc')>Departure Time ↑</option>
                        <option value="time_desc" @selected(($filters['sort'] ?? '') === 'time_desc')>Departure Time ↓</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price ↑</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price ↓</option>
                    </select>
                </div>

                <button type="submit" class="portal-btn-gold w-full">Apply Filter</button>
            </form>
        </aside>

        <div>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h2 class="font-heading text-3xl font-bold text-white sm:text-4xl">Available Flights</h2>
                <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-medium text-white/95">
                    {{ $flights->total() }} results
                </span>
            </div>

            <div class="space-y-4">
                @forelse ($flights as $flight)
                    @php
                        $availableSeats = (int) ($seatAvailability[$flight->id] ?? 0);
                        $selectedCabinClass = $filters['class'] ?? null;
                        $displayPrice = $selectedCabinClass
                            ? \App\Support\CabinClass::price((float) $flight->price, $selectedCabinClass)
                            : (float) $flight->price;
                    @endphp
                    <article class="portal-card transition hover:brightness-[1.02]">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
                                <h3 class="mt-1 text-2xl font-semibold text-slate-800">{{ $flight->flight_number }}</h3>
                                <p class="mt-2 text-slate-600">
                                    {{ $flight->departureAirport->city }} ({{ $flight->departureAirport->code }}) →
                                    {{ $flight->arrivalAirport->city }} ({{ $flight->arrivalAirport->code }})
                                </p>
                            </div>
                            <div class="text-sm text-slate-600">
                                <p>Departure: {{ $flight->departure_time->format('d M Y H:i') }}</p>
                                <p>Arrival: {{ $flight->arrival_time->format('d M Y H:i') }}</p>
                                <p class="mt-1">{{ $availableSeats }} seats left</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-500">{{ $selectedCabinClass ? \App\Support\CabinClass::label($selectedCabinClass) : 'Starting from' }}</p>
                                <p class="text-3xl font-bold text-[#0f3f78]">Rp{{ number_format($displayPrice, 0, ',', '.') }}</p>
                                <a href="{{ route('flights.show', ['flight' => $flight, 'class' => $selectedCabinClass]) }}" class="portal-btn-blue mt-2 px-4 py-2">
                                    Select
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="portal-card text-center text-slate-600">No flights match your filters.</div>
                @endforelse
            </div>

            @if ($flights->hasPages())
                <div class="mt-6 rounded-2xl bg-white/80 p-3">
                    {{ $flights->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection
