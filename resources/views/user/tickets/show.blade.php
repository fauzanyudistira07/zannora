@extends('layouts.portal')

@section('title', 'Zannora | E-Ticket')
@section('active', 'bookings')

@push('styles')
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body.portal-shell {
                min-height: auto !important;
                background: #ffffff !important;
            }

            body.portal-shell::before,
            header,
            footer,
            .ticket-print-actions {
                display: none !important;
            }

            main.portal-container {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .ticket-print-area {
                max-width: 100% !important;
                margin: 0 !important;
                gap: 10px !important;
            }

            .ticket-print-card {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: none !important;
                border: 1px solid #d4deea !important;
                background: #ffffff !important;
                padding: 14px !important;
            }

            .ticket-print-code {
                font-size: 28px !important;
                line-height: 1.1 !important;
            }

            .ticket-print-qr img {
                width: 140px !important;
                height: 140px !important;
            }

            .ticket-print-empty {
                min-height: 110px !important;
                padding: 16px !important;
            }
        }
    </style>
@endpush

@section('content')
    <section class="ticket-print-area mx-auto max-w-4xl space-y-6">
        <article class="ticket-print-card portal-card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">E-Ticket</p>
                    <h1 class="ticket-print-code mt-1 break-all font-heading text-3xl font-bold leading-tight text-slate-800 sm:text-4xl">{{ $detail->ticket_number ?: 'Ticket' }}</h1>
                    <p class="text-slate-600">{{ $flight->airline->name }} - {{ $booking->booking_code }}</p>
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
                    <p class="text-lg font-semibold text-slate-800">{{ $detail->seat?->seat_number }} - {{ ucfirst($detail->seat?->class ?? '-') }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $flight->departureAirport->code }} -> {{ $flight->arrivalAirport->code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Boarding Time</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $flight->departure_time->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="ticket-print-actions mt-6 flex flex-wrap items-center gap-3">
                @if ($ticket->pdf_path)
                    <a href="{{ asset('storage/'.$ticket->pdf_path) }}" target="_blank" class="portal-btn-gold">Download PDF</a>
                @endif
                <button type="button" onclick="window.print()" class="portal-btn-blue">Print</button>
                <a href="{{ route('my-bookings.show', $booking) }}" class="portal-btn-blue">Back to Booking</a>
            </div>
        </article>

        <article class="ticket-print-card portal-card">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">QR Code</h2>
            <div class="ticket-print-qr mt-4 flex items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white/70 p-8">
                @if ($ticket->qr_code_path)
                    @php($qrUrl = str_starts_with($ticket->qr_code_path, 'http') ? $ticket->qr_code_path : asset('storage/'.$ticket->qr_code_path))
                    <img src="{{ $qrUrl }}" alt="QR Ticket" class="h-48 w-48 object-contain">
                @else
                    <div class="ticket-print-empty text-center text-slate-500">
                        <p class="text-lg font-semibold text-slate-600">QR code belum tersedia</p>
                        <p class="mt-1 text-sm text-slate-500">Tiket tetap valid. Kode QR akan tampil otomatis setelah diaktifkan sistem.</p>
                    </div>
                @endif
            </div>
        </article>
    </section>
@endsection
