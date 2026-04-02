@extends('layouts.admin')

@section('title', 'Admin Users | Zannora')
@section('page-title', 'Users')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-[1fr_auto]">
            <div>
                <label class="admin-label" for="search">Search Name / Email</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Cari nama atau email user">
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Passengers</th>
                            <th>Bookings</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="font-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: '-' }}</td>
                                <td>{{ $user->passengers_count }}</td>
                                <td>{{ $user->bookings_count }}</td>
                                <td>{{ $user->created_at?->format('d M Y') }}</td>
                                <td><a href="{{ route('admin.users.show', $user) }}" class="admin-btn-secondary">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-slate-500">Data user belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </article>
    </section>
@endsection
