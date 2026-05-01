@extends('layouts.admin')

@section('title', 'Staff Dashboard | Zannora')
@section('page-title', 'Operations Dashboard')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="admin-kpi-card">
                <p class="admin-info-label">Pending Payments</p>
                <p class="mt-3 text-3xl font-bold text-amber-600">{{ $stats['payment_pending'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Transaksi yang menunggu verifikasi manual.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Open Bookings</p>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $stats['booking_pending'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Booking pending yang masih aktif di sistem.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Support Inbox</p>
                <p class="mt-3 text-3xl font-bold text-[#0f3f78]">{{ $stats['open_contact_messages'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Permintaan bantuan yang perlu ditindaklanjuti.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Active Flights</p>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $stats['total_flights'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Total flight yang dikelola di jaringan saat ini.</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <article class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Departure Watch</p>
                        <h2 class="admin-section-title">Upcoming departures</h2>
                    </div>
                    <a href="{{ route('admin.flights.index') }}" class="admin-btn-secondary">View Flights</a>
                </div>
                <div class="admin-table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Flight</th>
                                <th>Route</th>
                                <th>Departure</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($upcomingFlights as $flight)
                                <tr>
                                    <td>{{ $flight->flight_number }} · {{ $flight->airline?->name }}</td>
                                    <td>{{ $flight->departureAirport?->code }} -> {{ $flight->arrivalAirport?->code }}</td>
                                    <td>{{ $flight->departure_time?->format('d M Y H:i') }}</td>
                                    <td>@include('admin.partials.status-badge', ['status' => $flight->status])</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-slate-500">Tidak ada departure dalam 24 jam ke depan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Payment Queue</p>
                        <h2 class="admin-section-title">Pending payments</h2>
                    </div>
                    <a href="{{ route('admin.payments.index') }}" class="admin-btn-secondary">Open Payments</a>
                </div>
                <div class="admin-table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Customer</th>
                                <th>Airline</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($pendingPayments as $payment)
                                <tr>
                                    <td>{{ $payment->booking?->booking_code }}</td>
                                    <td>{{ $payment->booking?->user?->name }}</td>
                                    <td>{{ $payment->booking?->flight?->airline?->name }}</td>
                                    <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-slate-500">Tidak ada payment pending.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        <article class="admin-card">
            <div class="admin-section-head">
                <div>
                        <p class="admin-section-kicker">Support Queue</p>
                        <h2 class="admin-section-title">Open support messages</h2>
                    </div>
                <a href="{{ route('admin.contact-messages.index') }}" class="admin-btn-secondary">Open Inbox</a>
            </div>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th>Received</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($openMessages as $message)
                            <tr>
                                <td>{{ $message->name }}</td>
                                <td>{{ $message->subject }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $message->status])</td>
                                <td>{{ $message->assignedUser?->name ?: 'Unassigned' }}</td>
                                <td>{{ $message->created_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-slate-500">Tidak ada pesan bantuan terbuka.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
