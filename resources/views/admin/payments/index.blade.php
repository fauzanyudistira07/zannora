@extends('layouts.admin')

@section('title', 'Admin Payments | Zannora')
@section('page-title', 'Payments')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="admin-label" for="search">Search Booking/User/Method/Transaction</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Booking code, user, method">
            </div>
            <div>
                <label class="admin-label" for="status">Payment Status</label>
                <select id="status" name="status" class="admin-field">
                    <option value="">Semua status</option>
                    <option value="pending" @selected($status === 'pending')>Pending</option>
                    <option value="paid" @selected($status === 'paid')>Paid</option>
                    <option value="failed" @selected($status === 'failed')>Failed</option>
                    <option value="refunded" @selected($status === 'refunded')>Refunded</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Booking Code</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->booking?->booking_code }}</td>
                                <td>{{ $payment->booking?->user?->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $payment->payment_status])</td>
                                <td>{{ $payment->proof_file ? 'Ada' : 'Tidak ada' }}</td>
                                <td><a class="admin-btn-secondary" href="{{ route('admin.payments.show', $payment) }}">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-slate-500">Data payment belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payments->links() }}</div>
        </article>
    </section>
@endsection
