@extends('layouts.admin')

@section('title', 'Detail Passenger | Zannora')
@section('page-title', 'Passenger Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <p class="text-sm text-slate-500">Full Name</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Gender</p>
                    <p class="font-semibold text-slate-800">{{ ucfirst($passenger->gender) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Birth Date</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->birth_date?->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">User Owner</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->user?->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Identity Number</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->identity_number ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Passport Number</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->passport_number ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Nationality</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->nationality ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Created At</p>
                    <p class="font-semibold text-slate-800">{{ $passenger->created_at?->format('d M Y H:i') }}</p>
                </div>
            </div>
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Booking History</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Flight</th>
                            <th>Seat</th>
                            <th>Ticket</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($passenger->bookingDetails as $detail)
                            <tr>
                                <td>{{ $detail->booking?->booking_code }}</td>
                                <td>{{ $detail->booking?->user?->name }}</td>
                                <td>{{ $detail->booking?->flight?->flight_number }}</td>
                                <td>{{ $detail->seat?->seat_number }} ({{ ucfirst($detail->seat?->class ?? '-') }})</td>
                                <td>{{ $detail->ticket?->id ? ($detail->ticket_number ?: '-') : '-' }}</td>
                                <td>
                                    @if ($detail->booking)
                                        <a class="admin-btn-secondary" href="{{ route('admin.bookings.show', $detail->booking) }}">Booking</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Belum pernah ikut booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
