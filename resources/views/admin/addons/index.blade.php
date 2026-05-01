@extends('layouts.admin')

@section('title', 'Admin Add-Ons | Zannora')
@section('page-title', 'Add-Ons')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card space-y-5">
            <div class="admin-section-head">
                <div>
                    <p class="admin-section-kicker">Ancillary Services</p>
                    <h2 class="admin-section-title">Addon Monitoring</h2>
                    <p class="admin-section-copy">Pantau add-on bagasi, layanan prioritas, dan asuransi untuk setiap booking.</p>
                </div>
                <span class="admin-chip">{{ $addons->total() }} add-on</span>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="admin-label" for="search">Search</label>
                    <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Booking, user, passenger">
                </div>
                <div>
                    <label class="admin-label" for="type">Addon Type</label>
                    <select id="type" name="type" class="admin-field">
                        <option value="">Semua tipe</option>
                        <option value="baggage" @selected($type === 'baggage')>Baggage</option>
                        <option value="priority" @selected($type === 'priority')>Priority</option>
                        <option value="service" @selected($type === 'service')>Service</option>
                        <option value="insurance" @selected($type === 'insurance')>Insurance</option>
                    </select>
                </div>
                <div>
                    <label class="admin-label" for="status">Status</label>
                    <select id="status" name="status" class="admin-field">
                        <option value="">Semua status</option>
                        <option value="selected" @selected($status === 'selected')>Selected</option>
                        <option value="paid" @selected($status === 'paid')>Paid</option>
                        <option value="cancelled" @selected($status === 'cancelled')>Cancelled</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="admin-btn-primary">Filter</button>
                    <a href="{{ route('admin.addons.index') }}" class="admin-btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>User</th>
                            <th>Passenger</th>
                            <th>Addon</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($addons as $addon)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <a href="{{ route('admin.bookings.show', $addon->booking) }}" class="font-semibold text-[#0f3f78]">
                                        {{ $addon->booking?->booking_code }}
                                    </a>
                                </td>
                                <td>{{ $addon->booking?->user?->name ?: '-' }}</td>
                                <td>{{ $addon->bookingDetail?->passenger?->full_name ?: 'Booking-level' }}</td>
                                <td>{{ $addon->addon_name }}</td>
                                <td>{{ ucfirst($addon->addon_type) }}</td>
                                <td>{{ $addon->quantity }}</td>
                                <td class="whitespace-nowrap">Rp{{ number_format((float) $addon->total_price, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $addon->status])</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.addons.status', $addon) }}" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="admin-field min-w-[130px]">
                                            <option value="selected" @selected($addon->status === 'selected')>Selected</option>
                                            <option value="paid" @selected($addon->status === 'paid')>Paid</option>
                                            <option value="cancelled" @selected($addon->status === 'cancelled')>Cancelled</option>
                                        </select>
                                        <button type="submit" class="admin-btn-secondary">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-slate-500">Data add-on belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $addons->links() }}
            </div>
        </article>
    </section>
@endsection
