@extends('layouts.admin')

@section('title', 'Detail User | Zannora')
@section('page-title', 'User Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <p class="text-sm text-slate-500">Name</p>
                    <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Email</p>
                    <p class="font-semibold text-slate-800">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Phone</p>
                    <p class="font-semibold text-slate-800">{{ $user->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Registered</p>
                    <p class="font-semibold text-slate-800">{{ $user->created_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </article>

        <div class="grid gap-4 md:grid-cols-3">
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Total Passenger</p>
                <p class="text-2xl font-bold text-slate-800">{{ $user->passengers_count }}</p>
            </article>
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Total Booking</p>
                <p class="text-2xl font-bold text-slate-800">{{ $user->bookings_count }}</p>
            </article>
            <article class="admin-card-soft">
                <p class="text-sm text-slate-500">Total Ticket</p>
                <p class="text-2xl font-bold text-slate-800">{{ $ticketCount }}</p>
            </article>
        </div>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Passenger List</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Identity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($passengers as $passenger)
                            <tr>
                                <td>{{ $passenger->full_name }}</td>
                                <td>{{ ucfirst($passenger->gender) }}</td>
                                <td>{{ $passenger->birth_date?->format('d M Y') }}</td>
                                <td>{{ $passenger->identity_number ?: ($passenger->passport_number ?: '-') }}</td>
                                <td><a href="{{ route('admin.passengers.show', $passenger) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-slate-500">Belum ada passenger.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Recent Booking</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>Flight</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bookings as $booking)
                            @php($latestPayment = $booking->payments->sortByDesc('created_at')->first())
                            <tr>
                                <td>{{ $booking->booking_code }}</td>
                                <td>{{ $booking->flight?->flight_number }} - {{ $booking->flight?->airline?->name }}</td>
                                <td>Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $booking->status])</td>
                                <td>@include('admin.partials.status-badge', ['status' => $latestPayment?->payment_status])</td>
                                <td><a href="{{ route('admin.bookings.show', $booking) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Belum ada booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <div class="admin-card-soft">
                    <p class="text-sm text-slate-500">Payment Paid</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $paymentStats['paid'] }}</p>
                </div>
                <div class="admin-card-soft">
                    <p class="text-sm text-slate-500">Payment Pending</p>
                    <p class="text-xl font-bold text-amber-600">{{ $paymentStats['pending'] }}</p>
                </div>
                <div class="admin-card-soft">
                    <p class="text-sm text-slate-500">Payment Failed</p>
                    <p class="text-xl font-bold text-red-600">{{ $paymentStats['failed'] }}</p>
                </div>
            </div>
        </article>
    </section>
@endsection
