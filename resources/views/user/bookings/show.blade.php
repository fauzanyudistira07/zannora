@extends('layouts.portal')

@section('title', 'Zannora | Booking Detail')
@section('active', 'bookings')

@section('content')
    @php($ticketCount = $booking->details->filter(fn ($detail) => $detail->ticket !== null)->count())
    @php($activeAddonCount = $booking->addons->whereIn('status', ['selected', 'paid'])->count())
    @php($openChangeRequestCount = $booking->changeRequests->whereIn('status', ['submitted', 'in_review', 'approved'])->count())
    @php($checkinCompletedCount = $booking->details->whereIn('boarding_status', ['checked_in', 'boarded'])->count())

    <section class="space-y-6">
        <article class="portal-card">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">{{ $booking->flight->airline->name }}</p>
                    <h1 class="portal-fluid-title mt-1 font-heading font-bold text-slate-800">{{ $booking->booking_code }}</h1>
                    <p class="text-slate-600">
                        {{ $booking->flight->departureAirport->code }} -> {{ $booking->flight->arrivalAirport->code }}
                        Â· {{ $booking->flight->departure_time->format('d M Y H:i') }}
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
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="portal-card-soft">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Passengers</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $booking->details->count() }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Check-In Done</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $checkinCompletedCount }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Active Add-Ons</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $activeAddonCount }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Open Requests</p>
                    <p class="mt-1 text-2xl font-bold text-slate-800">{{ $openChangeRequestCount }}</p>
                </div>
            </div>

            @if ($booking->status === 'pending')
                <div class="mt-4 portal-card-soft">
                    <p class="text-sm text-slate-500">Payment Deadline</p>
                    <p class="font-semibold text-slate-800">{{ $booking->expired_at?->format('d M Y H:i:s') ?: '-' }}</p>
                    <p class="mt-1 text-sm text-amber-700">Jika pembayaran tidak dikirim dan diselesaikan sebelum waktu ini, booking akan expired dan kursi kembali tersedia.</p>
                </div>
            @endif
        </article>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="portal-card">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Passenger & Seat</h2>
                    @if ($ticketCount > 0)
                        <span class="portal-status-default">{{ $ticketCount }} ticket{{ $ticketCount > 1 ? 's' : '' }}</span>
                    @endif
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($booking->details as $detail)
                        <div class="portal-card-soft flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $detail->passenger?->full_name }}</p>
                                <p class="text-sm text-slate-600">Seat {{ $detail->seat?->seat_number }} Â· {{ ucfirst($detail->seat?->class ?? '-') }}</p>
                                <p class="mt-1 text-sm text-slate-500">
                                    Boarding:
                                    <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', (string) $detail->boarding_status)) }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-[#0f3f78]">Rp{{ number_format((float) $detail->price, 0, ',', '.') }}</p>
                                @if ($detail->ticket)
                                    <a href="{{ route('tickets.show', $detail->ticket) }}" class="mt-2 inline-flex text-sm font-semibold text-[#0f3f78] underline underline-offset-2">Open ticket</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="portal-card-soft text-slate-500">No passenger details.</p>
                    @endforelse
                </div>
            </article>

            <article class="portal-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Payment Info</h2>
                @if ($latestPayment)
                    <div class="mt-4 space-y-3">
                        <div class="portal-card-soft">
                            <p class="text-sm text-slate-500">Method</p>
                            <p class="font-semibold text-slate-800">{{ \App\Support\PaymentMethodCatalog::label($latestPayment->payment_method) }}</p>
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

        @if ($booking->addons->isNotEmpty())
            <article class="portal-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78] sm:text-2xl">Selected Add-Ons</h2>
                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach ($booking->addons->sortByDesc('created_at') as $addon)
                        <div class="portal-card-soft">
                            <p class="font-semibold text-slate-800">{{ $addon->addon_name }}</p>
                            <p class="text-sm text-slate-600">{{ ucfirst($addon->addon_type) }} Â· Qty {{ $addon->quantity }}</p>
                            @if ($addon->bookingDetail?->passenger)
                                <p class="mt-1 text-xs text-slate-500">Passenger: {{ $addon->bookingDetail->passenger->full_name }}</p>
                            @endif
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <p class="font-semibold text-[#0f3f78]">Rp{{ number_format((float) $addon->total_price, 0, ',', '.') }}</p>
                                @include('admin.partials.status-badge', ['status' => $addon->status])
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        @endif

        <article class="portal-card">
            <div class="flex flex-wrap items-center gap-3">
                @if ($booking->status === 'pending')
                    <a href="{{ route('payments.create', ['booking' => $booking->id]) }}" class="portal-btn-gold">
                        {{ $latestPayment?->submitted_at ? 'Update Payment Submission' : 'Pay Now' }}
                    </a>
                    @if ($latestPayment?->submitted_at)
                        <a href="{{ route('payments.show', $latestPayment) }}" class="portal-btn-blue">Transaction Detail</a>
                    @endif
                    @if ($latestPayment?->payment_method === 'qris')
                        <a href="{{ route('payments.qris.show', $latestPayment) }}" class="portal-btn-blue">Open QRIS</a>
                    @endif
                    <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}">
                        @csrf
                        <button type="submit" class="portal-btn-blue">Cancel Booking</button>
                    </form>
                @endif

                <a href="{{ route('my-bookings.addons.index', $booking) }}" class="portal-btn-blue">Manage Add-Ons</a>
                <a href="{{ route('my-bookings.change-requests.index', ['booking' => $booking->id]) }}" class="portal-btn-blue">Refund / Change Request</a>

                @if (in_array($booking->status, ['confirmed', 'completed'], true))
                    <a href="{{ route('my-bookings.checkin.index', $booking) }}" class="portal-btn-blue">Online Check-In</a>
                @endif

                @if ($ticketCount > 0)
                    <a href="{{ route('my-bookings.tickets', $booking) }}" class="portal-btn-blue">
                        {{ $ticketCount > 1 ? 'Open All Tickets' : 'Open Ticket' }}
                    </a>
                    @if ($ticketCount > 1)
                        <a href="{{ route('my-bookings.tickets.download-all', $booking) }}" class="portal-btn-blue">Download All PDFs</a>
                    @endif
                @endif
            </div>
        </article>
    </section>

@endsection
