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

            <div class="mt-5 grid gap-4 lg:grid-cols-[1fr_280px]">
                <form method="POST" action="{{ route('admin.airplanes.generate-seats', $airplane) }}" class="grid gap-4 rounded-2xl border border-slate-200 bg-white/70 p-4">
                    @csrf
                    <div>
                        <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Generate Cabin Layout</h2>
                        <p class="mt-1 text-sm text-slate-500">Buat kombinasi class dalam satu pesawat. Layout default: First 1-1, Business 2-2, Economy 3-3.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="admin-label" for="first_rows">First Class Rows</label>
                            <input id="first_rows" name="first_rows" type="number" min="0" value="{{ old('first_rows', $cabinSummary['first']['row_count']) }}" class="admin-field">
                        </div>
                        <div>
                            <label class="admin-label" for="business_rows">Business Class Rows</label>
                            <input id="business_rows" name="business_rows" type="number" min="0" value="{{ old('business_rows', $cabinSummary['business']['row_count']) }}" class="admin-field">
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="reset" value="1" class="rounded border-slate-300 text-[#0f3f78]"> Reset existing seats and rebuild cabin
                    </label>
                    <div class="flex flex-wrap items-center gap-2">
                        <button class="admin-btn-primary" type="submit">Generate Seats</button>
                        <span class="text-sm text-slate-500">Reset hanya bisa dipakai untuk pesawat yang belum punya histori booking.</span>
                    </div>
                </form>

                <div class="grid gap-3">
                    @foreach ($cabinSummary as $summary)
                        <div class="admin-card-soft">
                            <p class="text-sm text-slate-500">{{ $summary['label'] }}</p>
                            <p class="mt-1 text-lg font-semibold text-slate-800">{{ $summary['seat_count'] }} seats</p>
                            <p class="text-sm text-slate-500">{{ $summary['row_count'] }} rows</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <div class="grid gap-6 xl:grid-cols-2">
            <article class="admin-card">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Cabin Layout ({{ $airplane->seats_count }})</h2>
                    <span class="text-sm text-slate-500">Grouped by travel class</span>
                </div>

                <div class="mt-4 space-y-5">
                    @forelse ($seatMap['classes'] as $classKey => $seatClass)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $seatClass['label'] }}</p>
                                    <p class="text-sm text-slate-500">{{ $seatClass['description'] }}</p>
                                </div>
                                <span class="admin-badge admin-badge-default">{{ $seatClass['total_count'] }} seats</span>
                            </div>
                            @include('partials.seat-map', ['seatClass' => $seatClass, 'interactive' => false, 'classKey' => $classKey])
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada seat.</p>
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
