@extends('layouts.admin')

@section('title', 'Manager Dashboard | Zannora')
@section('page-title', 'Management Dashboard')

@section('content')
    <section class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="admin-kpi-card">
                <p class="admin-info-label">Revenue Total</p>
                <p class="mt-3 text-3xl font-bold text-emerald-700">Rp{{ number_format((float) $stats['revenue_total'], 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-slate-500">Akumulasi pembayaran paid di seluruh periode data.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Revenue This Month</p>
                <p class="mt-3 text-3xl font-bold text-emerald-700">Rp{{ number_format((float) $stats['revenue_this_month'], 0, ',', '.') }}</p>
                <p class="mt-2 text-sm text-slate-500">Performa pendapatan berjalan bulan ini.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Total Bookings</p>
                <p class="mt-3 text-3xl font-bold text-slate-800">{{ $stats['total_bookings'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Jumlah booking yang sudah tercatat di sistem.</p>
            </article>
            <article class="admin-kpi-card">
                <p class="admin-info-label">Open Support Inbox</p>
                <p class="mt-3 text-3xl font-bold text-[#0f3f78]">{{ $stats['open_contact_messages'] }}</p>
                <p class="mt-2 text-sm text-slate-500">Case support yang masih menunggu penanganan.</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <article class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Team Composition</p>
                        <h2 class="admin-section-title">Role mix</h2>
                    </div>
                </div>
                <div class="mt-4 space-y-3">
                    @php($maxRoleMix = max(collect($roleMix)->max() ?? 0, 1))
                    @foreach ($roleMix as $role => $count)
                        <div class="space-y-2 rounded-2xl border border-slate-200 bg-white/75 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-700">{{ \App\Support\UserRole::label($role) }}</p>
                                <p class="text-sm text-slate-500">{{ $count }}</p>
                            </div>
                            @include('admin.partials.progress-bar', ['percentage' => ($count / $maxRoleMix) * 100])
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="admin-card xl:col-span-2">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Top Performance</p>
                        <h2 class="admin-section-title">Popular routes</h2>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="admin-btn-secondary">Open Reports</a>
                </div>
                <div class="admin-table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Flight</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($popularRoutes as $route)
                                <tr>
                                    <td>{{ $route['route'] }}</td>
                                    <td>{{ $route['flight_number'] }}</td>
                                    <td>{{ $route['total_bookings'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-slate-500">Belum ada data route populer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <article class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Revenue Curve</p>
                        <h2 class="admin-section-title">Monthly revenue</h2>
                    </div>
                </div>
                <div class="mt-4 space-y-3">
                    @php($maxRevenue = max(collect($monthlyRevenue)->max('total') ?? 0, 1))
                    @forelse ($monthlyRevenue as $item)
                        <div class="grid gap-2 sm:grid-cols-[110px_1fr_130px] sm:items-center">
                            <p class="text-sm text-slate-600">{{ $item['month'] }}</p>
                            @include('admin.partials.progress-bar', ['percentage' => ($item['total'] / $maxRevenue) * 100])
                            <p class="text-right text-sm font-semibold text-slate-700">Rp{{ number_format((float) $item['total'], 0, ',', '.') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data revenue bulanan.</p>
                    @endforelse
                </div>
            </article>

            <article class="admin-card">
                <div class="admin-section-head">
                    <div>
                        <p class="admin-section-kicker">Support Watch</p>
                        <h2 class="admin-section-title">Open support cases</h2>
                    </div>
                    <a href="{{ route('admin.contact-messages.index') }}" class="admin-btn-secondary">Open Inbox</a>
                </div>
                <div class="admin-table-wrap mt-4">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>PIC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($openMessages as $message)
                                <tr>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>@include('admin.partials.status-badge', ['status' => $message->status])</td>
                                    <td>{{ $message->assignedUser?->name ?: 'Unassigned' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-slate-500">Tidak ada case support terbuka.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </section>
@endsection
