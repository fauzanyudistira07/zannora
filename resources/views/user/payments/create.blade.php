@extends('layouts.portal')

@section('title', 'Zannora | Payment')
@section('active', 'bookings')

@section('content')
    <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <article class="portal-card">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h1 class="font-heading text-4xl font-bold text-[#0f3f78]">Payment Page</h1>
                <span id="payment-api-status" class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">API: checking...</span>
            </div>
            <p class="mt-1 text-slate-600">Complete payment for booking {{ $booking->booking_code }}.</p>

            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                <div>
                    <label for="payment_method" class="portal-label">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="portal-select" required>
                        <option value="">Select payment method</option>
                        <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Bank Transfer</option>
                        <option value="e_wallet" @selected(old('payment_method') === 'e_wallet')>E-Wallet (Dummy)</option>
                    </select>
                    @error('payment_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="proof_file" class="portal-label">Upload Proof (Optional)</label>
                    <input id="proof_file" name="proof_file" type="file" class="portal-input">
                    @error('proof_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="portal-btn-gold">Submit Payment</button>
            </form>
        </article>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Payment Summary</h2>
            <div class="mt-4 space-y-3">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Code</p>
                    <p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="font-semibold text-slate-800">
                        {{ $booking->flight->departureAirport->code }} → {{ $booking->flight->arrivalAirport->code }}
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

    <script>
        (async () => {
            const statusEl = document.getElementById('payment-api-status');
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
