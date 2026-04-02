@extends('layouts.admin')

@section('title', 'Admin Bookings | Zannora')
@section('page-title', 'Bookings')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 xl:grid-cols-7">
            <div>
                <label class="admin-label" for="search">Booking Code</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="BK-...">
            </div>
            <div>
                <label class="admin-label" for="status">Status</label>
                <select id="status" name="status" class="admin-field">
                    <option value="">Semua</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="confirmed" @selected($status === 'confirmed')>Confirmed</option>
                    <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                    <option value="completed" @selected($status === 'completed')>Completed</option>
                </select>
            </div>
            <div>
                <label class="admin-label" for="date">Tanggal Booking</label>
                <input id="date" name="date" type="date" value="{{ $date }}" class="admin-field">
            </div>
            <div>
                <label class="admin-label" for="user">User</label>
                <input id="user" name="user" value="{{ $user }}" class="admin-field" placeholder="Nama user">
            </div>
            <div>
                <label class="admin-label" for="flight">Flight</label>
                <input id="flight" name="flight" value="{{ $flight }}" class="admin-field" placeholder="Flight number">
            </div>
            <div>
                <label class="admin-label" for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status" class="admin-field">
                    <option value="">Semua</option>
                    <option value="pending" @selected($paymentStatus === 'pending')>Pending</option>
                    <option value="paid" @selected($paymentStatus === 'paid')>Paid</option>
                    <option value="failed" @selected($paymentStatus === 'failed')>Failed</option>
                    <option value="refunded" @selected($paymentStatus === 'refunded')>Refunded</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.bookings.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Flight</th>
                            <th>Route</th>
                            <th>Passengers</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Expired At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($bookings as $booking)
                            @php($latestPayment = $booking->payments->sortByDesc('created_at')->first())
                            <tr>
                                <td class="font-semibold">{{ $booking->booking_code }}</td>
                                <td>{{ $booking->user?->name }}</td>
                                <td>{{ $booking->flight?->flight_number }}</td>
                                <td>{{ $booking->flight?->departureAirport?->code }} -> {{ $booking->flight?->arrivalAirport?->code }}</td>
                                <td>{{ $booking->total_passengers }}</td>
                                <td>Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <div class="space-y-1">
                                        @include('admin.partials.status-badge', ['status' => $booking->status])
                                        @include('admin.partials.status-badge', ['status' => $latestPayment?->payment_status])
                                    </div>
                                </td>
                                <td>{{ $booking->expired_at?->format('d M Y H:i') ?: '-' }}</td>
                                <td><a href="{{ route('admin.bookings.show', $booking) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-slate-500">Data booking belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $bookings->links() }}</div>
        </article>
    </section>
@endsection
