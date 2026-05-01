@extends('layouts.admin')

@section('title', 'Admin Passengers | Zannora')
@section('page-title', 'Passengers')

@section('content')
    <section class="space-y-5">
        <form method="GET" class="admin-card grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label for="search" class="admin-label">Search Passenger</label>
                <input id="search" name="search" value="{{ $search }}" class="admin-field" placeholder="Nama passenger">
            </div>
            <div>
                <label for="user_id" class="admin-label">Filter User</label>
                <select id="user_id" name="user_id" class="admin-field">
                    <option value="">Semua user</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected((int) $userId === (int) $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button class="admin-btn-primary" type="submit">Filter</button>
                <a href="{{ route('admin.passengers.index') }}" class="admin-btn-secondary">Reset</a>
            </div>
        </form>

        <article class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>User</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Nationality</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($passengers as $passenger)
                            <tr>
                                <td>{{ ($passengers->firstItem() ?? 1) + $loop->index }}</td>
                                <td>{{ $passenger->user?->name }}</td>
                                <td class="font-semibold">{{ $passenger->full_name }}</td>
                                <td>{{ ucfirst($passenger->gender) }}</td>
                                <td>{{ $passenger->birth_date?->format('d M Y') }}</td>
                                <td>{{ $passenger->nationality ?: '-' }}</td>
                                <td>{{ $passenger->created_at?->format('d M Y') }}</td>
                                <td><a class="admin-btn-secondary" href="{{ route('admin.passengers.show', $passenger) }}">Detail</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-slate-500">Data passenger belum tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $passengers->links() }}</div>
        </article>
    </section>
@endsection
