@extends('layouts.admin')

@section('title', 'Admin Reports | Zannora')
@section('page-title', 'Reports')

@section('content')
    <section class="space-y-6">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div>
                <label class="admin-label" for="date_from">Date From</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}" class="admin-field">
            </div>
            <div>
                <label class="admin-label" for="date_to">Date To</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}" class="admin-field">
            </div>
            <div>
                <label class="admin-label" for="airline_id">Airline</label>
                <select id="airline_id" name="airline_id" class="admin-field">
                    <option value="">Semua airline</option>
                    @foreach ($airlines as $airline)
                        <option value="{{ $airline->id }}" @selected((int) $filters['airline_id'] === (int) $airline->id)>{{ $airline->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="route">Route</label>
                <input id="route" name="route" value="{{ $filters['route'] }}" class="admin-field" placeholder="CGK / DPS">
            </div>
            <div>
                <label class="admin-label" for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status" class="admin-field">
                    <option value="">Semua</option>
                    <option value="pending" @selected($filters['payment_status'] === 'pending')>Pending</option>
                    <option value="paid" @selected($filters['payment_status'] === 'paid')>Paid</option>
                    <option value="failed" @selected($filters['payment_status'] === 'failed')>Failed</option>
                    <option value="refunded" @selected($filters['payment_status'] === 'refunded')>Refunded</option>
                </select>
            </div>
            <div>
                <label class="admin-label" for="booking_status">Booking Status</label>
                <select id="booking_status" name="booking_status" class="admin-field">
                    <option value="">Semua</option>
                    <option value="pending" @selected($filters['booking_status'] === 'pending')>Pending</option>
                    <option value="confirmed" @selected($filters['booking_status'] === 'confirmed')>Confirmed</option>
                    <option value="cancelled" @selected($filters['booking_status'] === 'cancelled')>Cancelled</option>
                    <option value="completed" @selected($filters['booking_status'] === 'completed')>Completed</option>
                </select>
            </div>
            <div class="flex flex-wrap items-end gap-2 md:col-span-2 lg:col-span-3 xl:col-span-4">
                <button class="admin-btn-primary" type="submit">Generate</button>
                <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="admin-btn-secondary">Export CSV</a>
            </div>
        </form>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Total Booking</p><p class="text-2xl font-bold text-slate-800">{{ $summary['total_bookings'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Payment Paid</p><p class="text-2xl font-bold text-emerald-700">{{ $summary['total_paid_payments'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Cancelled Booking</p><p class="text-2xl font-bold text-red-600">{{ $summary['total_cancelled_bookings'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Flight Aktif</p><p class="text-2xl font-bold text-slate-800">{{ $summary['total_active_flights'] }}</p></article>
            <article class="admin-card-soft"><p class="text-sm text-slate-500">Pendapatan</p><p class="text-2xl font-bold text-emerald-700">Rp{{ number_format((float) $summary['revenue_total'], 0, ',', '.') }}</p></article>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Popular Routes</h2>
                <div class="admin-table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Flight Number</th>
                                <th>Total Bookings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($summary['popular_routes'] as $route)
                                <tr>
                                    <td>{{ $route['route'] }}</td>
                                    <td>{{ $route['flight_number'] }}</td>
                                    <td>{{ $route['total_bookings'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-slate-500">Belum ada data route.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="admin-card">
                <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Revenue Per Month</h2>
                <div class="space-y-3 mt-4">
                    @php($maxRevenue = max(collect($summary['monthly_revenue'])->max('total') ?? 0, 1))
                    @forelse ($summary['monthly_revenue'] as $item)
                        <div class="grid gap-2 sm:grid-cols-[100px_1fr_120px] sm:items-center">
                            <p class="text-sm text-slate-600">{{ $item['month'] }}</p>
                            @include('admin.partials.progress-bar', ['percentage' => ($item['total'] / $maxRevenue) * 100])
                            <p class="text-right text-sm font-semibold text-slate-700">Rp{{ number_format((float) $item['total'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data revenue bulanan.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection
