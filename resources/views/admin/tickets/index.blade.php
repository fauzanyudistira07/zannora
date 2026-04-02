@extends('layouts.admin')

@section('title', 'Admin Tickets | Zannora')
@section('page-title', 'Tickets')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-[1fr_auto]">
            <div>
                <label class="admin-label" for="search">Search Booking/Passenger/Flight/Ticket</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Ticket number, booking, passenger, flight">
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.tickets.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Passenger</th>
                            <th>Booking</th>
                            <th>Flight</th>
                            <th>Seat</th>
                            <th>Issued At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tickets as $ticket)
                            @php($detail = $ticket->bookingDetail)
                            <tr>
                                <td class="font-semibold">{{ $detail?->ticket_number ?: '-' }}</td>
                                <td>{{ $detail?->passenger?->full_name }}</td>
                                <td>{{ $detail?->booking?->booking_code }}</td>
                                <td>{{ $detail?->booking?->flight?->flight_number }}</td>
                                <td>{{ $detail?->seat?->seat_number }}</td>
                                <td>{{ $ticket->issued_at?->format('d M Y H:i') ?: '-' }}</td>
                                <td><a href="{{ route('admin.tickets.show', $ticket) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-slate-500">Data ticket belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $tickets->links() }}</div>
        </article>
    </section>
@endsection
