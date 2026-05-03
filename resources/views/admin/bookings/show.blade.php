@extends('layouts.admin')

@section('title', 'Booking Detail | Zannora')
@section('page-title', 'Booking Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Booking Code</p><p class="font-semibold text-slate-800">{{ $booking->booking_code }}</p></div>
                <div><p class="text-sm text-slate-500">User</p><p class="font-semibold text-slate-800">{{ $booking->user?->name }} ({{ $booking->user?->email }})</p></div>
                <div><p class="text-sm text-slate-500">Flight</p><p class="font-semibold text-slate-800">{{ $booking->flight?->flight_number }} - {{ $booking->flight?->airline?->name }}</p></div>
                <div><p class="text-sm text-slate-500">Route</p><p class="font-semibold text-slate-800">{{ $booking->flight?->departureAirport?->code }} -> {{ $booking->flight?->arrivalAirport?->code }}</p></div>
                <div><p class="text-sm text-slate-500">Total Passenger</p><p class="font-semibold text-slate-800">{{ $booking->total_passengers }}</p></div>
                <div><p class="text-sm text-slate-500">Total Price</p><p class="font-semibold text-slate-800">Rp{{ number_format((float) $booking->total_price, 0, ',', '.') }}</p></div>
                <div><p class="text-sm text-slate-500">Status</p>@include('admin.partials.status-badge', ['status' => $booking->status])</div>
                <div><p class="text-sm text-slate-500">Expired At</p><p class="font-semibold text-slate-800">{{ $booking->expired_at?->format('d M Y H:i') ?: '-' }}</p></div>
            </div>

            @if (auth()->user()->isAdmin() || auth()->user()->isStaff())
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="admin-field">
                            <option value="pending" @selected($booking->status === 'pending')>Pending</option>
                            <option value="confirmed" @selected($booking->status === 'confirmed')>Confirmed</option>
                            <option value="cancelled" @selected($booking->status === 'cancelled')>Cancelled</option>
                            <option value="completed" @selected($booking->status === 'completed')>Completed</option>
                        </select>
                        <button class="admin-btn-primary" type="submit">Update Status</button>
                    </form>

                    @if ($booking->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" onsubmit="return confirm('Batalkan booking ini?')">
                            @csrf
                            <button class="admin-btn-secondary" type="submit">Cancel Booking</button>
                        </form>
                    @endif
                </div>
            @endif
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Passenger & Seat</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Passenger</th>
                            <th>Seat</th>
                            <th>Price</th>
                            <th>Ticket Number</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($booking->details as $detail)
                            <tr>
                                <td>{{ $detail->passenger?->full_name }}</td>
                                <td>{{ $detail->seat?->seat_number }} ({{ ucfirst($detail->seat?->class ?? '-') }})</td>
                                <td>Rp{{ number_format((float) $detail->price, 0, ',', '.') }}</td>
                                <td>{{ $detail->ticket_number ?: '-' }}</td>
                                <td>
                                    @if ($detail->ticket)
                                        <a href="{{ route('admin.tickets.show', $detail->ticket) }}" class="admin-btn-secondary">Lihat Ticket</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-slate-500">Detail booking kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Payment List</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($booking->payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $payment->payment_status])</td>
                                <td>{{ $payment->proof_file ? 'Ada' : 'Tidak ada' }}</td>
                                <td><a href="{{ route('admin.payments.show', $payment) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Belum ada payment.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
