@extends('layouts.portal')

@section('title', 'Zannora | E-Ticket')
@section('active', 'bookings')

@section('content')
    <section class="mx-auto max-w-4xl space-y-6">
        <article class="portal-card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">E-Ticket</p>
                    <h1 class="mt-1 font-heading text-4xl font-bold text-slate-800">{{ $detail->ticket_number ?: 'Ticket' }}</h1>
                    <p class="text-slate-600">{{ $flight->airline->name }} · {{ $booking->booking_code }}</p>
                </div>
                @if ($ticket->issued_at)
                    <span class="portal-status-confirmed">Issued {{ $ticket->issued_at->format('d M Y H:i') }}</span>
                @endif
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Passenger</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $detail->passenger?->full_name }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Seat</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $detail->seat?->seat_number }} · {{ ucfirst($detail->seat?->class ?? '-') }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $flight->departureAirport->code }} → {{ $flight->arrivalAirport->code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Boarding Time</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $flight->departure_time->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                @if ($ticket->pdf_path)
                    <a href="{{ asset('storage/'.$ticket->pdf_path) }}" target="_blank" class="portal-btn-gold">Download PDF</a>
                @endif
                <button type="button" onclick="window.print()" class="portal-btn-blue">Print</button>
                <a href="{{ route('my-bookings.show', $booking) }}" class="portal-btn-blue">Back to Booking</a>
            </div>
        </article>

        <article class="portal-card">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">QR Code</h2>
            <div class="mt-4 flex items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white/70 p-8">
                @if ($ticket->qr_code_path)
                    <img src="{{ asset('storage/'.$ticket->qr_code_path) }}" alt="QR Ticket" class="h-48 w-48 object-contain">
                @else
                    <div class="text-center text-slate-500">
                        <p class="text-6xl">◻</p>
                        <p>QR code belum tersedia.</p>
                    </div>
                @endif
            </div>
        </article>
    </section>
@endsection

