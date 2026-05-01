@extends('layouts.portal')

@section('title', 'Zannora | QRIS Payment')
@section('active', 'bookings')

@section('content')
    <section x-data="qrisCountdown(@js(optional($payment->booking->expired_at)?->toIso8601String()))" class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <article class="portal-card">
            <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Instant Payment</p>
            <h1 class="font-heading text-3xl font-bold text-[#0f3f78] sm:text-4xl">{{ $paymentLabel }}</h1>
            <p class="mt-2 text-slate-600">Scan QR berikut dalam 5 menit. Jika lewat, booking akan expired dan kursi dilepas ke user lain.</p>

            <div class="mt-6 grid gap-6 lg:grid-cols-[320px_1fr]">
                <div class="rounded-[28px] border border-slate-200 bg-white/80 p-5 text-center shadow-[0_14px_36px_rgba(15,63,120,.08)]">
                    <div class="mx-auto grid h-12 w-12 place-content-center rounded-2xl bg-[#0f3f78] text-sm font-bold text-white">QR</div>
                    <div class="mt-4 flex justify-center">
                        <img src="{{ $qrCode }}" alt="QRIS Demo" class="h-64 w-64 object-contain">
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Saat QR berhasil discan, pembayaran akan otomatis masuk status paid dan tiket diterbitkan.</p>
                </div>

                <div class="space-y-4">
                    <div class="portal-card-soft">
                        <p class="text-sm text-slate-500">Amount</p>
                        <p class="text-3xl font-bold text-[#0f3f78]">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-sm text-slate-500">Booking</p>
                        <p class="font-semibold text-slate-800">{{ $payment->booking->booking_code }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $payment->booking->flight->airline->name }} · {{ $payment->booking->flight->departureAirport->code }} -> {{ $payment->booking->flight->arrivalAirport->code }}</p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-sm text-slate-500">Batas Pembayaran</p>
                        <p class="font-semibold text-slate-800">{{ optional($payment->booking->expired_at)->format('d M Y H:i:s') ?: '-' }}</p>
                        <p class="mt-1 text-sm font-semibold text-amber-600" x-text="countdown"></p>
                    </div>
                    <div class="portal-card-soft">
                        <p class="text-sm text-slate-500">Simulated Scan URL</p>
                        <p class="mt-1 break-all text-xs text-slate-500">{{ $scanUrl }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ $scanUrl }}" target="_blank" class="portal-btn-gold">Simulate QR Scan</a>
                        <a href="{{ route('payments.show', $payment) }}" class="portal-btn-blue">Payment Detail</a>
                        <a href="{{ route('my-bookings.show', $payment->booking) }}" class="portal-btn-blue">Back to Booking</a>
                    </div>
                </div>
            </div>
        </article>

        <aside class="portal-card h-fit">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Status</h2>
            <div class="mt-4 space-y-3">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Payment Status</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($payment->payment_status) }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Status</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($payment->booking->status) }}</p>
                </div>
            </div>
        </aside>
    </section>

    <script>
        function qrisCountdown(expiresAt) {
            return {
                expiresAt,
                countdown: '',
                init() {
                    this.updateCountdown();
                    setInterval(() => this.updateCountdown(), 1000);
                },
                updateCountdown() {
                    if (!this.expiresAt) {
                        this.countdown = '';
                        return;
                    }

                    const diff = new Date(this.expiresAt).getTime() - Date.now();

                    if (diff <= 0) {
                        this.countdown = 'Sesi pembayaran telah berakhir. Refresh halaman.';
                        return;
                    }

                    const minutes = Math.floor(diff / 60000);
                    const seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                    this.countdown = `Sisa waktu ${minutes}:${seconds}`;
                },
            };
        }

        setTimeout(() => window.location.reload(), 10000);
    </script>
@endsection
