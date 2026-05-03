@extends('layouts.admin')

@section('title', 'Admin Flights | Zannora')
@section('page-title', 'Flights')

@section('content')
    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-white/90">Kelola jadwal dan status penerbangan.</p>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.flights.create') }}" class="admin-btn-primary">Tambah Flight</a>
            @endif
        </div>

        <form method="GET" class="admin-card grid gap-3 xl:grid-cols-6">
            <div>
                <label class="admin-label" for="search">Flight Number</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Contoh: GA123">
            </div>
            <div>
                <label class="admin-label" for="date">Date</label>
                <input id="date" name="date" type="date" value="{{ $date }}" class="admin-field">
            </div>
            <div>
                <label class="admin-label" for="route">Route Code</label>
                <input id="route" name="route" value="{{ $route }}" class="admin-field" placeholder="CGK / DPS">
            </div>
            <div>
                <label class="admin-label" for="airline_id">Airline</label>
                <select id="airline_id" name="airline_id" class="admin-field">
                    <option value="">Semua airline</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}" @selected((int) $airlineId === (int) $airline->id)>{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-field">
                    <option value="">Semua status</option>
                    <option value="scheduled" @selected($status === 'scheduled')>Scheduled</option>
                    <option value="delayed" @selected($status === 'delayed')>Delayed</option>
                    <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                    <option value="completed" @selected($status === 'completed')>Completed</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.flights.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Flight Number</th>
                            <th>Airline</th>
                            <th>Route</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Seats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($flights as $flight)
                            <tr>
                                <td class="font-semibold">{{ $flight->flight_number }}</td>
                                <td>{{ $flight->airline?->name }}</td>
                                <td>{{ $flight->departureAirport?->code }} -> {{ $flight->arrivalAirport?->code }}</td>
                                <td>{{ $flight->departure_time?->format('d M Y H:i') }}</td>
                                <td>{{ $flight->arrival_time?->format('d M Y H:i') }}</td>
                                <td>Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $flight->status])</td>
                                <td>{{ $flight->booked_seats }} / {{ $flight->airplane?->capacity }}</td>
                                <td class="space-x-1">
                                    <a href="{{ route('admin.flights.show', $flight) }}" class="admin-btn-secondary">Detail</a>
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('admin.flights.edit', $flight) }}" class="admin-btn-secondary">Edit</a>
                                        <form action="{{ route('admin.flights.destroy', $flight) }}" method="POST" class="inline" onsubmit="return confirm('Hapus flight ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-btn-secondary" type="submit">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-slate-500">Data flight belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $flights->links() }}</div>
        </article>
    </section>
@endsection
