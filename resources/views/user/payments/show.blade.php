@extends('layouts.portal')

@section('title', 'Zannora | Payment Detail')
@section('active', 'bookings')

@section('content')
    <section class="space-y-6">
        <article class="portal-card">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.25em] text-[#315b8c]">Payment</p>
                    <h1 class="font-heading text-3xl font-bold text-[#0f3f78] sm:text-4xl">{{ $paymentLabel }}</h1>
                    <p class="mt-2 text-slate-600">Booking {{ $payment->booking->booking_code }}</p>
                </div>
                @if ($payment->payment_status === 'paid')
                    <span class="portal-status-confirmed">Paid</span>
                @elseif ($payment->payment_status === 'pending')
                    <span class="portal-status-pending">Pending Verification</span>
                @elseif ($payment->payment_status === 'failed')
                    <span class="portal-status-cancelled">Failed</span>
                @else
                    <span class="portal-status-default">{{ ucfirst($payment->payment_status) }}</span>
                @endif
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Amount</p>
                    <p class="text-2xl font-bold text-slate-800">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Transaction Code</p>
                    <p class="font-semibold text-slate-800">{{ $payment->transaction_code ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Submitted At</p>
                    <p class="font-semibold text-slate-800">{{ $payment->submitted_at?->format('d M Y H:i:s') ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Paid At</p>
                    <p class="font-semibold text-slate-800">{{ $payment->paid_at?->format('d M Y H:i:s') ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Route</p>
                    <p class="font-semibold text-slate-800">{{ $payment->booking->flight->departureAirport->code }} -> {{ $payment->booking->flight->arrivalAirport->code }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Booking Status</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($payment->booking->status) }}</p>
                </div>
            </div>
        </article>

        <article class="portal-card">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Submitted Payment Data</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Nama Pembayar</p>
                    <p class="font-semibold text-slate-800">{{ $payment->payer_name ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Nomor Telepon</p>
                    <p class="font-semibold text-slate-800">{{ $payment->payer_phone ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Bank Pengirim</p>
                    <p class="font-semibold text-slate-800">{{ $payment->payer_bank_name ?: '-' }}</p>
                </div>
                <div class="portal-card-soft">
                    <p class="text-sm text-slate-500">Nomor Rekening Pengirim</p>
                    <p class="font-semibold text-slate-800">{{ $payment->payer_bank_account_number ?: '-' }}</p>
                </div>
                <div class="portal-card-soft md:col-span-2">
                    <p class="text-sm text-slate-500">Catatan</p>
                    <p class="font-semibold text-slate-800">{{ $payment->payment_notes ?: '-' }}</p>
                </div>
                <div class="portal-card-soft md:col-span-2">
                    <p class="text-sm text-slate-500">Bukti Pembayaran</p>
                    @if ($payment->proof_file)
                        <a href="{{ asset('storage/'.$payment->proof_file) }}" target="_blank" class="font-semibold text-[#0f3f78] underline">Open uploaded proof</a>
                    @else
                        <p class="font-semibold text-slate-800">-</p>
                    @endif
                </div>
            </div>
        </article>

        <article class="portal-card">
            <div class="flex flex-wrap items-center gap-3">
                @if ($qrisUrl)
                    <a href="{{ $qrisUrl }}" class="portal-btn-gold">Open QRIS</a>
                @endif
                @if ($payment->payment_status !== 'paid' && $payment->booking->status === 'pending')
                    <a href="{{ route('payments.create', ['booking' => $payment->booking->id]) }}" class="portal-btn-blue">Edit Payment Submission</a>
                @endif
                <a href="{{ route('my-bookings.show', $payment->booking) }}" class="portal-btn-blue">Back to Booking</a>
            </div>
        </article>
    </section>
@endsection
