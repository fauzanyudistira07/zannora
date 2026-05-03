@extends('layouts.portal')

@section('title', 'Zannora | Payment')
@section('active', 'bookings')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <article class="portal-card">
            <h1 class="font-heading text-4xl font-bold text-[#0f3f78]">Pembayaran Midtrans</h1>
            <p class="mt-1 text-slate-600">Lanjutkan pembayaran booking {{ $booking->booking_code }} melalui Midtrans Snap.</p>

            <form method="POST" action="{{ route('payments.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="payment_method" value="midtrans_snap">

                <button type="submit" class="portal-btn-gold">Lanjut ke Midtrans Snap</button>

                @error('payment')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </form>
        </article>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Ringkasan Pembayaran</h2>
            <div class="mt-4 space-y-3">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Code</p>
                    <p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="font-semibold text-slate-800">
                        {{ $booking->flight->departureAirport->code }} -> {{ $booking->flight->arrivalAirport->code }}
                    </p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Total</p>
                    <p class="text-2xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Current Status</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($latestPayment?->payment_status ?? 'pending') }}</p>
                </div>
            </div>
        </aside>
    </section>
@endsection
