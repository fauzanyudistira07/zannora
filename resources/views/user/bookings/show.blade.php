@extends('layouts.portal')

@section('title', 'Zannora | Booking Detail')
@section('active', 'bookings')

@section('content')
    @php
        $ticket = optional($booking->details->first())->ticket;
    @endphp

    <section class="space-y-6">
        <article class="portal-card">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $booking->flight->airline->name }}</p>
                    <h1 class="mt-1 font-heading text-4xl font-bold text-slate-800">{{ $booking->booking_code }}</h1>
                    <p class="text-slate-600">
                        {{ $booking->flight->departureAirport->code }} → {{ $booking->flight->arrivalAirport->code }}
                        · {{ $booking->flight->departure_time->format('d M Y H:i') }}
                    </p>
                </div>
                <div>
                    @if ($booking->status === 'pending')
                        <span class="portal-status-pending">Pending</span>
                    @elseif ($booking->status === 'confirmed')
                        <span class="portal-status-confirmed">Confirmed</span>
                    @elseif ($booking->status === 'cancelled')
                        <span class="portal-status-cancelled">Cancelled</span>
                    @else
                        <span class="portal-status-default">{{ ucfirst($booking->status) }}</span>
                    @endif
                </div>
                <span id="booking-detail-api-status" class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">API: checking...</span>
            </div>
        </article>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="portal-card">
                <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Passenger & Seat</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($booking->details as $detail)
                        <div class="portal-card-soft flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $detail->passenger?->full_name }}</p>
                                <p class="text-sm text-slate-600">Seat {{ $detail->seat?->seat_number }} · {{ ucfirst($detail->seat?->class ?? '-') }}</p>
                            </div>
                            <p class="font-semibold text-[#0f3f78]">Rp{{ number_format((float) $detail->price, 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="portal-card-soft text-slate-500">No passenger details.</p>
                    @endforelse
                </div>
            </article>

            <article class="portal-card">
                <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Payment Info</h2>
                @if ($latestPayment)
                    <div class="mt-4 space-y-3">
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Method</p>
                            <p class="font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $latestPayment->payment_method)) }}</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Amount</p>
                            <p class="font-semibold text-slate-800">Rp{{ number_format((float) $latestPayment->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Status</p>
                            <p class="font-semibold text-slate-800">{{ ucfirst($latestPayment->payment_status) }}</p>
                        </div>
                    </div>
                @else
                    <p class="mt-4 portal-card-soft text-slate-500">No payment data yet.</p>
                @endif
            </article>
        </div>

        <article class="portal-card">
            <div class="flex flex-wrap items-center gap-3">
                @if ($booking->status === 'pending')
                    <a href="{{ route('payments.create', ['booking' => $booking->id]) }}" class="portal-btn-gold">Pay Now</a>
                    <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}">
                        @csrf
                        <button type="submit" class="portal-btn-blue">Cancel Booking</button>
                    </form>
                @endif

                @if ($ticket)
                    <a href="{{ route('tickets.show', $ticket) }}" class="portal-btn-blue">Download Ticket</a>
                @endif
            </div>
        </article>
    </section>

    <script>
        (async () => {
            const statusEl = document.getElementById('booking-detail-api-status');
            if (!statusEl) return;

            try {
                await zannoraApiFetch('/api/v1/bookings/{{ $booking->id }}');
                statusEl.textContent = 'API: connected';
            } catch (error) {
                statusEl.textContent = 'API: unavailable';
            }
        })();
    </script>
@endsection
