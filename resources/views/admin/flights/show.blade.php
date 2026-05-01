@extends('layouts.admin')

@section('title', 'Flight Detail | Zannora')
@section('page-title', 'Flight Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div><p class="text-sm text-slate-500">Flight Number</p><p class="font-semibold text-slate-800">{{ $flight->flight_number }}</p></div>
                    <div><p class="text-sm text-slate-500">Airline</p><p class="font-semibold text-slate-800">{{ $flight->airline?->name }}</p></div>
                    <div><p class="text-sm text-slate-500">Airplane</p><p class="font-semibold text-slate-800">{{ $flight->airplane?->model }}</p></div>
                    <div><p class="text-sm text-slate-500">Price</p><p class="font-semibold text-slate-800">Rp{{ number_format((float) $flight->price, 0, ',', '.') }}</p></div>
                    <div><p class="text-sm text-slate-500">Departure</p><p class="font-semibold text-slate-800">{{ $flight->departureAirport?->code }} - {{ $flight->departure_time?->format('d M Y H:i') }}</p></div>
                    <div><p class="text-sm text-slate-500">Arrival</p><p class="font-semibold text-slate-800">{{ $flight->arrivalAirport?->code }} - {{ $flight->arrival_time?->format('d M Y H:i') }}</p></div>
                    <div><p class="text-sm text-slate-500">Booked Seats</p><p class="font-semibold text-slate-800">{{ $bookedSeatIds->count() }}</p></div>
                    <div><p class="text-sm text-slate-500">Available Seats</p><p class="font-semibold text-slate-800">{{ $availableSeats }}</p></div>
                </div>

                <form method="POST" action="{{ route('admin.flights.status', $flight) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="admin-field" required>
                        <option value="scheduled" @selected($flight->status === 'scheduled')>Scheduled</option>
                        <option value="delayed" @selected($flight->status === 'delayed')>Delayed</option>
                        <option value="cancelled" @selected($flight->status === 'cancelled')>Cancelled</option>
                        <option value="completed" @selected($flight->status === 'completed')>Completed</option>
                    </select>
                    <button class="admin-btn-primary" type="submit">Update Status</button>
                </form>
            </div>
        </article>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Payment Paid</p><p class="text-xl font-bold text-emerald-700">{{ $paymentSummary['paid'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Payment Pending</p><p class="text-xl font-bold text-amber-600">{{ $paymentSummary['pending'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Payment Failed</p><p class="text-xl font-bold text-red-600">{{ $paymentSummary['failed'] }}</p></article>
        </div>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Booking List</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Passengers</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Ticket Issued</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($flight->bookings as $booking)
                            <tr>
                                <td>{{ $booking->booking_code }}</td>
                                <td>{{ $booking->user?->name }}</td>
                                <td>{{ $booking->total_passengers }}</td>
                                <td>Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $booking->status])</td>
                                <td>{{ $booking->details->whereNotNull('ticket')->count() }}</td>
                                <td><a href="{{ route('admin.bookings.show', $booking) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-slate-500">Belum ada booking untuk flight ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
