@extends('layouts.portal')

@section('title', 'Zannora | Dashboard User')
@section('active', 'bookings')

@section('content')
    <section class="space-y-6">
        <h1 class="font-heading text-4xl font-bold text-white">Dashboard User</h1>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="portal-card">
                <p class="text-sm text-slate-500">Active Bookings</p>
                <p class="mt-2 text-4xl font-bold text-slate-800">{{ $stats['active_bookings'] }}</p>
            </article>
            <article class="portal-card">
                <p class="text-sm text-slate-500">Pending Payment</p>
                <p class="mt-2 text-4xl font-bold text-slate-800">{{ $stats['pending_payments'] }}</p>
            </article>
            <article class="portal-card">
                <p class="text-sm text-slate-500">Completed Trips</p>
                <p class="mt-2 text-4xl font-bold text-slate-800">{{ $stats['completed_trips'] }}</p>
            </article>
        </div>

        <article class="portal-card">
            <h2 class="font-heading text-2xl font-bold text-[#0f3f78]">Recent Booking</h2>
            <div class="mt-4 space-y-3">
                @forelse ($bookings as $booking)
                    <a href="{{ route('my-bookings.show', $booking) }}" class="portal-card-soft block transition hover:bg-white">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p>
                                <p class="text-sm text-slate-500">{{ $booking->flight->flight_number }} · {{ $booking->flight->airline->name }}</p>
                            </div>
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
                    </a>
                @empty
                    <p class="portal-card-soft text-slate-500">No booking yet.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection

