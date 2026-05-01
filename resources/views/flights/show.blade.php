@extends('layouts.portal')

@section('title', 'Zannora | Flight Detail')
@section('active', 'flights')

@section('content')
    @php
        $durationMinutes = $flight->departure_time->diffInMinutes($flight->arrival_time);
        $durationHours = intdiv($durationMinutes, 60);
        $durationRemain = $durationMinutes % 60;
        $classKeys = $seatMap['class_keys'];
        $initialClass = in_array($selectedClass, $classKeys, true) ? $selectedClass : ($classKeys[0] ?? 'economy');
    @endphp

    <section x-data="{ selectedClass: '{{ $initialClass }}' }" class="grid gap-4 sm:gap-6 xl:grid-cols-[1.08fr_0.92fr]">
        <div class="space-y-4 sm:space-y-6">
            <article class="portal-card">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $flight->airline->name }}</p>
                        <h1 class="portal-fluid-title mt-1 font-heading font-bold text-slate-800">{{ $flight->flight_number }}</h1>
                    </div>
                    @if ($flight->status === 'scheduled')
                        <span class="portal-status-confirmed self-start">On Time</span>
                    @elseif ($flight->status === 'delayed')
                        <span class="portal-status-pending self-start">Delayed</span>
                    @elseif ($flight->status === 'cancelled')
                        <span class="portal-status-cancelled self-start">Cancelled</span>
                    @else
                        <span class="portal-status-default self-start">{{ ucfirst($flight->status) }}</span>
                    @endif
                </div>

                <div class="mt-4 grid gap-3 sm:mt-6 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Route</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $flight->departureAirport->code }} → {{ $flight->arrivalAirport->code }}</p>
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

                <div class="mt-3 grid gap-3 sm:mt-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Duration</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $durationHours }}h {{ $durationRemain }}m</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Aircraft</p>
                        <p class="mt-1 text-lg font-semibold text-slate-800">{{ $flight->airplane->model }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Base Price</p>
                        <p class="mt-1 text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </article>

            <article class="portal-card">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Seat Availability</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Layout kursi dibuat sama dengan halaman booking, jadi class dan seat availability tetap sinkron.
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($seatMap['classes'] as $classKey => $seatClass)
                        <button
                            type="button"
                            @click="selectedClass = '{{ $classKey }}'"
                            class="portal-card-soft text-left transition duration-200"
                            :class="selectedClass === '{{ $classKey }}' ? 'ring-2 ring-[#0f3f78] border-[#0f3f78] bg-white' : 'hover:bg-white'"
                        >
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="text-base font-semibold text-slate-800 sm:text-lg">{{ $seatClass['label'] }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $seatClass['description'] }}</p>
                                </div>
                                <span class="portal-status-default self-start">{{ $availableSeatCounts[$classKey] ?? 0 }}</span>
                            </div>
                            <p class="mt-3 text-xl font-bold text-[#0f3f78] sm:mt-4 sm:text-2xl">Rp{{ number_format((float) ($classPrices[$classKey] ?? 0), 0, ',', '.') }}</p>
                        </button>
                    @endforeach
                </div>

                <div class="mt-5 rounded-3xl border border-white/50 bg-white/45 p-4 sm:p-5">
                    @foreach ($seatMap['classes'] as $classKey => $seatClass)
                        <div x-show="selectedClass === '{{ $classKey }}'" x-cloak class="space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $seatClass['short_label'] }}</p>
                                    <h3 class="mt-1 text-xl font-bold text-slate-800 sm:text-2xl">{{ $seatClass['label'] }}</h3>
                                </div>
                                <span class="portal-status-confirmed">{{ $seatClass['available_count'] }} seat available</span>
                            </div>

                            @include('partials.seat-map', ['seatClass' => $seatClass, 'interactive' => false, 'classKey' => $classKey])
                        </div>
                    @endforeach
                </div>
            </article>
        </div>

        <aside class="portal-card h-fit xl:sticky xl:top-6">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78] sm:text-3xl">Ready to Book?</h2>
            <p class="mt-2 text-slate-600">
                Pilih class yang Anda inginkan, lalu lanjut ke booking untuk memilih passenger dan seat pada layout yang sama.
            </p>
            <div class="mt-5 space-y-3">
                @foreach ($seatMap['classes'] as $classKey => $seatClass)
                    <div x-show="selectedClass === '{{ $classKey }}'" x-cloak class="space-y-3">
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Cabin</p>
                            <p class="text-xl font-semibold text-slate-800">{{ $seatClass['label'] }}</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Available seats</p>
                            <p class="text-xl font-semibold text-slate-800">{{ $availableSeatCounts[$classKey] ?? 0 }} seats</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Price per passenger</p>
                            <p class="text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) ($classPrices[$classKey] ?? 0), 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Status</p>
                    <p class="text-xl font-semibold text-slate-800">{{ ucfirst($flight->status) }}</p>
                </div>
            </div>

            @auth
                @if (auth()->user()->isCustomer())
                    @foreach ($seatMap['classes'] as $classKey => $seatClass)
                        <a
                            x-show="selectedClass === '{{ $classKey }}'"
                            x-cloak
                            href="{{ route('booking.create', ['flight' => $flight->id, 'class' => $classKey]) }}"
                            class="portal-btn-gold mt-6 w-full"
                        >
                            Book {{ $seatClass['short_label'] }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ route('admin.dashboard') }}" class="portal-btn-blue mt-6 w-full">
                        Open Backoffice Dashboard
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
