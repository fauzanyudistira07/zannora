@extends('layouts.portal')

@section('title', 'Zannora | Payment Detail')
@section('active', 'bookings')

@section('content')
    <section class="portal-card">
        <h1 class="font-heading text-4xl font-bold text-[#0f3f78]">Payment Detail</h1>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="portal-card-soft">
                <p class="text-sm text-slate-500">Booking</p>
                <p class="font-semibold text-slate-800">{{ $payment->booking->booking_code }}</p>
            </div>
            <div class="portal-card-soft">
                <p class="text-sm text-slate-500">Method</p>
                <p class="font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            </div>
            <div class="portal-card-soft">
                <p class="text-sm text-slate-500">Amount</p>
                <p class="font-semibold text-slate-800">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
            </div>
            <div class="portal-card-soft">
                <p class="text-sm text-slate-500">Status</p>
                <p class="font-semibold text-slate-800">{{ ucfirst($payment->payment_status) }}</p>
            </div>
            @if ($payment->midtrans_order_id)
                <div class="portal-card-soft md:col-span-2">
                    <p class="text-sm text-slate-500">Midtrans Order ID</p>
                    <p class="font-semibold text-slate-800">{{ $payment->midtrans_order_id }}</p>
                </div>
            @endif
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            @if ($midtransUrl)
                <a href="{{ $midtransUrl }}" target="_blank" rel="noopener" class="portal-btn-gold">Bayar Sekarang (Midtrans Snap)</a>
            @endif
            @if ($payment->payment_status !== 'paid' && $payment->booking->status === 'pending')
                <a href="{{ route('payments.create', ['booking' => $payment->booking->id]) }}" class="portal-btn-blue">Ubah Pengajuan Pembayaran</a>
            @endif
            <a href="{{ route('my-bookings.show', $payment->booking) }}" class="portal-btn-blue">Kembali ke Booking</a>
        </div>

        @if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending')
            <p id="payment-sync-hint" class="mt-4 text-sm text-slate-600">
                Menunggu konfirmasi pembayaran. Status akan diperbarui otomatis.
            </p>
        @endif
    </section>

    @if ($payment->payment_method === 'midtrans_snap' && $payment->payment_status === 'pending')
        <script>
            (function () {
                const syncUrl = @json(route('payments.sync', $payment));
                const hintEl = document.getElementById('payment-sync-hint');
                const maxAttempts = 20;
                let attempts = 0;

                const runSync = async () => {
                    attempts += 1;

                    try {
                        const response = await zannoraApiFetch(syncUrl);
                        const status = (response?.payment_status || '').toLowerCase();

                        if (status === 'paid' || status === 'failed' || status === 'refunded') {
                            window.location.reload();
                            return;
                        }
                    } catch (error) {
                        // Keep trying for temporary gateway/network issues.
                    }

                    if (attempts < maxAttempts) {
                        window.setTimeout(runSync, 2500);
                    } else if (hintEl) {
                        hintEl.textContent = 'Status masih diproses. Silakan refresh halaman 10-20 detik lagi jika belum berubah.';
                    }
                };

                window.setTimeout(runSync, 1500);
            })();
        </script>
    @endif
@endsection
