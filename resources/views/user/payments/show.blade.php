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
        </div>
        <div class="mt-5">
            <a href="{{ route('my-bookings.show', $payment->booking) }}" class="portal-btn-blue">Back to Booking</a>
        </div>
    </section>
@endsection

