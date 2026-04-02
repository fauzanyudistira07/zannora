@extends('layouts.admin')

@section('title', 'Admin Dashboard | Zannora')
@section('page-title', 'Dashboard')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="admin-card">
                <p class="text-sm text-slate-500">Total Users</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['total_users'] }}</p>
            </article>
            <article class="admin-card">
                <p class="text-sm text-slate-500">Total Passengers</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['total_passengers'] }}</p>
            </article>
            <article class="admin-card">
                <p class="text-sm text-slate-500">Total Flights</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['total_flights'] }}</p>
            </article>
            <article class="admin-card">
                <p class="text-sm text-slate-500">Total Bookings</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['total_bookings'] }}</p>
            </article>
            <article class="admin-card">
                <p class="text-sm text-slate-500">Total Tickets</p>
                <p class="mt-2 text-3xl font-bold text-slate-800">{{ $stats['total_tickets'] }}</p>
            </article>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Booking Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $stats['booking_pending'] }}</p>
            </article>
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Payment Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $stats['payment_pending'] }}</p>
            </article>
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Revenue Total</p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">Rp{{ number_format($stats['revenue_total'], 0, ',', '.') }}</p>
            </article>
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Revenue This Month</p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">Rp{{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
            </article>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <article class="admin-card lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Booking Per Bulan</h2>
                </div>
                <div class="space-y-3">
                    @php($maxBooking = max($bookingChart->max('total'), 1))
                    @foreach ($bookingChart as $item)
                        <div class="grid gap-2 sm:grid-cols-[120px_1fr_40px] sm:items-center">
                            <p class="text-sm text-slate-600">{{ $item['month'] }}</p>
                            @include('admin.partials.progress-bar', ['percentage' => ($item['total'] / $maxBooking) * 100])
                            <p class="text-right text-sm font-semibold text-slate-700">{{ $item['total'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Quick Alert</h2>
                <div class="mt-4 space-y-2">
                    @forelse ($quickAlerts as $alert)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-700">{{ $alert }}</div>
                    @empty
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">Tidak ada alert kritis saat ini.</div>
                    @endforelse
                </div>

                <div class="mt-6 grid gap-3">
                    <div class="admin-card-soft">
                        <p class="text-sm text-slate-500">Booking Confirmed</p>
                        <p class="text-2xl font-bold text-emerald-700">{{ $stats['booking_confirmed'] }}</p>
                    </div>
                    <div class="admin-card-soft">
                        <p class="text-sm text-slate-500">Booking Cancelled</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['booking_cancelled'] }}</p>
                    </div>
                    <div class="admin-card-soft">
                        <p class="text-sm text-slate-500">Payment Paid vs Pending</p>
                        <p class="text-sm font-semibold text-slate-700">Paid {{ $paymentStatusChart['paid'] }} / Pending {{ $paymentStatusChart['pending'] }}</p>
                    </div>
                </div>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <article class="admin-card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Recent Bookings</h2>
                    <a href="{{ route('admin.bookings.index') }}" class="admin-btn-secondary">Lihat Semua</a>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>User</th>
                                <th>Flight</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentBookings as $booking)
                                <tr>
                                    <td><a class="font-semibold text-[#0f3f78]" href="{{ route('admin.bookings.show', $booking) }}">{{ $booking->booking_code }}</a></td>
                                    <td>{{ $booking->user?->name }}</td>
                                    <td>{{ $booking->flight?->flight_number }}</td>
                                    <td>@include('admin.partials.status-badge', ['status' => $booking->status])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-slate-500">Belum ada data booking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="admin-card">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Recent Payments</h2>
                    <a href="{{ route('admin.payments.index') }}" class="admin-btn-secondary">Lihat Semua</a>
                </div>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentPayments as $payment)
                                <tr>
                                    <td><a class="font-semibold text-[#0f3f78]" href="{{ route('admin.payments.show', $payment) }}">{{ $payment->booking?->booking_code }}</a></td>
                                    <td>{{ $payment->booking?->user?->name }}</td>
                                    <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                    <td>@include('admin.partials.status-badge', ['status' => $payment->payment_status])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-slate-500">Belum ada data payment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </section>
@endsection
