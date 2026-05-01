@extends('layouts.admin')

@section('title', 'Seat Detail | Zannora')
@section('page-title', 'Seat Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Airline</p><p class="font-semibold text-slate-800">{{ $seat->airplane?->airline?->name }}</p></div>
                <div><p class="text-sm text-slate-500">Airplane</p><p class="font-semibold text-slate-800">{{ $seat->airplane?->model }} ({{ $seat->airplane?->registration_number }})</p></div>
                <div><p class="text-sm text-slate-500">Seat Number</p><p class="font-semibold text-slate-800">{{ $seat->seat_number }}</p></div>
                <div><p class="text-sm text-slate-500">Class</p><p class="font-semibold text-slate-800">{{ ucfirst($seat->class) }}</p></div>
            </div>
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Booking Usage</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Passenger</th>
                            <th>Flight</th>
                            <th>Status</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($seat->bookingDetails as $detail)
                            <tr>
                                <td>
                                    @if ($detail->booking)
                                        <a href="{{ route('admin.bookings.show', $detail->booking) }}" class="font-semibold text-[#0f3f78]">{{ $detail->booking->booking_code }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $detail->booking?->user?->name ?: '-' }}</td>
                                <td>{{ $detail->passenger?->full_name ?: '-' }}</td>
                                <td>{{ $detail->booking?->flight?->flight_number ?: '-' }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $detail->booking?->status])</td>
                                <td>{{ $detail->ticket?->id ? ($detail->ticket_number ?: '-') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-slate-500">Seat belum pernah dipakai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
