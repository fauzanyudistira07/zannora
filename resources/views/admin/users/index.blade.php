@extends('layouts.admin')

@section('title', 'Admin Users | Zannora')
@section('page-title', 'Users')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-2 xl:grid-cols-[1.3fr_220px_220px_auto]">
            <div>
                <label class="admin-label" for="search">Search Name / Email</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Cari nama, email, telepon, atau employee ID">
            </div>
            <div>
                <label class="admin-label" for="role">Role</label>
                <select id="role" name="role" class="admin-field">
                    <option value="">Semua role</option>
                    @foreach ($roleOptions as $roleKey => $roleOption)
                        <option value="{{ $roleKey }}" @selected($role === $roleKey)>{{ $roleOption['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin-label" for="department">Department</label>
                <input id="department" name="department" value="{{ $department }}" class="admin-field" placeholder="Ops, Finance, Revenue">
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">Reset</a>
                @if (auth()->user()->canManageUsers())
                    <a href="{{ route('admin.users.create') }}" class="admin-btn-secondary">Tambah User</a>
                @endif
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Passengers</th>
                            <th>Bookings</th>
                            <th>Registered</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ ($users->firstItem() ?? 1) + $loop->index }}</td>
                                <td class="font-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: '-' }}</td>
                                <td>{{ $user->roleLabel() }}</td>
                                <td>{{ $user->department ?: '-' }}</td>
                                <td>{{ $user->passengers_count }}</td>
                                <td>{{ $user->bookings_count }}</td>
                                <td>{{ $user->created_at?->format('d M Y') }}</td>
                                <td class="space-x-1 whitespace-nowrap">
                                    <a href="{{ route('admin.users.show', $user) }}" class="admin-btn-secondary">Detail</a>
                                    @if (auth()->user()->canManageUsers())
                                        <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn-secondary">Edit</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-slate-500">Data user belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </article>
    </section>
@endsection
