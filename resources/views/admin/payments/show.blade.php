@extends('layouts.admin')

@section('title', 'Payment Detail | Zannora')
@section('page-title', 'Payment Detail')

@section('content')
    <section class="space-y-6">
        <article class="admin-card">
            <div class="grid gap-4 md:grid-cols-4">
                <div><p class="text-sm text-slate-500">Payment ID</p><p class="font-semibold text-slate-800">{{ $payment->id }}</p></div>
                <div><p class="text-sm text-slate-500">Booking</p><p class="font-semibold text-slate-800">{{ $payment->booking?->booking_code }}</p></div>
                <div><p class="text-sm text-slate-500">User</p><p class="font-semibold text-slate-800">{{ $payment->booking?->user?->name }}</p></div>
                <div><p class="text-sm text-slate-500">Method</p><p class="font-semibold text-slate-800">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p></div>
                <div><p class="text-sm text-slate-500">Amount</p><p class="font-semibold text-slate-800">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</p></div>
                <div><p class="text-sm text-slate-500">Status</p>@include('admin.partials.status-badge', ['status' => $payment->payment_status])</div>
                <div><p class="text-sm text-slate-500">Transaction Code</p><p class="font-semibold text-slate-800">{{ $payment->transaction_code ?: '-' }}</p></div>
                <div><p class="text-sm text-slate-500">Paid At</p><p class="font-semibold text-slate-800">{{ $payment->paid_at?->format('d M Y H:i') ?: '-' }}</p></div>
            </div>

            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="admin-card-soft">
                    <p class="text-sm text-slate-500">Booking Info</p>
                    <p class="font-semibold text-slate-800">{{ $payment->booking?->flight?->flight_number }} - {{ $payment->booking?->flight?->airline?->name }}</p>
                    <p class="text-sm text-slate-600">{{ $payment->booking?->flight?->departureAirport?->code }} -> {{ $payment->booking?->flight?->arrivalAirport?->code }}</p>
                </div>
                <div class="admin-card-soft">
                    <p class="text-sm text-slate-500">Proof File</p>
                    @if ($payment->proof_file)
                        @php($proofUrl = str_starts_with($payment->proof_file, 'http') ? $payment->proof_file : asset('storage/'.$payment->proof_file))
                        <a href="{{ $proofUrl }}" target="_blank" class="text-sm font-semibold text-[#0f3f78] underline">Lihat bukti pembayaran</a>
                    @else
                        <p class="text-sm text-slate-600">Bukti pembayaran belum diupload.</p>
                    @endif
                </div>
            </div>

            @if ($payment->payment_status === 'pending' && (auth()->user()->isAdmin() || auth()->user()->isStaff()))
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="flex flex-wrap items-center gap-2">
                        @csrf
                        <input name="transaction_code" class="admin-field" placeholder="Transaction Code" required>
                        <button class="admin-btn-primary" type="submit">Verifikasi Payment</button>
                    </form>

                    <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="flex flex-wrap items-center gap-2" onsubmit="return confirm('Tolak payment ini?')">
                        @csrf
                        <input name="transaction_code" class="admin-field" placeholder="Optional code">
                        <button class="admin-btn-secondary" type="submit">Tolak Payment</button>
                    </form>
                </div>
            @endif
        </article>

        <article class="admin-card">
            <h2 class="font-heading text-xl font-bold text-[#0f3f78]">Passenger dalam Booking</h2>
            <div class="admin-table-wrap mt-4">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Passenger</th>
                            <th>Seat</th>
                            <th>Ticket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payment->booking?->details ?? [] as $detail)
                            <tr>
                                <td>{{ $detail->passenger?->full_name }}</td>
                                <td>{{ $detail->seat?->seat_number }} ({{ ucfirst($detail->seat?->class ?? '-') }})</td>
                                <td>
                                    @if ($detail->ticket)
                                        <a href="{{ route('admin.tickets.show', $detail->ticket) }}" class="admin-btn-secondary">Lihat Ticket</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-slate-500">Tidak ada detail passenger.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection
