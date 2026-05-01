@extends('layouts.portal')

@section('title', 'Zannora | My Bookings')
@section('active', 'bookings')

@section('content')
    <section>
        <div class="mb-5 flex items-center justify-between">
            <h1 class="font-heading text-4xl font-bold text-white">My Bookings</h1>
            <a href="{{ route('flights.index') }}" class="portal-btn-blue">Search New Flight</a>
        </div>

        <div class="space-y-4">
            @forelse ($bookings as $booking)
                <article class="portal-card">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $booking->flight->airline->name }}</p>
                            <h2 class="mt-1 text-2xl font-semibold text-slate-800">{{ $booking->booking_code }}</h2>
                            <p class="text-slate-600">
                                {{ $booking->flight->departureAirport->code }} â†’ {{ $booking->flight->arrivalAirport->code }}
                                Â· {{ $booking->flight->departure_time->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-500">Total</p>
                            <p class="text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
                            @if ($booking->status === 'pending')
                                <p class="mt-1 text-xs text-amber-700">Bayar sebelum {{ $booking->expired_at?->format('H:i:s') ?: '-' }}</p>
                            @endif
                            <div class="mt-2">
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
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('my-bookings.show', $booking) }}" class="portal-btn-blue px-4 py-2">Detail</a>
                            @if ($booking->status === 'pending')
                                <a href="{{ route('payments.create', ['booking' => $booking->id]) }}" class="portal-btn-gold px-4 py-2">
                                    {{ $booking->payments->sortByDesc('created_at')->first()?->submitted_at ? 'Update Payment' : 'Pay Now' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="portal-card text-center text-slate-600">
                    No booking yet.
                </div>
            @endforelse
        </div>

        @if ($bookings->hasPages())
            <div class="mt-6 rounded-2xl bg-white/80 p-3">
                {{ $bookings->links() }}
            </div>
        @endif
    </section>

@endsection
