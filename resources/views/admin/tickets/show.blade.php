@extends('layouts.admin')

@section('title', 'Ticket Detail | Zannora')
@section('page-title', 'Ticket Detail')

@section('content')
    @php($detail = $ticket->bookingDetail)
    @php($booking = $detail?->booking)
    @php($flight = $booking?->flight)

    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Ticket Number</p><p class="font-semibold text-slate-800">{{ $detail?->ticket_number ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Booking Code</p><p class="font-semibold text-slate-800">{{ $booking?->booking_code ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Passenger</p><p class="font-semibold text-slate-800">{{ $detail?->passenger?->full_name ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Seat</p><p class="font-semibold text-slate-800">{{ $detail?->seat?->seat_number ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Flight</p><p class="font-semibold text-slate-800">{{ $flight?->flight_number ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Route</p><p class="font-semibold text-slate-800">{{ $flight?->departureAirport?->code ?: '-' }} -> {{ $flight?->arrivalAirport?->code ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Issued At</p><p class="font-semibold text-slate-800">{{ $ticket->issued_at?->format('d M Y H:i') ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Booking Status</p>@include('admin.partials.status-badge', ['status' => $booking?->status])</div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                    <form method="POST" action="{{ route('admin.tickets.regenerate', $ticket) }}">
                        @csrf
                        <button class="admin-btn-primary" type="submit">Regenerate Ticket</button>
                    </form>
                @endif

                @if ($ticket->pdf_path)
                    <a href="{{ route('admin.tickets.pdf', $ticket) }}" class="admin-btn-secondary">Download PDF</a>
                @endif

                @if ($ticket->qr_code_path)
                    <a href="{{ route('admin.tickets.qr', $ticket) }}" target="_blank" class="admin-btn-secondary">Open QR</a>
                @endif
            </div>
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Related Booking</h2>
            @if ($booking)
                <div class="mt-4 admin-card-soft">
                    <p class="text-sm text-slate-500">User</p>
                    <p class="font-semibold text-slate-800">{{ $booking->user?->name }} ({{ $booking->user?->email }})</p>
                    <p class="mt-2 text-sm text-slate-500">Total Price</p>
                    <p class="font-semibold text-slate-800">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="admin-btn-secondary mt-3">Lihat Booking</a>
                </div>
            @else
                <p class="mt-4 text-sm text-slate-500">Booking terkait tidak ditemukan.</p>
            @endif
        </article>
    </section>
@endsection
