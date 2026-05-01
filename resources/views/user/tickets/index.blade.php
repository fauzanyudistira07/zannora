@extends('layouts.portal')

@section('title', 'Zannora | Booking Tickets')
@section('active', 'bookings')

@section('content')
    <section class="ticket-sheet mx-auto max-w-5xl space-y-6">
        <article class="portal-card portal-print-hide">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Booking Tickets</p>
                    <h1 class="portal-fluid-title mt-1 font-heading font-bold text-slate-800">{{ $booking->booking_code }}</h1>
                    <p class="mt-2 text-slate-600">
                        {{ $flight->airline->name }} · {{ $flight->departureAirport->code }} → {{ $flight->arrivalAirport->code }}
                    </p>
                    <p class="mt-1 text-sm text-slate-500">{{ $ticketDetails->count() }} passenger ticket{{ $ticketDetails->count() > 1 ? 's' : '' }} ready</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" onclick="window.print()" class="portal-btn-gold">Print All Tickets</button>
                    <a href="{{ route('my-bookings.tickets.download-all', $booking) }}" class="portal-btn-blue">Download All PDFs</a>
                    <a href="{{ route('my-bookings.show', $booking) }}" class="portal-btn-blue">Back to Booking</a>
                </div>
            </div>
        </article>

        @foreach ($ticketDetails as $detail)
            @php($ticket = $detail->ticket)
            @include('user.tickets.partials.ticket-card', ['ticket' => $ticket, 'detail' => $detail, 'booking' => $booking, 'flight' => $flight, 'showSingleLink' => true, 'showPrintButton' => false])
        @endforeach
    </section>
@endsection
